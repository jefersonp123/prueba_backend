<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private Currency $currency;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->token = app(JwtService::class)->issue($user);
        $this->currency = Currency::create([
            'name' => 'Dólar estadounidense',
            'symbol' => 'USD',
            'exchange_rate' => '1.000000',
        ]);
    }

    public function test_product_routes_require_authentication(): void
    {
        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_it_creates_lists_shows_updates_and_deletes_a_product(): void
    {
        $payload = $this->productPayload();

        $created = $this->withToken($this->token)->postJson('/api/products', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'Laptop')
            ->assertJsonPath('data.manufacuring_date_create', '2026-01-15')
            ->assertJsonPath('data.manufacuring_date_expire', '2028-01-15')
            ->assertJsonMissingPath('data.currency')
            ->json('data');

        $this->withToken($this->token)->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.currency.name', 'Dólar estadounidense');

        $this->withToken($this->token)->getJson("/api/products/{$created['id']}")
            ->assertOk()
            ->assertJsonPath('data.currency_id', $this->currency->id)
            ->assertJsonPath('data.currency.name', 'Dólar estadounidense')
            ->assertJsonMissingPath('data.currency.exchange_rate');

        $payload['name'] = 'Laptop Pro';
        $this->withToken($this->token)->putJson("/api/products/{$created['id']}", $payload)
            ->assertOk()
            ->assertJsonPath('data.name', 'Laptop Pro')
            ->assertJsonMissingPath('data.currency');

        $this->withToken($this->token)->deleteJson("/api/products/{$created['id']}")
            ->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $created['id']]);
    }

    public function test_it_validates_product_input_and_returns_not_found(): void
    {
        $this->withToken($this->token)->postJson('/api/products', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost']);

        $this->withToken($this->token)->getJson('/api/products/999')->assertNotFound();

        $payload = $this->productPayload();
        $payload['manufacuring_date_expire'] = '2025-01-15';
        $this->withToken($this->token)->postJson('/api/products', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('manufacuring_date_expire');
    }

    public function test_it_registers_lists_and_prevents_duplicate_currency_prices(): void
    {
        $product = Product::create($this->productPayload());
        $euro = Currency::create(['name' => 'Euro', 'symbol' => 'EUR', 'exchange_rate' => '0.920000']);
        $payload = ['currency_id' => $euro->id, 'price' => '920.00'];

        $this->withToken($this->token)->postJson("/api/products/{$product->id}/prices", $payload)
            ->assertCreated()
            ->assertJsonPath('data.currency.symbol', 'EUR');

        $this->withToken($this->token)->getJson("/api/products/{$product->id}/prices")
            ->assertOk()->assertJsonCount(1, 'data');

        $this->withToken($this->token)->postJson("/api/products/{$product->id}/prices", $payload)
            ->assertUnprocessable()->assertJsonValidationErrors('currency_id');

        $this->withToken($this->token)->postJson("/api/products/{$product->id}/prices", [
            'currency_id' => $this->currency->id,
            'price' => '1000.00',
        ])->assertUnprocessable()->assertJsonValidationErrors('currency_id');
    }

    public function test_deleting_a_product_cascades_its_prices(): void
    {
        $product = Product::create($this->productPayload());
        $product->prices()->create(['currency_id' => $this->currency->id, 'price' => '1000.00']);

        $this->withToken($this->token)->deleteJson("/api/products/{$product->id}")->assertOk();

        $this->assertDatabaseCount('product_prices', 0);
    }

    private function productPayload(): array
    {
        return [
            'name' => 'Laptop',
            'description' => 'Equipo de prueba',
            'price' => '1000.00',
            'currency_id' => $this->currency->id,
            'tax_cost' => '160.00',
            'manufacturing_cost' => '600.00',
            'manufacuring_date_create' => '2026-01-15',
            'manufacuring_date_expire' => '2028-01-15',
        ];
    }
}
