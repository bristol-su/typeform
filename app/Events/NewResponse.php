<?php

namespace BristolSU\Module\Typeform\Events;

use BristolSU\ControlDB\Contracts\Repositories\User;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\Action\Contracts\TriggerableEvent;

class NewResponse implements TriggerableEvent
{

    /**
     * @var Response
     */
    public $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        $user = app(User::class)->getById($this->response->submitted_by);
        return [
            'id' => $this->response->id,
            'form_id' => $this->response->form_id,
            'submitted_by_id' => $this->response->submitted_by,
            'submitted_by_email' => $user->data()->email(),
            'submitted_by_first_name' => $user->data()->firstName(),
            'submitted_by_last_name' => $user->data()->lastName(),
            'submitted_by_preferred_name' => $user->data()->preferredName(),
            'submitted_at' => $this->response->submitted_at->format('Y-m-d H:i:s'),
            'module_instance_id' => $this->response->module_instance_id,
            'activity_instance_id' => $this->response->activity_instance_id
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFieldMetaData(): array
    {
        return [
            'id' => [
                'label' => 'Response ID',
                'helptext' => 'The ID of the response as given by Typeform'
            ],
            'form_id' => [
                'label' => 'Form ID',
                'helptext' => 'The ID of the Typeform form'
            ],
            'submitted_by_id' => [
                'label' => 'Form Submitted By',
                'helptext' => 'ID of the user who submitted the form'
            ],
            'submitted_by_email' => [
                'label' => 'Form Submitted By Email',
                'helptext' => 'Email of the user who submitted the form'
            ],
            'submitted_by_first_name' => [
                'label' => 'Form Submitted By First Name',
                'helptext' => 'First Name of the user who submitted the form'
            ],
            'submitted_by_last_name' => [
                'label' => 'Form Submitted By Last Name',
                'helptext' => 'Last Name of the user who submitted the form'
            ],
            'submitted_by_preferred_name' => [
                'label' => 'Form Submitted By Preferred Name',
                'helptext' => 'Preferred Name of the user who submitted the form'
            ],
            'submitted_at' => [
                'label' => 'Form Submitted At',
                'helptext' => 'The date and time at which the form_was_filled_in'
            ],
            'module_instance_id' => [
                'label' => 'Module Instance ID',
                'helptext' => 'ID of the module instance the form was submitted in'
            ],
            'activity_instance_id' => [
                'label' => 'Activity Instance ID',
                'helptext' => 'ID of the activity instance that submitted the form'
            ],
        ];
    }
}