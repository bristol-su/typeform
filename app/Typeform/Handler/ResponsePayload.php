<?php

namespace BristolSU\Module\Typeform\Typeform\Handler;

use BristolSU\Module\Typeform\Typeform\Contracts\Payload;
use Carbon\Carbon;

class ResponsePayload extends Payload
{

    private $formId;
    private $fields;

    public function __construct(array $bundle, $formId, $fields)
    {
        parent::__construct($bundle);
        $this->formId = $formId;
        $this->fields = $fields;
    }

    public function formId()
    {
        return $this->formId;
    }

    public function responseId()
    {
        return $this->property('token');
    }

    public function submitterId()
    {
        return (int) $this->property('hidden.portal_user_id');
    }

    public function submittedAt()
    {
        return Carbon::parse($this->property('submitted_at'));
    }

    public function activityInstanceId()
    {
        return (int) $this->property('hidden.activity_instance');
    }

    public function fields()
    {
        return ($this->fields ?? []);
    }

    public function answers()
    {
        return $this->property('answers');
    }

    public function moduleInstanceId()
    {
        return (int) $this->property('hidden.module_instance');
    }
}