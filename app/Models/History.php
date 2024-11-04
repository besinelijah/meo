<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'birthday',
        'sex',
        'street',
        'brgy',
        'municipality',
        'province',
    ];

    /**
     * Get histories
     * 
     * @param int|null $limit
     */
    public function histories(
        ?int $limit = null,
        bool $paginate = false,
        ?int $page = 1
    ) {
        // Fetch the clients with `clientUser` and `latestLogout` relationships
        $records = Client::whereHas('clientUser')
            ->with([
                'latestLogout', // Eager-load `latestLogout`
                'user'
            ])
            ->get(); // Retrieve all records, without attempting SQL sorting
        
        // Sort by `latestLogout->created_at` in PHP
        $sortedRecords = $records->sortByDesc(fn($client) => $client->latestLogout->created_at ?? null);

        if ($paginate) {
            $perPage = $limit ?? 5;

            // Paginate the sorted collection in PHP
            $currentPageItems = $sortedRecords->slice(($page - 1) * $perPage, $perPage)->values();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $currentPageItems,
                $sortedRecords->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        }

        return $sortedRecords->values();
    }
}
