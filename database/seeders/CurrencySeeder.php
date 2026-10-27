<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::upsert([
            ['name' => 'Dólar estadounidense', 'symbol' => 'USD', 'exchange_rate' => '1.000000', 'exchange_rate_date' => '2026-10-26'],
            ['name' => 'Euro', 'symbol' => 'EUR', 'exchange_rate' => '0.920000', 'exchange_rate_date' => '2026-10-26'],
            ['name' => 'Bolívar', 'symbol' => 'VES', 'exchange_rate' => '36.500000', 'exchange_rate_date' => '2026-10-26'],
        ], ['name'], ['symbol', 'exchange_rate', 'exchange_rate_date']);
    }
}
