<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsumableItem extends Model
{
    use HasFactory;

    protected $table = 'consumable_items';

    protected $fillable = [
        'name',
        'points',
    ];

    public function dailyConsumedItem(): HasMany
    {
        return $this->hasMany(DailyConsumedItem::class, 'consumable_item_id');
    }
}
