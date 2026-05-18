<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products  = Product::all();

        $orderScenarios = [
            // Delivered + paid
            [
                'status'         => 'delivered',
                'payment_status' => 'completed',
                'payment_method' => 'vnpay',
                'notes'          => 'Giao hàng trước 5 giờ chiều giúp tôi.',
            ],
            // Delivered + paid (COD)
            [
                'status'         => 'delivered',
                'payment_status' => 'completed',
                'payment_method' => 'cod',
                'notes'          => null,
            ],
            // Shipped
            [
                'status'         => 'shipped',
                'payment_status' => 'pending',
                'payment_method' => 'vnpay',
                'notes'          => 'Gọi điện trước khi giao.',
            ],
            // Processing
            [
                'status'         => 'processing',
                'payment_status' => 'completed',
                'payment_method' => 'bank_transfer',
                'notes'          => null,
            ],
            // Pending
            [
                'status'         => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cod',
                'notes'          => 'Đặt hàng thử lần đầu.',
            ],
            // Cancelled
            [
                'status'         => 'cancelled',
                'payment_status' => 'refunded',
                'payment_method' => 'vnpay',
                'notes'          => 'Đổi ý, hủy đơn.',
            ],
        ];

        foreach ($customers as $index => $customer) {
            // Each customer gets 1–2 orders
            $scenarioCount = ($index % 2 === 0) ? 2 : 1;

            for ($i = 0; $i < $scenarioCount; $i++) {
                $scenario = $orderScenarios[($index + $i) % count($orderScenarios)];

                // Pick 1–3 random products
                $pickedProducts = $products->random(rand(1, 3));
                $totalAmount    = 0;
                $itemsData      = [];

                foreach ($pickedProducts as $product) {
                    $quantity      = rand(1, 5);
                    $price         = $product->price;
                    $totalAmount  += $price * $quantity;
                    $itemsData[]   = [
                        'product_id' => $product->id,
                        'quantity'   => $quantity,
                        'price'      => $price,
                    ];
                }

                $order = Order::create([
                    'user_id'        => $customer->id,
                    'total_amount'   => $totalAmount,
                    'status'         => $scenario['status'],
                    'payment_status' => $scenario['payment_status'],
                    'notes'          => $scenario['notes'],
                    'created_at'     => now()->subDays(rand(1, 60)),
                ]);

                foreach ($itemsData as $item) {
                    OrderItem::create(array_merge($item, ['order_id' => $order->id]));
                }

                // Create payment record for non-pending orders
                if ($scenario['payment_status'] !== 'pending') {
                    Payment::create([
                        'order_id'       => $order->id,
                        'amount'         => $totalAmount,
                        'method'         => $scenario['payment_method'],
                        'transaction_id' => $scenario['payment_status'] === 'completed'
                            ? strtoupper('TXN' . uniqid())
                            : null,
                        'status'         => $scenario['payment_status'],
                        'paid_at'        => in_array($scenario['payment_status'], ['completed', 'refunded'])
                            ? $order->created_at->addHours(rand(1, 24))
                            : null,
                    ]);
                }
            }
        }
    }
}
