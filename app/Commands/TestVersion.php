<?php

namespace App\Commands;

use Illuminate\Console\Command;

class TestVersion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the application version';

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
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('App Version: ' . config('app.version'));
        $this->info('Environment: ' . config('app.env'));
    }
}
