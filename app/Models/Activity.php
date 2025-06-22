<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activity';

    protected $fillable = [
        'type',
        'description',
        'activity_time',
    ];

    protected $casts = [
        'activity_time' => 'datetime',
    ];

    /**
     * Get the formatted activity time.
     *
     * @return string
     */
    public function getFormattedActivityTimeAttribute()
    {
        return $this->activity_time->format('Y-m-d H:i:s');
    }

    /**
     * Scope a query to only include activities of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }


}
