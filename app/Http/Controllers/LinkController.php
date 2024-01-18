<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LinkController extends Controller
{
    public function getCoingeckoPrice($coin, $exchange)
    {
        $url = "https://api.coingecko.com/api/v3/coins/".$coin."/tickers";

        $response = file_get_contents($url);

        $price = null;
        if ($response) {
            $json = json_decode($response, true);
//            dd($json);
            if (array_key_exists('tickers', $json)) {
                foreach ($json['tickers'] as $ticker) {
                    if ($ticker['market']['name'] == $exchange && $ticker['target'] == 'USDT') {
                        $price = $ticker['last'];

                        $data = [
                            'success' => true,
                            'data' => $price,
                            'timestamp' => date('Y-m-d H:i:s')
                        ];

                        Cache::put('cgprice_' . $coin . '_' . $exchange, $data, 600);

                        return response()->json($data);
                    }
                }
            }
        }

        if (Cache::has('cgprice_' . $coin . '_' . $exchange)) {
            $data = Cache::get('cgprice_' . $coin . '_' . $exchange);
            return response()->json($data);
        } else {
            abort(403);
        }

    }

    public function getCoingeckoPriceHigherVol($coin, $exchange)
    {
        $url = "https://api.coingecko.com/api/v3/exchanges/".$exchange."/tickers?coin_ids=".$coin;

        $response = file_get_contents($url);

        if ($response) {
            $json = json_decode($response, true);
//            dd($json);/**/
            $maxConvertedVolumeUSD = 0;
            $selectedArray = null;
            foreach ($json['tickers'] as $ticker){
                if (isset($ticker['converted_volume']['usd']) && $ticker['converted_volume']['usd'] > $maxConvertedVolumeUSD) {
                    $maxConvertedVolumeUSD = $ticker['converted_volume']['usd'];
                    $selectedArray = $ticker;
                }
            }

            $data = [
                'success' => true,
                'data' => sprintf('%.8f', floatval($selectedArray['converted_last']['usd'])),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            return response()->json($data);
        }
        abort(403);
    }

    public function getPriceFromDex($chain,$token)
    {
        $url = "https://api.geckoterminal.com/api/v2/networks/".$chain."/pools/".$token;
        $response = file_get_contents($url);
        if ($response) {
            $json = json_decode($response, true);
            $price = number_format($json['data']['attributes']['base_token_price_usd'], 12, '.', ' ');
            $data = [
                'success' => true,
                'data' => $price,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            return response()->json($data);
        }
    }

    public function getPriceFromDexV2($chain,$token)
    {
        $url = "https://api.geckoterminal.com/api/v2/simple/networks/".$chain."/token_price/".$token;
        $response = file_get_contents($url);
        if ($response) {
            $json = json_decode($response, true);
            $price = number_format($json['data']['attributes']['token_prices'][$token], 12, '.', ' ');
            $data = [
                'success' => true,
                'data' => $price,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            return response()->json($data);
        }
    }

    public function getCoingeckoPriceWithTarget($coin, $target)
    {

        $url = "https://api.coingecko.com/api/v3/simple/price?ids=".$coin."&vs_currencies=".$target;

        $response = file_get_contents($url);

        if ($response) {
            $json = json_decode($response, true);
            if (array_key_exists($coin, $json)) {
                $data = [
                    'success' => true,
                    'data' => $json[$coin][$target],
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                Cache::put('cgprice_' . $coin . '_' . $target, $data, 600);

                return response()->json($data);
            }
        }

        if (Cache::has('cgprice_' . $coin . '_' . $target)) {
            $data = Cache::get('cgprice_' . $coin . '_' . $target);
            return response()->json($data);
        } else {
            abort(403);
        }

    }
}
