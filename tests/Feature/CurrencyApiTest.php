<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = app(JwtService::class)->issue(User::factory()->create());
    }

    public function test_it_lists_currencies(): void
    {
        Currency::create([
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.920000',
            'exchange_rate_date' => '2026-10-26',
        ]);

        $this->withToken($this->token)->getJson('/api/currency')
            ->assertOk()
            ->assertJsonPath('data.0.symbol', 'EUR');
    }

    public function test_it_creates_a_currency(): void
    {
        $this->withToken($this->token)->postJson('/api/currency', [
            'name' => 'Euro',
            'symbol' => 'EUR',
            'exchange_rate' => '0.920000',
            'exchange_rate_date' => '2026-10-26',
        ])->assertCreated()
            ->assertJsonPath('data.name', 'Euro')
            ->assertJsonPath('data.exchange_rate_date', '2026-10-26');

        $this->assertDatabaseHas('currencies', ['symbol' => 'EUR']);
    }

    public function test_it_shows_a_currency(): void
    {
        $currency = Currency::create([
            'name' => 'Libra esterlina',
            'symbol' => 'GBP',
            'exchange_rate' => '0.760000',
            'exchange_rate_date' => '2026-10-27',
        ]);

        $this->withToken($this->token)->getJson("/api/currency/{$currency->id}")
            ->assertOk()
            ->assertJsonPath('data.symbol', 'GBP')
            ->assertJsonPath('data.exchange_rate_date', '2026-10-27');
    }
}
