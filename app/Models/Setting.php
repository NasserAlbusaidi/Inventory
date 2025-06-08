<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
   use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get the value of a setting by key.
     *
     * @param string $key
     * @return mixed
     */
    public static function getValue($key)
    {
        return self::where('key', $key)->first()->value ?? null;
    }

    /**
     * Set the value of a setting by key.
     *
     * @param string $key
     * @param mixed $value
     */
    public static function setValue($key, $value)
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
