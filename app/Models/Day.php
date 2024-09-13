<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    use HasFactory;

    protected $table = 'days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date',
        'weight',
        'training_duration',
        'day_calorie_goal',
        'calories',
        'water',
        'steps',
        'meals_warm',
        'meals_cold',
        'is_cheat_day',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dailyConsumedItems(): HasMany
    {
        return $this->hasMany(DailyConsumedItem::class, 'day_id');
    }
}
