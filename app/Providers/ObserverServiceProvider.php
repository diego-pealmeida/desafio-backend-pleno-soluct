<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models as M;
use App\Observers as O;

class ObserverServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        M\Task::observe(O\TaskObserver::class);
    }
}
