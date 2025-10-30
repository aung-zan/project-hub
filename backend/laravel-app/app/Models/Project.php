<?php

namespace App\Models;

use App\Enum\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'created_at' => 'datetime:Y-m-d H:i:s',
            'updated_at' => 'datetime:Y-m-d H:i:s',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include projects that match with given search.
     */
    #[Scope]
    protected function searchWith(Builder $query, string $search): void
    {
        $query->where(function (Builder $query) use ($search) {
            $query->orWhere('status', $search)
                ->orWhere('description', 'LIKE', "%$search%")
                ->orWhere('name', 'LIKE', "%$search%");
        });
    }

    /**
     * Scope a query to sort projects that match with given search.
     */
    #[Scope]
    protected function orderWith(Builder $query, string $search): void
    {
        $query->orderByRaw("Case
            When status = '$search' Then 1
            Else 2
        End ASC");
    }
}
