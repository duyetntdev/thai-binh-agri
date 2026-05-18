<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentStatus;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    public function findByOrder(Order $order): ?Payment
    {
        return Payment::where('order_id', $order->id)->first();
    }

    public function firstOrCreateForOrder(Order $order, array $data): Payment
    {
        return Payment::firstOrCreate(
            ['order_id' => $order->id],
            array_merge(['order_id' => $order->id], $data),
        );
    }

    public function markCompleted(Payment $payment, string $transactionId): Payment
    {
        $payment->update([
            'transaction_id' => $transactionId,
            'status'         => PaymentStatus::COMPLETED,
            'paid_at'        => now(),
        ]);

        return $payment->fresh();
    }

    public function markFailed(Payment $payment): Payment
    {
        $payment->update(['status' => PaymentStatus::FAILED]);

        return $payment->fresh();
    }

    public function updateStatus(Payment $payment, PaymentStatus $status): Payment
    {
        $payment->update(['status' => $status]);

        return $payment->fresh();
    }
}
