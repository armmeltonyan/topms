<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TopMsService
{
    public function buy(string $serverName,string $rounds)
    {
        $server = '◄ AKIMOFF YouTube ► 45.136.204.158:27015';
        $response = Http::withoutRedirecting()->get('http://127.0.0.1:5000/api?server='.urlencode($server).'&rounds='.$rounds);

        return $response->json();
    }
}
