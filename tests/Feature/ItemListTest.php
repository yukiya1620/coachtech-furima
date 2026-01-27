<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** ID4 商品一覧：全商品が取得できる */
    public function test_items_index_shows_items(): void
    {
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'スニーカー']);

        $res = $this->get(route('items.index', ['tab' => 'recommend']));
        $res->assertStatus(200);
        $res->assertSee('腕時計');
        $res->assertSee('スニーカー');
    }

    /** ID4 商品一覧：購入済み商品はSOLD表示 */
    public function test_items_index_shows_sold_label(): void
    {
        Item::factory()->create(['name' => '売れた', 'is_sold' => true]);

        $res = $this->get(route('items.index', ['tab' => 'recommend']));
        $res->assertStatus(200);
        $res->assertSee('SOLD');
    }

    /** ID6 商品検索 */
    public function test_can_search_items_by_partial_name(): void
    {
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'スニーカー']);

        $res = $this->get(route('items.index', ['keyword' => '時計', 'tab' => 'recommend']));

        $res->assertStatus(200);
        $res->assertSee('腕時計');
        $res->assertDontSee('スニーカー');
    }

    /** ID6 検索状態がマイリストでも保持 */
    public function test_search_query_is_preserved_on_tabs(): void
    {
        Item::factory()->create(['name' => '腕時計']);

        $res = $this->get(route('items.index', ['keyword' => '時計', 'tab' => 'recommend']));
        $res->assertStatus(200);

        $res->assertSee('value="時計"', false);
    }
}
