<?php
/**
 * Created by PhpStorm.
 * User: tegaralaga
 * Date: 2019-09-14
 * Time: 23:10
 */

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\VenueModel;
use App\Models\VenueTypeModel;
use App\Traits\VenetTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\RedisHelper as RH;
use Illuminate\Validation\Rule;
use App\Helpers\VenueHelper as VH;

class VenueController extends Controller
{
    use VenetTrait;

    public function __construct()
    {
    }

    public function venue_info(Request $request, $id) {
        $venue = null;
        $parent = $request->parent;
        if (blank($parent)) {
            $parent = false;
        } else {
            $parent = strtolower($parent);
            if ($parent == 'true'){
                $parent = true;
            } else {
                $parent = false;
            }
        }
        $venue = VH::GetVenueData($id, false, false, $parent);
        if ($venue == null) {
            $this->code = Response::HTTP_NOT_FOUND;
            $this->message = 'Not Found';
        } else {
            $this->success = true;
            $this->data = $venue;
        }
        return $this->json();
    }

    public function create(Request $request) {
        /**
         * set basic rule
         * parent is not required, but when supplied, need to check at tbl_venue
         * location_type, predefined
         * name max 200 char
         * description is not required, but when supplied it should be no more than 200 char
         * capacity is not required, but when supplied, it should be numeric value
         */
        $rules = [
            'parent' => 'exists:tbl_venue,ven_id',
            'location_type' => [
                'required',
                Rule::in(['INDOOR', 'OUTDOOR', 'SEMI']),
            ],
            'name' => 'required|max:200',
            'description' => 'max:200',
            'capacity' => 'numeric',
        ];
        /**
         * checking venue type, if numeric, should check at tbl_venue_type
         * else check predefined venue type
         */
        $venue_type = $request->input('venue_type');
        if (is_numeric($venue_type)) {
            $rules['venue_type'] = 'required|exists:tbl_venue_type,vty_id';
        } else {
            $venue_types_data = $this->get_venue_types();
            $rules['venue_type'] = ['required', Rule::in($venue_types_data)];
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $this->message = $validator->errors();
        } else {
            $parent_id = (blank($request->parent) ? 0 : $request->parent);
            $rules = [];
            $kelurahan = $request->kelurahan;
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $address = $request->address;
            /**
             * check if parent is supplied
             * if not
             * kelurahan need to check existing data at tbl_kelurahan
             * latitude float/decimal/double you name it, between -90 till 90
             * longitude float/decimal/double you name it, between -180 till 180
             * address no more than 200 char
             * if parent supplied
             * check value for kelurahan, latitude, longitude, address, if supplied, follow the same rule
             */
            if ($parent_id == 0) {
                $rules = [
                    'kelurahan' => 'required|exists:tbl_kelurahan,kel_id',
                    'latitude' => 'required|numeric|between:-90,90',
                    'longitude' => 'required|numeric|between:-180,180',
                    'address' => 'required|max:200',
                ];
            } else {
                if (filled($kelurahan)) {
                    $rules['kelurahan'] = 'required|exists:tbl_kelurahan,kel_id';
                } else {
                    $kelurahan = null;
                }
                if (filled($latitude)) {
                    $rules['latitude'] = 'required|numeric|between:-90,90';
                } else {
                    $latitude = null;
                }
                if (filled($longitude)) {
                    $rules['longitude'] = 'required|numeric|between:-180,180';
                } else {
                    $longitude = null;
                }
                if (filled($address)) {
                    $rules['address'] = 'required|max:200';
                } else {
                    $address = null;
                }
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $this->message = $validator->errors();
            } else {
                /**
                 * checking kelurahan, latitude, longitude, address value
                 * if not supplied, it will inherit from parent value
                 */
                if (($kelurahan == null || $latitude == null || $longitude == null || $address == null) && ($parent_id > 0)) {
                    $parent = VenueModel::select('ven_kel_id', 'ven_coordinate', 'ven_address')->find($parent_id);
                    if ($kelurahan == null) {
                        $kelurahan = $parent->ven_kel_id;
                    }
                    if ($latitude == null) {
                        $latitude = $parent->ven_coordinate->getLat();
                    }
                    if ($longitude == null) {
                        $longitude = $parent->ven_coordinate->getLng();
                    }
                    if ($address == null) {
                        $address = $parent->ven_address;
                    }
                }
                \DB::beginTransaction();
                try {
                    $venue = new VenueModel();
                    $venue->ven_parent = $parent_id;
                    if (!(is_numeric($venue_type))) {
                        $venue_type = VenueTypeModel::select('vty_id')->where('vty_name', $venue_type)->first();
                        $venue_type = $venue_type->vty_id;
                    }
                    $venue->ven_vty_id = $venue_type;
                    $venue->ven_location_type = $request->location_type;
                    $venue->ven_kel_id = $kelurahan;
                    $venue->ven_coordinate = new Point($latitude, $longitude);
                    $venue->ven_capacity = (blank($request->capacity) ? 0 : $request->capacity);
                    $venue->ven_address = $address;
                    $venue->ven_name = $request->name;
                    $venue->ven_description = (blank($request->description) ? null : $request->description);
                    $venue->save();
                    \DB::commit();
                    $this->success = true;
                    // display saved venue
                    $this->data = VH::GetVenueData($venue->ven_id, false, false, false);
                } catch (\Exception $e) {
                    \DB::rollBack();
                    $this->success = false;
                    $this->message = $e->getMessage();
                }
            }
        }
        return $this->json();
    }

    private function get_venue_types() {
        $redis_key = 'venue:types';
        $cache = RH::Get($redis_key);
        $venue_types = [];
        if ($cache == null) {
            $select = VenueTypeModel::select('vty_name')->get();
            if (count($select) > 0) {
                $venue_types = collect($select)->map(function ($venue) {
                    return $venue->vty_name;
                });
                $venue_types = $venue_types->toArray();
            }
            RH::Set($redis_key, json_encode($venue_types), RH::WEEK);
        } else {
            $venue_types = json_decode($cache, true);
        }
        return $venue_types;
    }

}