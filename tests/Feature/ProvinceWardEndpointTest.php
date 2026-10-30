<?php

namespace Tests\Feature;

use App\Models\Province;
use App\Models\Ward;
use App\Modules\Orders\Requests\StoreOrderRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProvinceWardEndpointTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('division_type');
            $table->string('codename');
            $table->timestamps();
        });

        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('province_id');
            $table->string('name');
            $table->string('division_type');
            $table->string('codename');
            $table->timestamps();
        });
    }

    public function test_endpoint_returns_only_wards_belonging_to_selected_province(): void
    {
        $province = Province::create([
            'code' => 'test-1',
            'name' => 'Tỉnh A',
            'division_type' => 'tỉnh',
            'codename' => 'tinh_a',
        ]);
        $otherProvince = Province::create([
            'code' => 'test-2',
            'name' => 'Tỉnh B',
            'division_type' => 'tỉnh',
            'codename' => 'tinh_b',
        ]);

        Ward::create([
            'code' => 'ward-1',
            'province_id' => $province->id,
            'name' => 'Xã Một',
            'division_type' => 'xã',
            'codename' => 'xa_mot',
        ]);
        Ward::create([
            'code' => 'ward-2',
            'province_id' => $otherProvince->id,
            'name' => 'Phường Hai',
            'division_type' => 'phường',
            'codename' => 'phuong_hai',
        ]);

        $this->getJson(route('cart.wards', $province))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.name', 'Xã Một');
    }

    public function test_checkout_rejects_a_ward_from_another_province(): void
    {
        $province = Province::create([
            'code' => 'test-1',
            'name' => 'Tỉnh A',
            'division_type' => 'tỉnh',
            'codename' => 'tinh_a',
        ]);
        $otherProvince = Province::create([
            'code' => 'test-2',
            'name' => 'Tỉnh B',
            'division_type' => 'tỉnh',
            'codename' => 'tinh_b',
        ]);
        $ward = Ward::create([
            'code' => 'ward-2',
            'province_id' => $otherProvince->id,
            'name' => 'Phường Hai',
            'division_type' => 'phường',
            'codename' => 'phuong_hai',
        ]);

        $request = StoreOrderRequest::create('/checkout', 'POST', [
            'province_id' => $province->id,
            'ward_id' => $ward->id,
        ]);
        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->errors()->has('ward_id'));
    }

    public function test_checkout_phone_accepts_normal_phone_format_and_rejects_letters(): void
    {
        $phoneRules = (new StoreOrderRequest)->rules()['shipping_phone'];

        $this->assertFalse(Validator::make(
            ['shipping_phone' => '+84 901-234-567'],
            ['shipping_phone' => $phoneRules],
        )->fails());

        $this->assertTrue(Validator::make(
            ['shipping_phone' => '0900ABCD'],
            ['shipping_phone' => $phoneRules],
        )->fails());
    }
}
