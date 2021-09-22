<?php

namespace BristolSU\Module\Tests\Typeform\Models;

use BristolSU\ControlDB\Models\User;
use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Module\Typeform\Models\Response;

class CommentTest extends TestCase
{

    /** @test */
    public function a_comment_can_be_created(){
        $response = Response::factory()->create();
        $user = $this->newUser();

        $comment = Comment::factory()->create([
            'response_id' => $response->id,
            'comment' => 'This is a test comment',
            'posted_by' => $user->id()
        ]);

        $this->assertDatabaseHas('typeform_comments', [
            'id' => $comment->id,
            'response_id' => $response->id,
            'comment' => 'This is a test comment',
            'posted_by' => $user->id()
        ]);
    }

    /** @test */
    public function a_comment_belongs_to_a_response(){
        $response = Response::factory()->create();

        $comment = Comment::factory()->create([
            'response_id' => $response->id,
        ]);

        $this->assertInstanceOf(Response::class, $comment->response);
        $this->assertModelEquals($response, $comment->response);
    }

    /** @test */
    public function posted_by_attribute_returns_a_user(){
        $user = $this->newUser();

        $comment = Comment::factory()->create([
            'posted_by' => $user->id()
        ]);

        $this->assertInstanceOf(User::class, $comment->posted_by);
        $this->assertModelEquals($user, $comment->posted_by);
    }

}
