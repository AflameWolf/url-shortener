<?php

namespace App\Services;

use App\Models\Link;
use App\Repositories\Contracts\ClickRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $ip = $request->ip();
        if(Cache::has($ip)){
            $countryCity=Cache::get($ip);
        }
        else{
            $countryCity=$this->getCountryAndCity($ip);
        }

        $data = [
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer'),
            'country' => $countryCity['country'] ?? null,
            'city' => $countryCity['city'] ?? null,
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

    protected function getCountryAndCity(?string $ip): ?array
    {
        $url = "https://ipinfo.io/{$ip}/json";
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data && !$data['bogon']) {

            Cache::add($ip,['country' => $data['country'],'city' => $data['city'],] , now()->addDay(1));

            return [
                'country' => $data['country'],
                'city' => $data['city'],
            ];
        }
        return null;
    }
}
