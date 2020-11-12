<?php

namespace Components\Tests\Models;

use App\Traits\LangTrait;
use Carbon\Carbon;
use Components\Tests\Contracts\Models\TestInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Test extends Model implements TestInterface
{
    use SoftDeletes;

    protected $table = 'tests';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',

        'public_start',
    ];

    public function setUrlAttribute($value): void
    {
        $this->attributes['url'] = empty($value) ? str_slug($this->attributes['title']) : str_slug($value);
    }

    public function getFullUrlAttribute(): string
    {
        return $this->path_name . DIRECTORY_SEPARATOR . $this->url;
    }

    public function getPathNameAttribute(): string
    {
        return $this->table;
    }

    public function questions(): Relation
    {
        return $this->hasMany(TestQuestion::class);
    }

    public function results(): Relation
    {
        return $this->hasMany(TestResults::class);
    }

    public function scopePublic($query, Carbon $now): Builder
    {
        return $query
            ->where('published', true)
            ->where(function ($query) use ($now) {
                $query->where('public_start', '<', $now)
                    ->orWhereNull('public_start');
            })
        ;
    }
}
