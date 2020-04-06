<?php

namespace BristolSU\Module\Typeform\Http\Controllers\AdminApi;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Events\CommentCreated;
use BristolSU\Module\Typeform\Events\CommentDeleted;
use BristolSU\Module\Typeform\Events\CommentUpdated;
use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\Authentication\Contracts\Authentication;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index(Request $request, Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('admin.comment.index');

        return $response->comments;
    }

    public function store(Request $request, Activity $activity, ModuleInstance $moduleInstance, Response $response)
    {
        $this->authorize('admin.comment.store');

        $comment = $response->comments()->create([
            'comment' => $request->input('comment'),
            'posted_by' => app(Authentication::class)->getUser()->id
        ]);

        event(new CommentCreated($comment));

        return $comment;
    }

    public function destroy(Request $request, Activity $activity, ModuleInstance $moduleInstance, Comment $comment)
    {
        $this->authorize('admin.comment.destroy');

        $comment->delete();

        event(new CommentDeleted($comment));

        return $comment;
    }

    public function update(Request $request, Activity $activity, ModuleInstance $moduleInstance, Comment $comment)
    {
        $this->authorize('admin.comment.update');

        $comment->comment = $request->input('comment', $comment->comment);

        $comment->save();

        event(new CommentUpdated($comment));

        return $comment;
    }
    
}