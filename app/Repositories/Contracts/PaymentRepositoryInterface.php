<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentStatus;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find the payment record for a given order.
     */
    public function findByOrder(Order $order): ?Payment;

    /**
     * Create or retrieve the payment record for an order.
     */
    public function firstOrCreateForOrder(Order $order, array $data): Payment;

    /**
     * Mark a payment as completed with a transaction ID.
     */
    public function markCompleted(Payment $payment, string $transactionId): Payment;

    /**
     * Mark a payment as failed.
     */
    public function markFailed(Payment $payment): Payment;

    /**
     * Update the status of a payment.
     */
    public function updateStatus(Payment $payment, PaymentStatus $status): Payment;
}
