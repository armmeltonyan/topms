<?php

namespace App\Http\Controllers;

use App\Jobs\duskJob;
use App\Models\Iteration;
use App\Services\TopMsServiceV2;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $buyHistory = Iteration::where('user_id', auth()->id())->get();
        return view('home',compact('buyHistory'));
    }

    public function buy(Request $request)
    {
        $dates = explode('-',$request->datetimes);
        $startDate = Carbon::parse(str_replace(' ','',$dates[0]));
        $endDate = Carbon::parse(str_replace(' ','',$dates[1]));
        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
//        dd($request->all());
//        foreach ($request->monitoring as $monitoring){
//
//        }
//        dd($request->all());
        $insertArray = [];
        foreach ($request->monitorings as $monitoring => $round){
            if(isset($round['selected'])) {
                foreach ($period as $date){
                    $combinedDateTime = Carbon::parse($date->toDateString() . ' ' . $request->time);
                    $insertArray[] = ['user_id'=>auth()->id(),'monitoring'=>$monitoring,'iteration'=>$round['round'],'process_at'=>$combinedDateTime,'server_name'=>$request->serverName,'time'=>$request->time];
                }
            }
        }

        Iteration::insert($insertArray);

        return back();
    }


}
