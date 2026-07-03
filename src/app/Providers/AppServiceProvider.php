<?php

namespace App\Providers;

use App\Models\Link;
use App\Policies\LinkPolicy;
use App\Repositories\ClickRepository;
use App\Repositories\Contracts\ClickRepositoryInterface;
use App\Repositories\Contracts\LinkRepositoryInterface;
use App\Repositories\Eloquent\ClickRepository as EloquentClickRepository;
use App\Repositories\Eloquent\LinkRepository as EloquentLinkRepository;
use App\Services\ClickService;
use App\Services\LinkService;
use App\Services\UrlShortenerService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Регистрация репозиториев
        $this->app->bind(
            LinkRepositoryInterface::class,
            EloquentLinkRepository::class
        );

        $this->app->bind(
            ClickRepositoryInterface::class,
            EloquentClickRepository::class
        );

        // Регистрация сервисов
        $this->app->singleton(UrlShortenerService::class, function () {
            return new UrlShortenerService();
        });

        $this->app->singleton(LinkService::class, function ($app) {
            return new LinkService(
                $app->make(LinkRepositoryInterface::class),
                $app->make(UrlShortenerService::class)
            );
        });

        $this->app->singleton(ClickService::class, function ($app) {
            return new ClickService(
                $app->make(ClickRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        Gate::policy(Link::class, LinkPolicy::class);
    }
}
