<?php

namespace BristolSU\Module\Typeform\Http\Controllers\ParticipantApi;

use BristolSU\Module\Typeform\Events\CommentCreated;
use BristolSU\Module\Typeform\Events\CommentDeleted;
use BristolSU\Module\Typeform\Events\CommentUpdated;
use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ActivityInstance\Contracts\ActivityInstanceResolver;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index(Request $request, Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('comment.index');
        if ((int)$response->activity_instance_id !== (int)app(ActivityInstanceResolver::class)->getActivityInstance()->id) {
            throw new AuthorizationException();
        }
        return $response->comments;
    }

    public function store(Request $request, Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('comment.store');

        $comment = $response->comments()->create([
            'comment' => $request->input('comment'),
            'posted_by' => app(Authentication::class)->getUser()->id
        ]);

        event(new CommentCreated($comment));

        return $comment;
    }

    public function destroy(Request $request, Activity $activity, ModuleInstance $moduleInstance, Comment $comment)
    {
        $this->authorize('comment.destroy');

        if ((int)$comment->response->activity_instance_id !== (int)app(ActivityInstanceResolver::class)->getActivityInstance()->id) {
            throw new AuthorizationException();
        }

        $comment->delete();

        event(new CommentDeleted($comment));

        return $comment;
    }

    public function update(Request $request, Activity $activity, ModuleInstance $moduleInstance, Comment $comment)
    {
        $this->authorize('comment.update');

        if ((int)$comment->response->activity_instance_id !== (int)app(ActivityInstanceResolver::class)->getActivityInstance()->id) {
            throw new AuthorizationException();
        }

        $comment->comment = $request->input('comment', $comment->comment);

        $comment->save();

        event(new CommentUpdated($comment));
        
        return $comment;
    }

}