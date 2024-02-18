<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TopMsService
{
    public function buy(string $serverName,string $rounds, string $service)
    {
        try {
//            $response = Http::withoutRedirecting()->get('http://89.105.198.151:8001/api?server='.urlencode($serverName).'&rounds='.$rounds.'&service='.$service);
        $response = Http::withoutRedirecting()->get('http://127.0.0.1:5000/api?server='.urlencode($serverName).'&rounds='.$rounds.'&service='.$service);
            Log::error('service: '.$service);
            Log::error('server: '. $serverName);
            Log::error($response->json());
            return $response->json();
        }catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
