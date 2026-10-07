<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AdministrativeAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = Http::acceptJson()
            ->timeout(60)
            ->get('https://provinces.open-api.vn/api/v2/?depth=2')
            ->throw()
            ->json();

        if (! is_array($areas) || $areas === []) {
            throw new RuntimeException('Không tải được dữ liệu tỉnh thành và phường xã.');
        }

        DB::transaction(function () use ($areas) {
            $provinceRows = collect($areas)->map(fn (array $province) => [
                'code' => (string) $province['code'],
                'name' => $province['name'],
                'division_type' => $province['division_type'],
                'codename' => $province['codename'],
                'created_at' => now(),
                'updated_at' => now(),
            ])->all();

            DB::table('provinces')->upsert(
                $provinceRows,
                ['code'],
                ['name', 'division_type', 'codename', 'updated_at'],
            );

            $provinceIds = Province::query()->pluck('id', 'code');
            $wardRows = collect($areas)
                ->flatMap(fn (array $province) => collect($province['wards'] ?? [])->map(fn (array $ward) => [
                    'code' => (string) $ward['code'],
                    'province_id' => $provinceIds[(string) $province['code']],
                    'name' => $ward['name'],
                    'division_type' => $ward['division_type'],
                    'codename' => $ward['codename'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]))
                ->all();

            foreach (array_chunk($wardRows, 500) as $chunk) {
                DB::table('wards')->upsert(
                    $chunk,
                    ['code'],
                    ['province_id', 'name', 'division_type', 'codename', 'updated_at'],
                );
            }

            if ($wardRows !== []) {
                DB::table('wards')->whereNotIn('code', array_column($wardRows, 'code'))->delete();
            }

            DB::table('provinces')->whereNotIn('code', array_column($provinceRows, 'code'))->delete();
        });
    }
}
