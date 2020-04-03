<?php

namespace BristolSU\Module\Typeform;

use BristolSU\Module\Typeform\Commands\CheckResponses;
use BristolSU\Module\Typeform\Commands\SyncWebhookStatus;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesApproved;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesRejected;
use BristolSU\Module\Typeform\CompletionConditions\NumberOfResponsesSubmitted;
use BristolSU\Module\Typeform\Events\NewResponse;
use BristolSU\Module\Typeform\Http\Controllers\Webhook\IncomingWebhookController;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Contracts\ResponseHandler;
use BristolSU\Module\Typeform\Typeform\WebhookHandler;
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

class ModuleServiceProvider extends ServiceProvider
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
        ]
    ];

    protected $events = [
        NewResponse::class => [
            'name' => 'New Response',
            'description' => 'When a new response is submitted'
        ]
    ];
    
    protected $commands = [
        SyncWebhookStatus::class,
        CheckResponses::class
    ];
    
    protected $scheduledCommands = [
        SyncWebhookStatus::class => '* * * * *',
        CheckResponses::class => '*/5 * * * *'
    ];
    
    public function alias(): string
    {
        return 'typeform';
    }

    public function namespace()
    {
        return '\BristolSU\Module\Typeform\Http\Controllers';
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
        
        Route::prefix('/api/a/{activity_slug}/{module_instance_slug}/typeform')
            ->middleware(['api', InjectModuleInstance::class, InjectActivity::class])
            ->namespace($this->namespace())
            ->group($this->baseDirectory() . '/routes/admin/webhook.php');
        
    }

    public function settings(): Form
    {
        return FormGenerator::make()->withGroup(
            Group::make('Page Design')->withField(
                Field::input('title')->inputType('text')->label('Module Title')->default('Page Title')
            )->withField(
                Field::textArea('description')->label('Description')->hint('This will appear at the top of the page')->rows(4)->default('Description')
            )
        )->withGroup(
            Group::make('Embedded Form')->withField(
                Field::radios('embed_type')->inputType('text')->label('Type of form embedding')->hint('Embed the form in the page, or show the form as a popup?')
                    ->values([
                        ['name' => 'Embed the form in the page', 'value' => 'widget'],
                        ['name' => 'Show the form as a popup', 'value' => 'popup'],
                        ['name' => 'Show the form as a drawer from the left', 'value' => 'drawer_left'],
                        ['name' => 'Show the form as a drawer from the right', 'value' => 'drawer_right'],
                    ])->default('widget')
            )->withField(
                Field::input('form_url')->inputType('text')->label('Form URL')->hint('The URL of the form. Make sure it\'s published first!')
            )->withField(
                Field::switch('hide_headers')->label('Hide form headers?')->hint('Should we hide the form headers? This helps integrate the form into the page.')
                    ->textOn('Hidden')->textOff('Shown')->default(true)
            )->withField(
                Field::switch('hide_footer')->label('Hide form footer?')->hint('Should we hide the form footer? This helps integrate the form into the page.')
                    ->textOn('Hidden')->textOff('Shown')->default(true)
            )
        )->withGroup(
            Group::make('Responses')->withField(
                Field::switch('collect_responses')->label('Save responses?')->hint('Do you want responses to be saved on the portal? You will always be able to see responses on typeform.')
                    ->textOn('Save')->textOff('Do not save')->default(false)
            )->withField(
                Field::input('form_id')->inputType('text')->label('Form ID')->hint('ID of the form so we can collect responses')
            )->withField(
                Field::switch('use_webhook')->label('Use webhook?')->hint('Use a webhook for instant responses?')
                    ->textOn('Use')->textOff('Do not use')->default(true)
            )->withField(
                Field::switch('approval')->label('Approval Stage?')->hint('Turn on approval of responses?')
                ->textOn('Approval Enabled')->textOff('Approval Disabled')->default(false)
                ->help('Turning on approval allows you to delay completion of the module until you\'ve approved a response')
            )
        )->getSchema();
    }

}
