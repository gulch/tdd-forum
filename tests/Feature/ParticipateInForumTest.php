<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    /*public function authenticated_users_may_not_add_replies()
    {
        $this->expectException(AuthenticationException::class);

        $thread = \factory('App\Thread')->create();
        $reply = \factory('App\Reply')->create();
        $this->post($thread->path() . '/replies', $reply->toArray());
    }*/

    /** @test */
    public function an_authenticated_user_can_participate_in_forum_threads()
    {
        $this->be($user = \factory('App\User')->create());

        $thread = \factory('App\Thread')->create();

        $reply = \factory('App\Reply')->create();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->get($thread->path())->assertSee($reply->body);
    }
}
