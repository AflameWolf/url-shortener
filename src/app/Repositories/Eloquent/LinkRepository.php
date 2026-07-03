<?php

namespace App\Repositories\Eloquent;

use App\Models\Link;
use App\Models\User;
use App\Repositories\Contracts\LinkRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LinkRepository implements LinkRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Link::query();

        if (!empty($filters['search'])) {
            $query->where('original_url', 'LIKE', "%{$filters['search']}%")
                ->orWhere('short_code', 'LIKE', "%{$filters['search']}%");
        }

        if (!empty($filters['status'])) {
            match($filters['status']) {
                'active' => $query->where('is_active', true)->whereNull('expires_at'),
                'expired' => $query->where('expires_at', '<', now()),
                'inactive' => $query->where('is_active', false),
                default => null,
            };
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function getUserLinks(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Link::where('user_id', $user->id);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('original_url', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('short_code', 'LIKE', "%{$filters['search']}%")
                    ->orWhere('title', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['sort_by'])) {
            $query->orderBy($filters['sort_by'], $filters['sort_order'] ?? 'desc');
        } else {
            $query->latest();
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function findByShortCode(string $shortCode): ?Link
    {
        return Link::where('short_code', $shortCode)->first();
    }

    public function findById(int $id): ?Link
    {
        return Link::find($id);
    }

    public function create(array $data): Link
    {
        return Link::create($data);
    }

    public function update(Link $link, array $data): Link
    {
        $link->update($data);
        return $link->fresh();
    }

    public function delete(Link $link): bool
    {
        return $link->delete();
    }

    public function incrementClicks(Link $link): void
    {
        $link->increment('clicks_count');
    }

    public function getStats(Link $link): array
    {
        $totalClicks = $link->clicks_count;
        $uniqueIps = $link->clicks()->distinct('ip_address')->count();
        $lastClick = $link->clicks()->latest('clicked_at')->first();

        $dailyStats = DB::table('clicks')
            ->select(DB::raw('DATE(clicked_at) as date, COUNT(*) as count'))
            ->where('link_id', $link->id)
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(7)
            ->get()
            ->toArray();

        return [
            'total_clicks' => $totalClicks,
            'unique_visitors' => $uniqueIps,
            'last_click_at' => $lastClick?->clicked_at,
            'daily_stats' => $dailyStats,
            'top_referers' => $this->getTopReferers($link),
            'top_countries' => $this->getTopCountries($link),
        ];
    }

    private function getTopReferers(Link $link): array
    {
        return $link->clicks()
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referer')
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getTopCountries(Link $link): array
    {
        return $link->clicks()
            ->select('country', DB::raw('COUNT(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
