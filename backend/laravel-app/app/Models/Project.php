<?php

namespace App\Models;

use App\Enum\ProjectStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    /**
     * Relationships between project and user.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->withPivot('role')
            ->as('membership');
    }

    /**
     * Relationships between project and project_user (pivot table).
     *
     * @return HasMany
     */
    public function projectUser(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    /**
     * Get the users with specific columns.
     *
     * @return Project
     */
    public function loadUsersWithSpecificColumns(): Project
    {
        return $this->load(['users' => function ($query) {
            $query->select(['users.id', 'users.name', 'users.username', 'users.email']);
        }]);
    }

    /**
     * Check the user is member of the project.
     *
     * @param int $userId
     * @return bool
     */
    public function hasUser(int $userId): bool
    {
        return DB::table('project_user')->where('project_id', $this->id)
            ->where('user_id', $userId)
            ->select('id')
            ->exists();
    }

    /**
     * Scope a query to only include projects that the user member in.
     *
     * @param Builder $query
     * @param int $userId
     * @return void
     */
    #[Scope]
    protected function forUser(Builder $query, int $userId)
    {
        $query->whereHas('projectUser', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }

    /**
     * Scope a query to only include projects that match with given search.
     *
     * @param Builder $query
     * @param string $search
     * @return void
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
     *
     * @param Builder $query
     * @param string $search
     * @return void
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
