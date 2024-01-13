<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing that validation fails when message
     * isn't given
     *
     * @return void
     */
    public function test_create_message_validation_fails(): void
    {
        $user = User::factory()->create([]);

        $this->actingAs($user)
            ->post('/message/', [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to save your message');
    }

    /**
     * Testing that we can add messages to the DB
     *
     * @return void
     */
    public function test_create_message_works(): void
    {
        $user = User::factory()->create([]);

        $this->actingAs($user)
            ->post('/message/', ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $message);
    }

    /**
     * Testing that validation fails when message
     * isn't given
     *
     * @return void
     */
    public function test_edit_message_validation_fails(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->post("/message/$message->id/edit", [])
            ->assertSessionHasErrors();

        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to update your message');
    }

    /**
     * Testing that the edit has been made as a user
     *
     * @return void
     */
    public function test_edit_message_can_be_done_by_user(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->post("/message/$message->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $message);
    }

    /**
     * Testing edit can't be made by a different user
     *
     * @return void
     */
    public function test_edit_message_can_not_be_done_different_user(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $this->actingAs($userTwo)
            ->post("/message/$message->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasErrors();

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(0, $message);
        $errors = session('errors');
        $this->assertEquals($errors->get('error')[0], 'Unable to update your message');
    }

    /**
     * Testing that the edit has been made as an admin
     *
     * @return void
     */
    public function test_edit_message_can_be_done_by_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $this->actingAs($admin)
            ->post("/message/$message->id/edit", ['message' => __FUNCTION__])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $message);
    }

    /**
     * Testing that the edit has been made as an admin
     *
     * @return void
     */
    public function test_delete_message_can_be_done_by_user(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => __FUNCTION__,
            'user_id' => $user->id
        ]);

        $this->actingAs($user)
            ->post("/message/$message->id/delete", [])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(0, $message);
    }

    /**
     * Testing edit can't be made by a different user
     *
     * @return void
     */
    public function test_delete_message_can_not_be_done_different_user(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => __FUNCTION__,
            'user_id' => $user->id
        ]);

        $this->actingAs($userTwo)
            ->post("/message/$message->id/delete", [])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(1, $message);
    }

    /**
     * Testing that the edit has been made as an admin
     *
     * @return void
     */
    public function test_delete_message_can_be_done_by_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => __FUNCTION__,
            'user_id' => $user->id
        ]);

        $this->actingAs($admin)
            ->post("/message/$message->id/delete", [])
            ->assertSessionHasNoErrors()
            ->assertStatus(Response::HTTP_FOUND);

        $message = Message::where('comment', '=', __FUNCTION__)->get();

        $this->assertCount(0, $message);
    }
}
