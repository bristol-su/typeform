<?php

namespace BristolSU\Module\Typeform\Events;

use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Support\Action\Contracts\TriggerableEvent;

class CommentCreated implements TriggerableEvent
{

    /**
     * @var Comment
     */
    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        return [
            'response_id' => $this->comment->response->id,
            'response_submitted_by_id' => $this->comment->response->submitted_by->id(),
            'response_submitted_by_email' => $this->comment->response->submitted_by->data()->email(),
            'response_submitted_by_first_name' => $this->comment->response->submitted_by->data()->firstName(),
            'response_submitted_by_last_name' => $this->comment->response->submitted_by->data()->lastName(),
            'response_submitted_by_preferred_name' => $this->comment->response->submitted_by->data()->preferredName(),
            'response_module_instance_id' => $this->comment->response->module_instance_id,
            'response_activity_instance_id' => $this->comment->response->activity_instance_id,
            'response_submitted_at' => $this->comment->response->created_at->format('Y-m-d H:i:s'),
            'response_updated_at' => $this->comment->response->updated_at->format('Y-m-d H:i:s'),
            'comment' => $this->comment->comment,
            'comment_id' => $this->comment->id,
            'comment_posted_at' => $this->comment->created_at->format('Y-m-d H:i:s'),
            'comment_edited_at' => $this->comment->updated_at->format('Y-m-d H:i:s'),
            'comment_posted_by_id' => $this->comment->posted_by->id(),
            'comment_posted_by_email' => $this->comment->posted_by->data()->email(),
            'comment_posted_by_first_name' => $this->comment->posted_by->data()->firstName(),
            'comment_posted_by_last_name' => $this->comment->posted_by->data()->lastName(),
            'comment_posted_by_preferred_name' => $this->comment->posted_by->data()->preferredName(),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFieldMetaData(): array
    {
        return [
            'response_id' => [
                'label' => 'File ID',
                'helptext' => 'The ID of the response'
            ],
            'response_submitted_by_id' => [
                'label' => 'File Uploader User ID',
                'helptext' => 'ID of the user who submitted the response'
            ],
            'response_submitted_by_email' => [
                'label' => 'File Uploader User Email',
                'helptext' => 'Email of the user who submitted the response'
            ],
            'response_submitted_by_first_name' => [
                'label' => 'File Uploader User First Name',
                'helptext' => 'First Name of the user who submitted the response'
            ],
            'response_submitted_by_last_name' => [
                'label' => 'File Uploader User Last Name',
                'helptext' => 'Last Name of the user who submitted the response'
            ],
            'response_submitted_by_preferred_name' => [
                'label' => 'File Uploader User Preferred Name',
                'helptext' => 'Preferred Name of the user who submitted the response'
            ],
            'response_module_instance_id' => [
                'label' => 'Module Instance ID',
                'helptext' => 'ID of the module instance the response was submitted to'
            ],
            'response_activity_instance_id' => [
                'label' => 'Activity Instance ID',
                'helptext' => 'ID of the activity instance that submitted the response'
            ],
            'response_submitted_at' => [
                'label' => 'File Uploaded At',
                'helptext' => 'Time and date the response was submitted at'
            ],
            'response_updated_at' => [
                'label' => 'File Updated At',
                'helptext' => 'Time and date the response was last updated'
            ],
            'comment' => [
                'label' => 'Comment Text',
                'helptext' => 'The text that makes up the comment'
            ],
            'comment_id' => [
                'label' => 'Comment ID',
                'helptext' => 'The ID of the comment'
            ],
            'comment_posted_at' => [
                'label' => 'Comment Posted At',
                'helptext' => 'The date and time at which the comment was posted'
            ],
            'comment_edited_at' => [
                'label' => 'Comment Edited At',
                'helptext' => 'The date and time at which the comment was last edited'
            ],
            'comment_posted_by_id' => [
                'label' => 'Comment Posted By',
                'helptext' => 'ID of the user who posted the comment'
            ],
            'comment_posted_by_email' => [
                'label' => 'Comment Posted By Email',
                'helptext' => 'Email of the user who posted the comment'
            ],
            'comment_posted_by_first_name' => [
                'label' => 'Comment Posted By First Name',
                'helptext' => 'First Name of the user who posted the comment'
            ],
            'comment_posted_by_last_name' => [
                'label' => 'Comment Posted By Last Name',
                'helptext' => 'Last Name of the user who posted the comment'
            ],
            'comment_posted_by_preferred_name' => [
                'label' => 'Comment Posted By Preferred Name',
                'helptext' => 'Preferred Name of the user who posted the comment'
            ],
        ];
    }
}