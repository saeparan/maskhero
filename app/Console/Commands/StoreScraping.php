<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreScraping extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:scraping';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '가게와 재고들 가져오기';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $areas = DB::table('area')->get();
        foreach ($areas as $area) {
            $client = new \GuzzleHttp\Client();
            $address_search = $area->name2 . ' ' . $area->name1;
            $response = $client->request('GET', 'https://8oi9s0nnth.apigw.ntruss.com/corona19-masks/v1/storesByAddr/json?address=' . urlencode($address_search));
            $json = json_decode($response->getBody()->getContents());

            foreach ($json->stores as $store) {
                if( !isset($store->remain_stat) ) {
                    $store->remain_stat = null;
                }
                if( !isset($store->stock_at) ) {
                    $store->stock_at = null;
                }
                if( $store->lng=='' || $store->lat=='' ) {
                    $store->lng = 0;
                    $store->lat = 0;
                }
                DB::table('stores')
                    ->updateOrInsert(
                        ['code' => $store->code],
                        [
                            'addr' => $store->addr,
                            'area_id' => $area->id,
                            'name' => $store->name,
                            'remain_stat' => $store->remain_stat,
                            'created_at' => $store->created_at,
                            'stock_at' => $store->stock_at,
                            'type' => $store->type,
                            'coordinate' => DB::raw("(GeomFromText('POINT({$store->lat} {$store->lng})'))"),
                            'update_time' => date('Y-m-d H:i:00')
                        ]
                    );
            }

            Log::info('store scraping: ' . $address_search);
            sleep(2);
        }
    }
}
