<?php

namespace BristolSU\Module\Tests\Typeform\Events;

use BristolSU\ControlDB\Models\DataUser;
use BristolSU\ControlDB\Models\User;
use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Events\NewResponse;
use BristolSU\Module\Typeform\Models\Response;
use Carbon\Carbon;

class NewResponseTest extends TestCase
{

    /** @test */
    public function it_returns_the_correct_fields()
    {
        $dataUser = factory(DataUser::class)->create([
            'first_name' => 'Toby',
            'last_name' => 'Twigger',
            'email' => 'tobytwigger@example.com',
            'preferred_name' => 'Toby Twigger2'
        ]);
        $user = factory(User::class)->create(['data_provider_id' => $dataUser->id()]);
        $submittedAt = Carbon::now()->subMinutes(15);

        $response = factory(Response::class)->create([
            'id' => 'a-response-id',
            'form_id' => 'some-form-id',
            'submitted_by' => $user->id(),
            'submitted_at' => $submittedAt,
            'module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id
        ]);

        $event = new NewResponse($response);

        $this->assertEquals([
            'id' => 'a-response-id',
            'form_id' => 'some-form-id',
            'submitted_by_id' => $user->id(),
            'submitted_by_email' => 'tobytwigger@example.com',
            'submitted_by_first_name' => 'Toby',
            'submitted_by_last_name' => 'Twigger',
            'submitted_by_preferred_name' => 'Toby Twigger2',
            'submitted_at' => $submittedAt->format('Y-m-d H:i:s'),
            'module_instance_id' => $this->getModuleInstance()->id(),
            'activity_instance_id' => $this->getActivityInstance()->id
        ], $event->getFields());

    }

    /** @test */
    public function it_returns_metadata_for_the_fields(){
        $response = factory(Response::class)->create();

        $event = new NewResponse($response);
        $fields = array_keys($event->getFields());

        foreach($fields as $field) {
            $this->assertArrayHasKey($field, NewResponse::getFieldMetaData());
            $this->assertArrayHasKey('label', NewResponse::getFieldMetaData()[$field]);
            $this->assertArrayHasKey('helptext', NewResponse::getFieldMetaData()[$field]);
        }
    }
}