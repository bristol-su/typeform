<?php

namespace BristolSU\Module\Tests\Typeform\Http\Controllers\ParticipantApi;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Events\CommentCreated;
use BristolSU\Module\Typeform\Events\CommentDeleted;
use BristolSU\Module\Typeform\Events\CommentUpdated;
use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class CommentControllerTest extends TestCase
{

    /** @test */
    public function index_returns_403_if_permission_not_given(){
        $this->revokePermissionTo('typeform.comment.index');

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $jsonResponse = $this->getJson($this->userApiUrl('response/' . $response->id . '/comment'));
        $jsonResponse->assertStatus(403);
    }

    /** @test */
    public function index_returns_200_if_permission_given(){
        $this->givePermissionTo('typeform.comment.index');

        $response = Response::factory()->create([
            'module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $jsonResponse = $this->getJson($this->userApiUrl('response/' . $response->id . '/comment'));
        $jsonResponse->assertStatus(200);
    }

    /** @test */
    public function index_returns_comments(){
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comments = Comment::factory()->count(5)->create(['response_id' => $response->id]);
        $otherComments = Comment::factory()->count(3)->create();

        $jsonResponse = $this->getJson($this->userApiUrl('/response/' . $response->id . '/comment'));

        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJsonCount(5);

        foreach($comments as $comment) {
            $jsonResponse->assertJsonFragment([
                'comment' => $comment->comment,
                'response_id' => (string) $response->id
            ]);
        }
    }

    /** @test */
    public function index_returns_a_403_if_the_response_does_not_belong_to_the_activity_instance(){
        $this->bypassAuthorization();

        $activityInstance = ActivityInstance::factory()->create(['activity_id' => $this->getActivity()->id]);
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $activityInstance->id]);
        $comments = Comment::factory()->count(5)->create(['response_id' => $response->id]);

        $jsonResponse = $this->getJson($this->userApiUrl('/response/' . $response->id . '/comment'));

        $jsonResponse->assertStatus(403);
    }

    /** @test */
    public function store_returns_403_if_permission_not_given(){
        $this->revokePermissionTo('typeform.comment.store');

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $jsonResponse = $this->postJson($this->userApiUrl('response/' . $response->id . '/comment'), ['comment' => 'Test']);
        $jsonResponse->assertStatus(403);
    }

    /** @test */
    public function store_returns_200_if_permission_given(){
        $this->givePermissionTo('typeform.comment.store');

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $jsonResponse = $this->postJson($this->userApiUrl('response/' . $response->id . '/comment'), ['comment' => 'Test']);
        $jsonResponse->assertStatus(201);
    }

    /** @test */
    public function store_creates_a_new_comment_in_the_database(){
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $this->assertDatabaseMissing('typeform_comments', ['response_id' => $response->id]);

        $jsonResponse = $this->postJson($this->userApiUrl('response/' . $response->id . '/comment'), ['comment' => 'TestComment Here']);
        $jsonResponse->assertStatus(201);

        $this->assertDatabaseHas('typeform_comments', [
            'response_id' => $response->id,
            'comment' => 'TestComment Here',
            'posted_by' => $this->getControlUser()->id()
        ]);
    }

    /** @test */
    public function store_returns_the_new_comment(){
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $jsonResponse = $this->postJson($this->userApiUrl('response/' . $response->id . '/comment'), ['comment' => 'TestComment Here']);
        $jsonResponse->assertStatus(201);
        $jsonResponse->assertJsonFragment([
            'response_id' => $response->id,
            'comment' => 'TestComment Here',
        ]);

    }

    /** @test */
    public function store_fires_an_event(){
        Event::fake(CommentCreated::class);
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id()]);

        $jsonResponse = $this->postJson($this->userApiUrl('response/' . $response->id . '/comment'), ['comment' => 'TestComment Here']);
        $jsonResponse->assertStatus(201);

        Event::assertDispatched(CommentCreated::class, function($event) use ($response) {
            return $event instanceof CommentCreated && $event->comment->response->is($response);
        });
    }

    /** @test */
    public function destroy_returns_403_if_permission_not_given(){
        $this->revokePermissionTo('typeform.comment.destroy');
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);

        $jsonResponse = $this->deleteJson($this->userApiUrl('/comment/' . $comment->id));
        $jsonResponse->assertStatus(403);

        $this->assertDatabaseHas('typeform_comments', [
            'id' => $comment->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function destroy_returns_200_if_permission_given(){
        $this->givePermissionTo('typeform.comment.destroy');
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);
        $jsonResponse = $this->deleteJson($this->userApiUrl('/comment/' . $comment->id));
        $jsonResponse->assertStatus(200);
    }

    /** @test */
    public function destroy_soft_deletes_the_comment(){
        $this->bypassAuthorization();
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);
        $jsonResponse = $this->deleteJson($this->userApiUrl('/comment/' . $comment->id));
        $jsonResponse->assertStatus(200);

        $this->assertSoftDeleted('typeform_comments', [
            'id' => $comment->id,
        ]);
    }

    /** @test */
    public function destroy_returns_the_comment(){
        $this->bypassAuthorization();
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $jsonResponse = $this->deleteJson($this->userApiUrl('/comment/' . $comment->id));
        $jsonResponse->assertStatus(200);

        $jsonResponse->assertJsonFragment([
            'id' => $comment->id,
            'deleted_at' => $now->format('Y-m-d H:i:s')
        ]);
    }

    /** @test */
    public function destroy_returns_a_403_if_the_response_does_not_belong_to_the_activity_instance(){
        $this->bypassAuthorization();

        $activityInstance = ActivityInstance::factory()->create(['activity_id' => $this->getActivity()->id]);
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $activityInstance->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);

        $jsonResponse = $this->deleteJson($this->userApiUrl('/comment/' . $comment->id));

        $jsonResponse->assertStatus(403);
    }

    /** @test */
    public function destroy_fires_an_event(){
        Event::fake(CommentDeleted::class);
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);

        $jsonResponse = $this->deleteJson($this->userApiUrl('comment/' . $comment->id));
        $jsonResponse->assertStatus(200);

        Event::assertDispatched(CommentDeleted::class, function($event) use ($comment) {
            return $event instanceof CommentDeleted && $event->comment->is($comment);
        });
    }

    /** @test */
    public function update_returns_403_if_permission_not_owned(){
        $this->revokePermissionTo('typeform.comment.update');
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['comment' => 'OldComment', 'response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('/comment/' . $comment->id), ['comment' => 'NewComment']);
        $jsonResponse->assertStatus(403);

        $this->assertDatabaseHas('typeform_comments', [
            'id' => $comment->id,
            'comment' => 'OldComment'
        ]);
    }

    /** @test */
    public function update_returns_200_if_permission_owned(){
        $this->givePermissionTo('typeform.comment.update');
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['comment' => 'OldComment', 'response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('/comment/' . $comment->id), ['comment' => 'NewComment']);
        $jsonResponse->assertStatus(200);
    }

    /** @test */
    public function update_updates_the_comment_text(){
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['comment' => 'OldComment', 'response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('/comment/' . $comment->id), ['comment' => 'NewComment']);
        $jsonResponse->assertStatus(200);

        $this->assertDatabaseHas('typeform_comments', [
            'id' => $comment->id,
            'comment' => 'NewComment'
        ]);
    }

    /** @test */
    public function update_returns_the_updated_comment(){
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['comment' => 'OldComment', 'response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('/comment/' . $comment->id), ['comment' => 'NewComment']);
        $jsonResponse->assertStatus(200);

        $jsonResponse->assertJsonFragment([
            'id' => $comment->id,
            'comment' => 'NewComment'
        ]);
    }

    /** @test */
    public function update_returns_a_403_if_the_response_does_not_belong_to_the_activity_instance(){
        $this->bypassAuthorization();

        $activityInstance = ActivityInstance::factory()->create(['activity_id' => $this->getActivity()->id]);
        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $activityInstance->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('/comment/' . $comment->id), ['comment' => 'NewComment']);

        $jsonResponse->assertStatus(403);
    }

    /** @test */
    public function update_fires_an_event(){
        Event::fake(CommentUpdated::class);
        $this->bypassAuthorization();

        $response = Response::factory()->create(['module_instance_id' => $this->getModuleInstance()->id(), 'activity_instance_id' => $this->getActivityInstance()->id]);
        $comment = Comment::factory()->create(['response_id' => $response->id]);

        $jsonResponse = $this->patchJson($this->userApiUrl('comment/' . $comment->id), ['comment' => 'NewComment']);
        $jsonResponse->assertStatus(200);

        Event::assertDispatched(CommentUpdated::class, function($event) use ($comment) {
            return $event instanceof CommentUpdated && $event->comment->is($comment);
        });
    }
}
