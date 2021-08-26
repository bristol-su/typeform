<?php

namespace BristolSU\Module\Typeform\Typeform;

use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\Connection\Contracts\Connector;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

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
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
    }

    public function webhookEnabled(Webhook $webhook)
    {
        try {
            $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag);
            $response = json_decode((string) $response->getBody(), true);
            return (isset($response['enabled'])?$response['enabled']:false);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }

    }

    public function webhookCreate(Webhook $webhook)
    {
        try {
            $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
                'json' => [
                    'url' => $webhook->url(),
                    'enabled' => true
                ]
            ]);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
    }

    public function webhookDelete(Webhook $webhook)
    {
        try {
            $this->connector->request('DELETE', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
    }

    public function webhookEnable(Webhook $webhook)
    {
        try {
            $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
                'json' => [
                    'enabled' => true
                ]
            ]);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
    }

    public function webhookDisable(Webhook $webhook)
    {
        try {
            $this->connector->request('PUT', 'https://api.typeform.com/forms/'. $webhook->form_id .'/webhooks/' . $webhook->tag, [
                'json' => [
                    'enabled' => false
                ]
            ]);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
    }

    public function allResponses(string $formId)
    {
        try {
            $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'.  $formId .'/responses', [
                'query' => [
                    'page_size' => 1000,
                    'completed' => true
                ]
            ]);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }

        try {
            $responses = json_decode((string) $response->getBody(), true);
        } catch (\TypeError $e) {
            $responses = null;
        }
        if($responses && array_key_exists('items', $responses)) {
            return $responses['items'];
        }
        return [];
    }

    public function allFields(string $formId)
    {
        try {
            $response = $this->connector->request('GET', 'https://api.typeform.com/forms/'.  $formId);
        } catch (ServerException $e) {
            if($e->getCode() !== 504 && $e->getCode() !== 502) {
                throw $e;
            }
        }
        $responses = json_decode((string) $response->getBody(), true);
        if($responses && array_key_exists('fields', $responses)) {
            return $responses['fields'];
        }
        return [];
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
