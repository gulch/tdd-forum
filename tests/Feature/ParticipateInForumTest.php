<?php

namespace Tests\Feature;

use App\Reply;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    public function authenticated_users_may_not_add_replies()
    {
        $this->expectException(AuthenticationException::class);

        $thread = \factory('App\Thread')->create();
        $reply = \factory('App\Reply')->create();
        $this->post($thread->path() . '/replies', $reply->toArray());
    }

    /** @test */
    public function an_authenticated_user_can_participate_in_forum_threads()
    {
        $this->be($user = \create('App\User'));

        $thread = \create('App\Thread');

        $reply = \create('App\Reply');

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */
    public function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();

        $thread = \create('App\Thread');

        $reply = \make('App\Reply', ['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }
    
    /** @test */
    public function unauthorized_users_cannot_delete_replies()
    {
        $this->withExceptionHandling();

        $reply = \create(Reply::class);

        $this->delete('/replies/' . $reply->id)
            ->assertRedirect('/login');
    }

    /** @test */
    public function authorized_users_can_delete_replies()
    {
        $this->signIn();

        $reply = \create(Reply::class, ['user_id' => \auth()->id()]);

        $this->delete('/replies/' . $reply->id);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    public function unauthorized_users_cannot_update_replies()
    {
        $this->withExceptionHandling();

        $reply = \create(Reply::class);

        $this->patch('/replies/' . $reply->id, ['body' => ''])
            ->assertRedirect('/login');
    }
    
    /** @test */
    public function authorized_users_can_update_replies()
    {
        $this->signIn();

        $reply = \create(Reply::class, ['user_id' => \auth()->id()]);

        $updatedReply = 'You been changed, fool.';
        $this->patch('/replies/' . $reply->id, ['body' => $updatedReply]);

        $this->assertDatabaseHas('replies', [
            'id' => $reply->id,
            'body' => $updatedReply,
        ]);
    }
}
