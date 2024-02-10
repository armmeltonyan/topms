<?php

namespace App\Http\Controllers;

use App\Jobs\duskJob;
use App\Models\Iteration;
use App\Services\TopMsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Application;

class DuskController extends Controller
{
    public function test(TopMsService $topMsService)
    {
        return $topMsService->buy('test',1,'testAPI');
    }

}
