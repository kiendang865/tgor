<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\GSTRate;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $now = now();
        $gst_rate = GSTRate::find(2);
        $new_rate = $gst_rate->rate+0.01;
        $gst_rate->update([
            'rate' => $new_rate
        ]);
        $this->info('Test:Cron Cummand Run successfully!');
    }
}
