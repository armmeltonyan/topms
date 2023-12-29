<?php

namespace App\Console\Commands;

use App\Models\Iteration;
use App\Services\TopMsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

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
        foreach ($iterations as $iteration) {
            if (Carbon::parse($iteration->process_at)->toDateString() == Carbon::now()->toDateString()){
                if (Carbon::now()->greaterThan(Carbon::parse($iteration->time))){
                    echo 'processing';
                    echo $iteration->server_name;
                    for ($x = 1; $x <= $iteration->iteration; $x++) {
                        $response = $topMsService->buy($iteration->server_name,1);
                        sleep(1);
                    }

                    if ($response['response']['success']) {
                        Iteration::whereId($iteration->id)->update(['processed'=>1]);
                    }
                }
            }
        }
    }
}
