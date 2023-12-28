<?php

namespace App\Console\Commands;

use App\Jobs\duskJob;
use App\Models\Iteration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class AddProcessToQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-process-to-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add process to queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server = '◄ AKIMOFF YouTube ► 45.136.204.158:27015';
        $response = Http::withoutRedirecting()->get('http://127.0.0.1:5000/api?server='.urlencode($server));

// Assuming the Python API returns JSON, you can decode it
        $data = $response->json();
        return $data;

        $processes = Iteration::where('processed',0)->where('process_at','>=',Carbon::now())->where('monitoring','Topms')->get();
        print_r($processes);
        foreach ($processes as $process){
            duskJob::dispatch($process->monitoring,$process->id);
        }

        return 'success';
    }
}
