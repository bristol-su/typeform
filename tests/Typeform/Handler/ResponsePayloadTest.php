<?php

namespace BristolSU\Module\Tests\Typeform\Typeform\Handler;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Typeform\Handler\ResponsePayload;
use Carbon\Carbon;

class ResponsePayloadTest extends TestCase
{

    /** @test */
    public function the_functions_return_the_correct_information(){
        $bundle =

        $jayParsedAry = [
            "landing_id" => "21085286190ffad1248d17c4135ee56f",
            "token" => "21085286190ffad1248d17c4135ee56f",
            "landed_at" => "2017-09-14T22:33:59Z",
            "submitted_at" => "2017-09-14T22:38:22Z",
            "metadata" => [
                "user_agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8",
                "platform" => "other",
                "referer" => "https://user_id.typeform.com/to/lR6F4j",
                "network_id" => "responsdent_network_id",
                "browser" => "default"
            ],
            "answers" => [
                [
                    "field" => [
                        "id" => "hVONkQcnSNRj",
                        "type" => "dropdown",
                        "ref" => "my_custom_dropdown_reference"
                    ],
                    "type" => "text",
                    "text" => "Job opportunities"
                ],
                [
                    "field" => [
                        "id" => "RUqkXSeXBXSd",
                        "type" => "yes_no",
                        "ref" => "my_custom_yes_no_reference"
                    ],
                    "type" => "boolean",
                    "boolean" => false
                ]
            ],
            "hidden" => [
                'portal_user_id' => 1,
                'activity_instance' => 3,
                'module_instance' => 4
            ],
            "calculated" => [
                "score" => 2
            ]
        ];
        
        $payload = new ResponsePayload($bundle, 'form-id-123', [
            ['field' => 'field1'], ['field' => 'field2']
        ]);

        $this->assertEquals('form-id-123', $payload->formId());
        $this->assertEquals('21085286190ffad1248d17c4135ee56f', $payload->responseId());
        $this->assertEquals(1, $payload->submitterId());
        $this->assertEquals(Carbon::parse('2017-09-14T22:38:22Z'), $payload->submittedAt());
        $this->assertEquals(3, $payload->activityInstanceId());
        $this->assertEquals(4, $payload->moduleInstanceId());
        $this->assertEquals([
            [
                "field" => [
                    "id" => "hVONkQcnSNRj",
                    "type" => "dropdown",
                    "ref" => "my_custom_dropdown_reference"
                ],
                "type" => "text",
                "text" => "Job opportunities"
            ],
            [
                "field" => [
                    "id" => "RUqkXSeXBXSd",
                    "type" => "yes_no",
                    "ref" => "my_custom_yes_no_reference"
                ],
                "type" => "boolean",
                "boolean" => false
            ]
        ], $payload->answers());
        $this->assertEquals([
            ['field' => 'field1'], ['field' => 'field2']
        ], $payload->fields());
        
    }
    
}