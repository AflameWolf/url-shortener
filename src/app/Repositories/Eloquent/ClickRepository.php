<?php

namespace App\Repositories\Eloquent;

use App\Models\Click;
use App\Models\Link;
use App\Repositories\Contracts\ClickRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ClickRepository implements ClickRepositoryInterface
{
    public function create(Link $link, array $data): Click
    {
        return Click::create([
            'link_id' => $link->id,
            ...$data
        ]);
    }

    public function getClicksByLink(Link $link, array $filters = []): LengthAwarePaginator
    {
        $query = $link->clicks();

        if (!empty($filters['from_date'])) {
            $query->where('clicked_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->where('clicked_at', '<=', $filters['to_date']);
        }

        return $query->latest('clicked_at')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function countByLink(Link $link): int
    {
        return $link->clicks()->count();
    }

    public function getDailyStats(Link $link, int $days = 7): array
    {
        return DB::table('clicks')
            ->select(DB::raw('DATE(clicked_at) as date, COUNT(*) as count'))
            ->where('link_id', $link->id)
            ->where('clicked_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
