<?php

namespace App\Console\Commands;

use App\Models\Iteration;
use App\Services\TopMsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RunDuskTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dusk:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TopMsService $topMsService)
    {
        $iterations = Iteration::where('processed',0)->get();
        Log::error($iterations);
        foreach ($iterations as $iteration) {
            Log::error('db date:'. Carbon::parse($iteration->process_at)->toDateString());
            Log::error('now date:'. Carbon::now()->toDateString());
            if (Carbon::parse($iteration->process_at)->toDateString() == Carbon::now()->toDateString()){
                Log::error('now time:'. Carbon::now());
                Log::error('db time:'. Carbon::parse($iteration->time));
                if (Carbon::now()->greaterThan(Carbon::parse($iteration->time))){
                    echo 'processing';
                    echo $iteration->server_name;
                    for ($x = 1; $x <= $iteration->iteration; $x++) {
                        $response = $topMsService->buy($iteration->server_name,1,$iteration->monitoring);
                        Log::error("response: ".$response);
                        sleep(1);
                    }

                    if ($response['success']) {
                        Iteration::whereId($iteration->id)->update(['processed'=>1,'response_msg'=>$response['msg']]);
                    }else{
                        Iteration::whereId($iteration->id)->update(['response_msg'=>$response['msg']]);
                    }
                }
            }
        }
    }
}
