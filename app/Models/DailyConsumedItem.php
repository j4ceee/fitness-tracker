<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyConsumedItem extends Model
{
    use HasFactory;

    protected $table = 'daily_consumed_items';

    protected $fillable = [
        'user_id',
        'item_id',
        'consumed_at',
        'quantity',
    ];

    public function consumableItem(): BelongsTo
    {
        return $this->belongsTo(ConsumableItem::class, 'consumable_item_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
}
