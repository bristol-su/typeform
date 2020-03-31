<?php

namespace BristolSU\Module\Typeform\Typeform\Handler;

use BristolSU\Module\Typeform\Events\NewResponse;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Field;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Contracts\Payload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class ResponseHandler
{
    
    /**
     * @inheritDoc
     */
    public function handle(Payload $payload): ?Response
    {
        if($payload->activityInstanceId() === null || $payload->moduleInstanceId() === null || $payload->submitterId() === null) {
            return null;
        }
        
        $response = $this->createResponse($payload);
        
        $fields = $this->createFields($payload);
        
        $answers = collect($payload->answers());
        foreach ($fields as $field) {
            $answer = $answers->first(function ($answer) use ($field) {
                return isset($answer['field']) && isset($answer['field']['id']) && $answer['field']['id'] === $field['id'];
            });
            if ($answer !== null) {
                Answer::updateOrCreate([
                    'field_id' => $field->id,
                    'response_id' => $response->id,
                ], [
                    'type' => $answer['type'],
                    'answer' => $answer[$answer['type']]
                ]);
            }
        }

        event(new NewResponse($response));
        
        return $response;
    }

    private function createResponse(Payload $payload): Response
    {
        return Response::firstOrCreate([
            'id' => $payload->responseId()
        ], [
            'form_id' => $payload->formId(),
            'submitted_by' => $payload->submitterId(),
            'submitted_at' => $payload->submittedAt(),
            'module_instance_id' => $payload->moduleInstanceId(),
            'activity_instance_id' => $payload->activityInstanceId()
        ]);
    }

    /**
     * @param Payload $payload
     * @return Collection|Field[]
     */
    private function createFields(Payload $payload): Collection
    {
        $fields = collect();
        
        foreach ($payload->fields() as $field) {
            try {
                $field = Field::findOrFail($field['id']);
            } catch (ModelNotFoundException $e) {
                $field = Field::create([
                    'id' => $field['id'],
                    'form_id' => $payload->formId(),
                    'type' => $field['type'],
                    'title' => $field['title']
                ]);
            }
            $fields->push($field);
        }
        
        return $fields;
    }
}