<?php

namespace BristolSU\Module\Tests\Typeform\Typeform\Handler;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Typeform\Handler\WebhookPayload;
use Carbon\Carbon;

class WebhookPayloadTest extends TestCase
{

    /** @test */
    public function the_functions_return_the_correct_information()
    {
        $bundle = [
            "event_id" => "LtWXD3crgy",
            "event_type" => "form_response",
            "form_response" => [
                "form_id" => "lT4Z3j",
                "token" => "a3a12ec67a1365927098a606107fac15",
                "submitted_at" => "2018-01-18T18:17:02Z",
                "landed_at" => "2018-01-18T18:07:02Z",
                "calculated" => [
                    "score" => 9
                ],
                "definition" => [
                    "id" => "lT4Z3j",
                    "title" => "Webhooks example",
                    "fields" => [
                        [
                            "id" => "DlXFaesGBpoF",
                            "title" => "Thanks, {{answer_60906475}}! What's it like where you live? Tell us in a few sentences.",
                            "type" => "long_text",
                            "ref" => "[readable_ref_long_text",
                            "allow_multiple_selections" => false,
                            "allow_other_choice" => false
                        ],
                        [
                            "id" => "SMEUb7VJz92Q",
                            "title" => "If you're OK with our city management following up if they have further questions, please give us your email address.",
                            "type" => "email",
                            "ref" => "readable_ref_email",
                            "allow_multiple_selections" => false,
                            "allow_other_choice" => false
                        ]
                    ]
                ],
                "hidden" => [
                    "portal_user_id" => 1,
                    'activity_instance' => 3,
                    'module_instance' => 4
                ],
                "answers" => [
                    [
                        "type" => "text",
                        "text" => "It's cold right now! I live in an older medium-sized city with a university. Geographically, the area is hilly.",
                        "field" => [
                            "id" => "DlXFaesGBpoF",
                            "type" => "long_text"
                        ]
                    ],
                    [
                        "type" => "email",
                        "email" => "laura@example.com",
                        "field" => [
                            "id" => "SMEUb7VJz92Q",
                            "type" => "email"
                        ]
                    ]
                ]
            ]
        ];
        
        $payload = new WebhookPayload($bundle);

        $this->assertEquals('lT4Z3j', $payload->formId());
        $this->assertEquals('a3a12ec67a1365927098a606107fac15', $payload->responseId());
        $this->assertEquals(1, $payload->submitterId());
        $this->assertEquals(Carbon::parse('2018-01-18T18:17:02Z'), $payload->submittedAt());
        $this->assertEquals(3, $payload->activityInstanceId());
        $this->assertEquals(4, $payload->moduleInstanceId());
        $this->assertEquals([
            [
                "type" => "text",
                "text" => "It's cold right now! I live in an older medium-sized city with a university. Geographically, the area is hilly.",
                "field" => [
                    "id" => "DlXFaesGBpoF",
                    "type" => "long_text"
                ]
            ],
            [
                "type" => "email",
                "email" => "laura@example.com",
                "field" => [
                    "id" => "SMEUb7VJz92Q",
                    "type" => "email"
                ]
            ]
        ], $payload->answers());
        $this->assertEquals([
            [
                "id" => "DlXFaesGBpoF",
                "title" => "Thanks, {{answer_60906475}}! What's it like where you live? Tell us in a few sentences.",
                "type" => "long_text",
                "ref" => "[readable_ref_long_text",
                "allow_multiple_selections" => false,
                "allow_other_choice" => false
            ],
            [
                "id" => "SMEUb7VJz92Q",
                "title" => "If you're OK with our city management following up if they have further questions, please give us your email address.",
                "type" => "email",
                "ref" => "readable_ref_email",
                "allow_multiple_selections" => false,
                "allow_other_choice" => false
            ]
        ], $payload->fields());
    }

}