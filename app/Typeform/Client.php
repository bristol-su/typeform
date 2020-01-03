<?php

namespace BristolSU\Module\Typeform\Typeform;

use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\Connection\Contracts\Connector;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\Cache\Repository;

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

    public function request($method, $uri, $options = [])
    {
        return $this->connector->request($method, $uri, $options);
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
        return json_decode((string) $response->getBody(), true)['items'];

    }

    public function allFields(string $formId)
    {
        $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'.  $formId);
        return json_decode((string) $response->getBody(), true)['fields'];
    }
}