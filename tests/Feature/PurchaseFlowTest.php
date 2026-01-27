<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    /** ID10 購入 */
    public function test_purchase_marks_item_as_sold(): void
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $seller = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create([
            'user_id' => $seller->id,
            'is_sold' => false,
        ]);

        $this->actingAs($buyer)->get(route('purchase.create', $item->id))->assertStatus(200);

        $shipping = [
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区テスト1-2-3',
            'building' => 'テストビル101',
        ];
        
        $res = $this->actingAs($buyer)
          ->withSession(["shipping.item.{$item->id}" => $shipping])
          ->post(route('purchase.store', $item->id),[
              'payment_method' => 'convenience',
          ]);

          $res->assertStatus(302);
          $res->assertSessionHasNoErrors();

          $this->assertDatabaseHas('purchases', [
              'user_id' => $buyer->id,
              'item_id' => $item->id,
              'payment_method' => 'convenience',
              'shipping_postal_code' => '123-4567',
            ]);

          $this->assertDatabaseHas('items', [
              'id' => $item->id,
              'is_sold' => 1,
            ]);
            
    }

    /** ID10 購入済み商品 */
    public function test_sold_item_shows_sold_on_index(): void
    {
        Item::factory()->create(['name' => '売れた', 'is_sold' => true]);

        $res = $this->get(route('items.index', ['tab' => 'recommend']));
        $res->assertStatus(200);
        $res->assertSee('SOLD');
    }

    /** ID10 プロフィール */
    public function test_purchased_item_appears_on_mypage_buy(): void
    {
        $buyer = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['is_sold' => true]);

        \DB::table('purchases')->insert([
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'shipping_postal_code' => '123-4567',
            'shipping_address' => '東京都渋谷区テスト1-2-3',
            'shipping_building' => 'テストビル101',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->actingAs($buyer)->get(route('mypage.index', ['page' => 'buy']));
        $res->assertStatus(200);
        $res->assertSee((string)$item->name);
    }

    /** ID13 ユーザー情報取得 */
    public function test_mypage_shows_user_name(): void
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'name' => '購入者ユーザー']);
        $res = $this->actingAs($user)->get(route('mypage.index', ['page' => 'sell']));
        $res->assertStatus(200);
        $res->assertSee('購入者ユーザー');
    }

    /** ID14 ユーザー情報変更 */
    public function test_profile_edit_page_loads(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $res = $this->actingAs($user)->get(route('profile.edit'));
        $res->assertStatus(200);
    }

    /** ID15 出品商品情報登録 */
    public function test_sell_create_page_loads(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $res = $this->actingAs($user)->get(route('sell.create'));
        $res->assertStatus(200);
    }
}
