<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_status',
        'notes',
        'shipping_address',
        'shipping_phone',
        'province_id',
        'ward_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::COMPLETED;
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canCancel();
    }

    /**
     * Recalculate total_amount from order items.
     */
    public function recalculateTotal(): void
    {
        $this->total_amount = $this->items()->sum(\DB::raw('price * quantity'));
        $this->save();
    }
}
