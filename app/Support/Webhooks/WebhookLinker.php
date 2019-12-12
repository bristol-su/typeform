<?php

namespace BristolSU\Module\Typeform\Support\Webhooks;

use BristolSU\Module\Typeform\Models\Webhook;
use WATR\Typeform;

class WebhookLinker implements Contracts\WebhookLinker
{

    /**
     * @var Typeform
     */
    private $typeform;

    public function __construct()
    {
    }

    public function link(Webhook $webhook)
    {
        $typeform = app(Typeform::class, ['apiKey' => $webhook->typeformConnection->api_key]);
        $form = $typeform->getForm($this->formId($webhook));
        $typeform->registerWebhook($form, $webhook->url(), $webhook->tag);
    }

    private function formId(Webhook $webhook)
    {
        return $webhook->moduleInstance->moduleInstanceSettings->settings['form_id'];
    }
    
    public function exists(Webhook $webhook)
    {
        // TODO register if already exists
        return false;
    }
    
}