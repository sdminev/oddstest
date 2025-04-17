<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StartReverbServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reverb:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel Reverb WebSocket server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Laravel Reverb server...');
        
        // Run the reverb:start command
        Artisan::call('reverb:start', [
        ]);
        
        $this->info(Artisan::output());
        
        return Command::SUCCESS;
    }
}
