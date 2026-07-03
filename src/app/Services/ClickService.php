<?php

namespace App\Services;

use App\Models\Link;
use App\Repositories\Contracts\ClickRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClickService
{
    protected ClickRepositoryInterface $repository;

    public function __construct(ClickRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function trackClick(Link $link, Request $request): void
    {
        $data = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'country' => $this->getCountry($request->ip()),
            'city' => $this->getCity($request->ip()),
            'clicked_at' => now(),
        ];

        $this->repository->create($link, $data);
    }

    public function getLinkClicks(Link $link, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->repository->getClicksByLink($link, $filters);
    }

    public function getClickCount(Link $link): int
    {
        return $this->repository->countByLink($link);
    }

    public function getDailyStats(Link $link, int $days = 7): array
    {
        return $this->repository->getDailyStats($link, $days);
    }

    protected function getCountry(?string $ip): ?string
    {
        //TODO прикрутить пакет для определения страны
        return null;
    }

    protected function getCity(?string $ip): ?string
    {
        return null;
    }
}
