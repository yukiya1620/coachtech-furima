<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** ID5 未ログイン */
    public function test_mylist_guest_shows_nothing(): void
    {
        Item::factory()->create(['name' => '腕時計']);
        $res = $this->get(route('items.index', ['tab' => 'mylist']));
        $res->assertStatus(200);

        $res->assertDontSee('腕時計');
    }

    /** ID8 いいね：いいね登録でマイリストに出る */
    public function test_like_item_appears_in_mylist(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['name' => 'いいね商品']);

        $res = $this->actingAs($user)->post(route('likes.toggle', $item->id));
        $res->assertStatus(302);

        $res2 = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));
        $res2->assertStatus(200);
        $res2->assertSee('いいね商品');
    }

    /** ID5 マイリスト：購入済み商品はSOLD表示 */
    public function test_mylist_shows_sold_label(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['name' => '売れた', 'is_sold' => true]);

        $this->actingAs($user)->post(route('likes.toggle', $item->id));

        $res = $this->actingAs($user)->get(route('items.index', ['tab' => 'mylist']));
        $res->assertStatus(200);
        $res->assertSee('SOLD');
    }
}
