<?php

namespace BristolSU\Module\Typeform\Typeform\Handler;

use BristolSU\Module\Typeform\Typeform\Contracts\Payload;
use Carbon\Carbon;

class WebhookPayload extends Payload
{
        
    public function formId()
    {
        return $this->property('form_response.form_id');
    }

    public function responseId()
    {
        return $this->property('form_response.token');
    }

    public function submitterId()
    {
        return (int) $this->property('form_response.hidden.portal_user_id');
    }

    public function submittedAt()
    {
        return Carbon::parse($this->property('form_response.submitted_at'));
    }

    public function activityInstanceId()
    {
        return (int) $this->property('form_response.hidden.activity_instance');
    }

    public function moduleInstanceId()
    {
        return (int) $this->property('form_response.hidden.module_instance');
    }

    public function fields()
    {
        return $this->property('form_response.definition.fields');
    }

    public function answers()
    {
        return $this->property('form_response.answers');
    }

}