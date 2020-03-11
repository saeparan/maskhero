<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APIController extends Controller
{
    public function getStores($lat, $lng)
    {
        $stores = DB::table('stores')
            ->select(['code', 'addr', 'name', 'remain_stat', 'stock_at', 'update_time', 'type', 'lat', 'lng'])
            ->selectRaw('
                 (
                  6373 * acos (
                  cos ( radians( ? ) )
                  * cos( radians( X(coordinate) ) )
                  * cos( radians( Y(coordinate) ) - radians( ? ) )
                  + sin ( radians( ? ) )
                  * sin( radians( X(coordinate) ) )
                )
            ) AS distance
            ', [$lat, $lng, $lat])
            ->having('distance', '<', 0.8)
            ->get()
        ;

        return response()->json($stores);
    }

    public function getStore($code)
    {
        $stores = DB::table('stores')
            ->select(['code', 'addr', 'name', 'remain_stat', 'stock_at', 'update_time', 'type', 'lat', 'lng'])
            ->where('code', '=', $code)
            ->first()
        ;

        $stores->stock_at = date('m월 d일 H시 i분', strtotime($stores->stock_at));
        $stores->update_time = date('m월 d일 H시 i분', strtotime($stores->update_time));
        return response()->json($stores);
    }
}
