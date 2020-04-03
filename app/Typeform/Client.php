<?php

namespace BristolSU\Module\Typeform\Typeform;

use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\Connection\Contracts\Connector;
use GuzzleHttp\Exception\ClientException;

class Client
{
    /**
     * @var Connector
     */
    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function webhookExists(Webhook $webhook)
    {
        try {
            $this->connector->request('GET', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag);
            return true;
        } catch (ClientException $e) {
            if($e->getCode() === 404){
                return false;
            }
            throw $e;
        } 
    }

    public function webhookEnabled(Webhook $webhook)
    {
        $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag);
        $response = json_decode((string) $response->getBody(), true);
        return (isset($response['enabled'])?$response['enabled']:false);
    }

    public function webhookCreate(Webhook $webhook)
    {
        $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
            'json' => [
                'url' => $webhook->url(),
                'enabled' => true
            ]
        ]);
    }

    public function webhookDelete(Webhook $webhook)
    {
        $this->connector->request('DELETE', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag);
    }

    public function webhookEnable(Webhook $webhook)
    {
        $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
            'json' => [
                'enabled' => true
            ]
        ]);
    }

    public function webhookDisable(Webhook $webhook)
    {
        $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
            'json' => [
                'enabled' => false
            ]
        ]);
    }

    public function allResponses(string $formId)
    {
        $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'.  $formId .'/responses', [
            'query' => [
                'page_size' => 1000
            ]
        ]);
        $responses = json_decode((string) $response->getBody(), true);
        if($responses && array_key_exists('items', $responses)) {
            return $responses['items'];
        }
        return [];

    }

    public function allFields(string $formId)
    {
        $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'.  $formId);
        return json_decode((string) $response->getBody(), true)['fields'];
    }

    public function downloadFileFromAnswer(Answer $answer, $file)
    {
        return $this->connector->request('GET', $answer->answer, [
            'sink' => $file,
            'curl.options' => array(
                'CURLOPT_RETURNTRANSFER' => true,
                'CURLOPT_FILE' => $file
            )
        ]);
    }
}