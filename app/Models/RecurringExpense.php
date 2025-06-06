<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'description',
        'monthly_cost',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the location that owns the recurring expense.
     */

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
