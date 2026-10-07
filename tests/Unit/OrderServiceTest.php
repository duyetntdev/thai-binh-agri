<?php

namespace Tests\Unit;

use App\Models\User;
use App\Modules\Orders\Services\OrderService;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class OrderServiceTest extends TestCase
{
    public function test_create_rejects_an_empty_order(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->service()->create(new User, [], 'cod', 1, 1, 'Test address', '0900000000');
    }

    public function test_create_rejects_missing_product_or_non_positive_quantity(): void
    {
        $service = $this->service();

        foreach ([
            [['quantity' => 1]],
            [['product_id' => 1]],
            [['product_id' => 1, 'quantity' => 0]],
            [['product_id' => 1, 'quantity' => -1]],
        ] as $items) {
            try {
                $service->create(new User, $items, 'cod', 1, 1, 'Test address', '0900000000');
                $this->fail('Expected invalid order items to be rejected.');
            } catch (\InvalidArgumentException $exception) {
                $this->assertNotSame('', $exception->getMessage());
            }
        }
    }

    private function service(): OrderService
    {
        return new OrderService(
            Mockery::mock(OrderRepositoryInterface::class),
            Mockery::mock(ProductRepositoryInterface::class),
        );
    }
}
