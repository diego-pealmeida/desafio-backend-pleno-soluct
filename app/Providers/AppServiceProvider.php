<?php

namespace App\Providers;

use App\Repositories as R;
use App\Services as S;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        /* Repositories */
        R\Auth\Repository::class => R\Auth\AuthRepository::class,
        R\Tasks\Repository::class => R\Tasks\TaskRepository::class,
        R\TaskHistory\Repository::class => R\TaskHistory\TaskHistoryRepository::class,
        R\User\Repository::class => R\User\UserRepository::class,

        /* Services */
        S\Tasks\Service::class => S\Tasks\TaskService::class,
        S\Auth\Service::class => S\Auth\AuthService::class
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer')
            );
        });
    }
}
