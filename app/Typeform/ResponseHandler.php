<?php

namespace BristolSU\Module\Typeform\Typeform;

use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Field;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Contracts\Payload;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResponseHandler
{

    /**
     * @inheritDoc
     */
    public function handle(ModuleInstance $moduleInstance, Payload $payload): ?Response
    {
        if($payload->activityInstanceId() === null) {
            return null;
        }
        // Find the response or create a new response
        $response = Response::create([
            'id' => $payload->responseId(),
            'form_id' => $payload->formId(),
            'submitted_by' => $payload->submitterId(),
            'submitted_at' => $payload->submittedAt(),
            'module_instance_id' => $moduleInstance->id,
            'activity_instance_id' => $payload->activityInstanceId()
        ]);

        // Get the fields that make up the form
        $fields = collect();
        foreach ($payload->fields() as $field) {
            try {
                $fields->push(Field::findOrFail($field['id']));
            } catch (ModelNotFoundException $e) {
                $fields->push(
                    Field::create([
                        'id' => $field['id'], 'form_id' => $payload->formId(), 'type' => $field['type'], 'title' => $field['title']
                    ])
                );
            }
        }

        // Save the answers for each field
        $answers = collect($payload->answers());
        foreach ($fields as $field) {
            $answer = $answers->first(function ($answer) use ($field) {
                return isset($answer['field']) && isset($answer['field']['id']) && $answer['field']['id'] === $field['id'];
            });
            if ($answer !== null) {
                Answer::create([
                    'field_id' => $field->id,
                    'response_id' => $response->id,
                    'type' => $answer['type'],
                    'answer' => $answer[$answer['type']]
                ]);
            }
        }

        return $response;
    }
}