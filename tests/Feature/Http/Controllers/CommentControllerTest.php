<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\CommentController;
use App\Models\Comment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that createReply validation fails when
     * message doesn't get sent
     *
     * @return void
     */
    public function test_create_reply_fails_validation(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/reply", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to save your comment');
    }

    /**
     * Test createReply fails when the message doesn't exist
     *
     * @return void
     */
    public function test_create_reply_fails_when_message_does_not_exist(): void
    {
        $user = User::factory()->create([]);

        $this->actingAs($user)
            ->post("message/UnitTest/reply", ['message' => __FUNCTION__])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Message does not exist');
    }

    /**
     * Test createReply fails when trying to comment when not the owner of the
     * original message or when not admin
     *
     * @return void
     */
    public function test_create_reply_fails_when_trying_to_reply_as_random(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $this->actingAs($userTwo)
            ->post("message/$message->id/reply", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to save your comment');
    }

    /**
     * Testing that createReply works when commenting as the original
     * owner
     *
     * @return void
     */
    public function test_create_reply_works_for_original_user(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/reply", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment = Comment::where('message_id', '=', $message->id)->where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $comment);
    }

    /**
     * Testing that createReply works for admin users
     *
     * @return void
     */
    public function test_create_reply_works_for_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/reply", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment = Comment::where('message_id', '=', $message->id)->where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $comment);
    }

    /**
     * Test that editComment validation fails when
     * message doesn't get sent
     *
     * @return void
     */
    public function test_edit_comment_fails_validation(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/$comment->id/edit", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to edit your comment');
    }

    /**
     * Testing comment edit fails when not the owner or an admin
     *
     * @return void
     */
    public function test_edit_comment_fails_when_trying_to_edit_as_random(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($userTwo)
            ->post("message/$message->id/$comment->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasErrors();

        $comment->refresh();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to edit your comment');
        $this->assertEquals('UnitTest', $comment->comment);
    }

    /**
     * Testing comment edit works for user that created it
     *
     * @return void
     */
    public function test_edit_comment_works_for_user(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/$comment->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment->refresh();

        $this->assertEquals(__FUNCTION__, $comment->comment);
    }

    /**
     * Testing comment edit works for admins
     *
     * @return void
     */
    public function test_edit_comment_works_for_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/$comment->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment->refresh();

        $this->assertEquals(__FUNCTION__, $comment->comment);
    }

    /**
     * Test toggleFavourite doesn't work when not an admin
     *
     * @return void
     */
    public function test_toggle_favourite_fails_when_not_admin(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/$comment->id/star", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to star your comment');
    }

    /**
     * Test that toggleFavourite fails when comment doesn't exist
     *
     * @return void
     */
    public function test_toggle_favourite_fails_when_comment_does_not_exist(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $admin->id
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/UnitTest/star", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to star your comment');
    }

    /**
     * Test that toggleFavourite sets all to falls and
     * toggles the selected comment from false to true
     *
     * @return void
     */
    public function test_toggle_favourite_toggles_to_true(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id,
            'favourite' => false
        ]);

        $commentTwo = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id,
            'favourite' => true
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/$comment->id/star", [])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment->refresh();
        $commentTwo->refresh();

        $this->assertEquals(true, $comment->favourite);
        $this->assertEquals(false, $commentTwo->favourite);
    }

    /**
     * Test that toggleFavourite sets all to falls and
     * toggles the selected comment from true to false
     *
     * @return void
     */
    public function test_toggle_favourite_toggles_to_false(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id,
            'favourite' => true,
        ]);

        $commentTwo = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id,
            'favourite' => true
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/$comment->id/star", [])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment->refresh();
        $commentTwo->refresh();

        $this->assertEquals(false, $comment->favourite);
        $this->assertEquals(false, $commentTwo->favourite);
    }

    /**
     * Testing commentDelete fails when not the owner or an admin
     *
     * @return void
     */
    public function test_edit_delete_fails_when_trying_to_edit_as_random(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($userTwo)
            ->post("message/$message->id/$comment->id/delete", ['message' => __FUNCTION__])
            ->assertSessionHasErrors();

        $comment = Comment::where('id', '=', $comment->id)->where('message_id', '=', $message->id)->get();

        $this->assertCount(1, $comment);
        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to delete your comment');
    }

    /**
     * Testing commentDelete works for user that created it
     *
     * @return void
     */
    public function test_edit_delete_works_for_user(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($user)
            ->post("message/$message->id/$comment->id/delete", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment = Comment::where('id', '=', $comment->id)->where('message_id', '=', $message->id)->get();

        $this->assertCount(0, $comment);
    }

    /**
     * Testing commentDelete works for admins
     *
     * @return void
     */
    public function test_edit_delete_works_for_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id
        ]);

        $comment = Comment::create([
            'comment' => 'UnitTest',
            'user_id' => $user->id,
            'message_id' => $message->id
        ]);

        $this->actingAs($admin)
            ->post("message/$message->id/$comment->id/delete", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $comment = Comment::where('id', '=', $comment->id)->where('message_id', '=', $message->id)->get();

        $this->assertCount(0, $comment);
    }
}
