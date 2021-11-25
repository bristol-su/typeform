<?php

namespace BristolSU\Module\Typeform;

use BristolSU\Module\Typeform\Commands\CheckResponses;
use BristolSU\Module\Typeform\Commands\SyncWebhookStatus;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesApproved;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesRejected;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesSubmitted;
use BristolSU\Module\Typeform\Events\NewResponse;
use BristolSU\Module\Typeform\Events\ResponseApproved;
use BristolSU\Module\Typeform\Events\ResponseRejected;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Events\CommentCreated;
use BristolSU\Module\Typeform\Events\CommentDeleted;
use BristolSU\Module\Typeform\Events\CommentUpdated;
use BristolSU\Module\Typeform\Models\Comment;
use BristolSU\Support\Activity\Middleware\InjectActivity;
use BristolSU\Support\Completion\Contracts\CompletionConditionManager;
use BristolSU\Support\Connection\Contracts\ServiceRequest;
use BristolSU\Support\Module\ModuleServiceProvider as ServiceProvider;
use BristolSU\Support\ModuleInstance\Middleware\InjectModuleInstance;
use FormSchema\Generator\Field;
use FormSchema\Generator\Form as FormGenerator;
use FormSchema\Generator\Group;
use FormSchema\Schema\Form;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Route;

class
ModuleServiceProvider extends ServiceProvider
{

    protected $permissions = [
        'view-form' => [
            'name' => 'View Form',
            'description' => 'View and submit the form.',
            'admin' => false
        ],
        'view-responses' => [
            'name' => 'View Responses',
            'description' => 'View the responses for the form.',
            'admin' => false
        ],
        'admin.view-form' => [
            'name' => 'View Form',
            'description' => 'View the form.',
            'admin' => true
        ],
        'admin.download-file' => [
            'name' => 'Download File',
            'description' => 'Download a file attached to the form.',
            'admin' => true
        ],
        'admin.refresh-form-responses' => [
            'name' => 'Refresh Form Responses',
            'description' => 'Manually refresh the form responses.',
            'admin' => true
        ],
        'admin.approve' => [
            'name' => 'Approve Responses',
            'description' => 'Can approve responses',
            'admin' => true
        ],
        'admin.comment.index' => [
            'name' => 'See comments',
            'description' => 'Allow the admin to see comments',
            'admin' => true
        ],
        'admin.comment.store' => [
            'name' => 'Comment',
            'description' => 'Allow the admin to comment on responses',
            'admin' => true
        ],
        'admin.comment.destroy' => [
            'name' => 'Delete a Comment',
            'description' => 'Allow the admin to delete a comment.',
            'admin' => true
        ],
        'admin.comment.update' => [
            'name' => 'Update a Comment',
            'description' => 'Allow the admin to update a comment.',
            'admin' => true
        ],

        // Comments
        'comment.index' => [
            'name' => 'See comments',
            'description' => 'Allow the user to see comments',
            'admin' => false
        ],
        'comment.store' => [
            'name' => 'Comment',
            'description' => 'Allow the user to comment on responses',
            'admin' => false
        ],
        'comment.destroy' => [
            'name' => 'Delete a Comment',
            'description' => 'Allow the user to delete a comment.',
            'admin' => false
        ],
        'comment.update' => [
            'name' => 'Update a Comment',
            'description' => 'Allow the user to update a comment.',
            'admin' => false
        ],
    ];

    protected $events = [
        NewResponse::class => [
            'name' => 'New Response',
            'description' => 'When a new response is submitted'
        ],
        CommentCreated::class => [
            'name' => 'Comment Left',
            'description' => 'When a comment has been left'
        ],
        CommentDeleted::class => [
            'name' => 'Comment Deleted',
            'description' => 'When a comment has been deleted'
        ],
        CommentUpdated::class => [
            'name' => 'Comment Updated',
            'description' => 'When a comment has been updated'
        ],
        ResponseApproved::class => [
            'name' => 'Response Approved',
            'description' => 'When a response has been approved'
        ],
        ResponseRejected::class => [
            'name' => 'Response Rejected',
            'description' => 'When a response has been rejected'
        ],
    ];

    protected $commands = [
        SyncWebhookStatus::class,
        CheckResponses::class
    ];

    protected $scheduledCommands = [
        SyncWebhookStatus::class => '*/10 * * * *',
        CheckResponses::class => '*/6 * * * *'
    ];

    public function alias(): string
    {
        return 'typeform';
    }

    public function namespace()
    {
        return null;
    }

    public function baseDirectory()
    {
        return __DIR__ . '/..';
    }

    public function boot()
    {
        parent::boot();

        app(CompletionConditionManager::class)->register('typeform', 'number_of_responses_submitted', NumberOfResponsesSubmitted::class);
        app(CompletionConditionManager::class)->register('typeform', 'number_of_responses_approved', NumberOfResponsesApproved::class);
        app(CompletionConditionManager::class)->register('typeform', 'number_of_responses_rejected', NumberOfResponsesRejected::class);
        app(ServiceRequest::class)->required($this->alias(), ['typeform']);

        Route::bind('typeform_response_id', function($id) {
            return Response::findOrFail($id);
        });
        Route::bind('typeform_answer_id_admin', function($id) {
            $answer = Answer::findOrFail($id);
            if($answer->response->module_instance_id === request()->route('module_instance_slug')->id()) {
                return $answer;
            }
            throw (new ModelNotFoundException)->setModel(Answer::class, $id);
        });
        Route::bind('typeform_answer_id_user', function($id) {
            $answer = Answer::findOrFail($id);
            if($answer->response->module_instance_id === request()->route('module_instance_slug')->id()
                && $answer->response->activity_instance_id === Response::activityInstanceId()
            ) {
                return $answer;
            }
            throw (new ModelNotFoundException)->setModel(Answer::class, $id);
        });

        Route::bind('typeform_comment', function($id) {
            $comment = Comment::findOrFail($id);
            if(request()->route('module_instance_slug') && (int) $comment->response->module_instance_id === request()->route('module_instance_slug')->id()) {
                return $comment;
            }
            throw (new ModelNotFoundException)->setModel(Comment::class);
        });

        Route::prefix('/api/a/{activity_slug}/{module_instance_slug}/typeform')
            ->middleware(['api', InjectModuleInstance::class, InjectActivity::class])
            ->namespace($this->namespace())
            ->group($this->baseDirectory() . '/routes/admin/webhook.php');

    }

    public function settings(): Form
    {
        return FormGenerator::make()->withGroup(
            Group::make('Page Design')->withField(
                Field::textInput('title')->setLabel('Module Title')->setValue('Page Title')
            )->withField(
                Field::textArea('description')->setLabel('Description')->setHint('This will appear at the top of the page')->setValue('Description')
            )
        )->withGroup(
            Group::make('Embedded Form')->withField(
                Field::radios('embed_type')->setLabel('Type of form embedding')->setHint('Embed the form in the page, or show the form as a popup?')
                    ->setOptions([
                        ['text' => 'Embed the form in the page', 'id' => 'widget'],
//                        ['text' => 'Show the form as a popup', 'id' => 'popup'],
//                        ['text' => 'Show the form as a drawer from the left', 'id' => 'drawer_left'],
//                        ['text' => 'Show the form as a drawer from the right', 'id' => 'drawer_right'],
                    ])->setValue('widget')
            )->withField(
                Field::textInput('form_url')->setLabel('Form URL')->setHint('The URL of the form. Make sure it\'s published first!')
            )->withField(
                Field::switch('hide_headers')->setLabel('Hide form headers?')->setHint('Should we hide the form headers? This helps integrate the form into the page.')
                    ->setOnText('Hidden')->setOffText('Shown')->setValue(true)
            )->withField(
                Field::switch('hide_footer')->setLabel('Hide form footer?')->setHint('Should we hide the form footer? This helps integrate the form into the page.')
                    ->setOnText('Hidden')->setOffText('Shown')->setValue(true)
            )
        )->withGroup(
            Group::make('Responses')->withField(
                Field::switch('collect_responses')->setLabel('Save responses?')->setHint('Do you want responses to be saved on the portal? You will always be able to see responses on typeform.')
                    ->setOnText('Save')->setOffText('Do not save')->setValue(false)
            )->withField(
                Field::textInput('form_id')->setLabel('Form ID')->setHint('ID of the form so we can collect responses')
            )->withField(
                Field::switch('use_webhook')->setLabel('Use webhook?')->setHint('Use a webhook for instant responses?')
                    ->setOnText('Use')->setOffText('Do not use')->setValue(true)
            )->withField(
                Field::switch('approval')->setLabel('Approval Stage?')->setHint('Turn on approval of responses?')
                ->setOnText('Approval Enabled')->setOffText('Approval Disabled')->setValue(false)
                ->setTooltip('Turning on approval allows you to delay completion of the module until you\'ve approved a response')
            )
        )->getSchema();
    }

}
