<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestbookControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testing that renderMain works
     *
     * @return void
     */
    public function test_render_main_loads(): void
    {
        $response = $this->get('/');

        $response->assertViewIs('guestbook');
        $response->assertViewHas('messages');
    }

    /**
     * Testing that you can't load the thread if not logged in
     *
     * @return void
     */
    public function test_render_message_redirects_if_not_logged_in(): void
    {
        $response = $this->followingRedirects()->get('/message/123');

        $response->assertViewIs('guestbook');
        $response->assertViewHas('messages');
    }

    /**
     * Testing that you can't load the thread if you're
     * a normal user not part of it
     *
     * @return void
     */
    public function test_render_message_redirects_if_not_in_the_thread(): void
    {
        $user = User::factory()->create([]);
        $userTwo = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $response = $this->followingRedirects()->actingAs($userTwo)->get('/message/' . $message->id);

        $response->assertViewIs('guestbook');
        $response->assertViewHas('messages');
    }

    /**
     * Testing that you can load the thread when logged in
     * as the owner
     *
     * @return void
     */
    public function test_render_message_when_logged_in_as_owner(): void
    {
        $user = User::factory()->create([]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get('/message/' . $message->id);

        $response->assertViewIs('message');
        $response->assertViewHas('message');
        $response->assertViewHas('comments');
    }

    /**
     * Testing that you can load the thread when logged in
     * as an admin
     *
     * @return void
     */
    public function test_render_message_when_logged_in_as_admin(): void
    {
        $user = User::factory()->create([]);
        $admin = User::factory()->create(['is_admin' => true]);

        $message = Message::create([
            'comment' => 'Old Message',
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($admin)->get('/message/' . $message->id);

        $response->assertViewIs('message');
        $response->assertViewHas('message');
        $response->assertViewHas('comments');
    }
}
