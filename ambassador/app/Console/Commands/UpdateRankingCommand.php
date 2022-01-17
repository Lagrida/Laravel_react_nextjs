<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class UpdateRankingCommand extends Command
{
    protected $signature = 'update:ranking';

    protected $description = 'Update Redis ranking';

    public function handle()
    {
        $ambassadors = User::ambassadors()->get();

        $bar = $this->output->createProgressBar($ambassadors->count());

        $bar->start();

        $ambassadors->each(function(User $user) use ($bar){
            Redis::zadd('rankings', round($user->revenue, 2), $user->name);
            $bar->advance();
        });
        
        $bar->finish();

        $this->info('');
    }
}
