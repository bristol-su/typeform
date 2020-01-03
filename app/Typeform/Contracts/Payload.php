<?php

namespace BristolSU\Module\Typeform\Typeform\Contracts;

abstract class Payload
{

    /**
     * @var array
     */
    private $bundle;

    public function __construct(array $bundle)
    {
        $this->bundle = $bundle;
    }

    public function property($property, $default = null)
    {
        return data_get($this->bundle, $property, $default);
    }

    abstract public function formId();
    abstract public function responseId();
    abstract public function submitterId();
    abstract public function submittedAt();
    abstract public function activityInstanceId();
    abstract public function fields();
    abstract public function answers();
    
}