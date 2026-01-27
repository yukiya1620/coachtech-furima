<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemShowCommentTest extends TestCase
{
    use RefreshDatabase;

    /** ID7 商品詳細 */
    public function test_item_show_displays_basic_info(): void
    {
        $item = Item::factory()->create(['name' => '腕時計', 'price' => 15000]);
        $res = $this->get(route('items.show', $item->id));
        $res->assertStatus(200);
        $res->assertSee('腕時計');
        $res->assertSee('15,000');
    }

    /** ID9 コメント送信 */
    public function test_verified_user_can_post_comment(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => 'テストコメント',
        ]);

        $res->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    public function test_guest_cannot_post_comment(): void
    {
        $item = Item::factory()->create();

        $res = $this->post(route('comments.store', $item->id), [
            'content' => 'ゲスト',
        ]);

        $res->assertStatus(302);
        $res->assertRedirect(route('login'));
    }

    /** ID9 コメント */
    public function test_unverified_user_redirected_to_verification_notice_on_comment(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => '未認証',
        ]);

        $res->assertStatus(302);
        $res->assertRedirect(route('verification.notice'));
    }

    public function test_comment_validation_required(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => '',
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['content']);
    }

    /** ID9 コメント */
    public function test_comment_validation_max_255(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $res = $this->actingAs($user)->post(route('comments.store', $item->id), [
            'content' => str_repeat('a', 256),
        ]);

        $res->assertStatus(302);
        $res->assertSessionHasErrors(['content']);
    }
}
