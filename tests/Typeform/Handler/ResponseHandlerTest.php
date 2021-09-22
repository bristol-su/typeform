<?php

namespace BristolSU\Module\Tests\Typeform\Typeform\Handler;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Events\NewResponse;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Models\Field;
use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Module\Typeform\Typeform\Contracts\Payload;
use BristolSU\Module\Typeform\Typeform\Handler\ResponseHandler;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class ResponseHandlerTest extends TestCase
{

    /** @test */
    public function it_creates_a_new_response_model(){
        $payload = new DummyPayload([
            'fields' => [], 'answers' => []
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_responses', [
            'id' => 'some-response-id',
            'form_id' => 'form-id-123',
            'submitted_by' => 1,
            'submitted_at' => '0001-01-01 00:00:00',
            'module_instance_id' => 4,
            'activity_instance_id' => 3
        ]);
    }

    /** @test */
    public function it_returns_the_response(){
        $payload = new DummyPayload([
            'fields' => [], 'answers' => []
        ]);

        $handler = new ResponseHandler();
        $response = $handler->handle($payload);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->exists);

        $responseFromDatabase = Response::where('id', 'some-response-id')->get()->first();
        $this->assertInstanceOf(Response::class, $responseFromDatabase);
        $this->assertTrue($responseFromDatabase->exists);

        $this->assertModelEquals($responseFromDatabase, $response);
    }

    /** @test */
    public function it_creates_all_the_fields(){
        $payload = new DummyPayload([
            'fields' => [
                ['id' => 'field1', 'type' => 'boolean', 'title' => 'Field 1'],
                ['id' => 'field2', 'type' => 'text', 'title' => 'Field 2'],
                ['id' => 'field3', 'type' => 'boolean', 'title' => 'Field 3'],
            ], 'answers' => []
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_fields', [
            'form_id' => 'form-id-123',
            'id' => 'field1',
            'type' => 'boolean',
            'title' => 'Field 1'
        ]);

        $this->assertDatabaseHas('typeform_fields', [
            'form_id' => 'form-id-123',
            'id' => 'field2',
            'type' => 'text',
            'title' => 'Field 2'
        ]);

        $this->assertDatabaseHas('typeform_fields', [
            'form_id' => 'form-id-123',
            'id' => 'field3',
            'type' => 'boolean',
            'title' => 'Field 3'
        ]);

        $this->assertEquals(3, Field::count());
    }

    /** @test */
    public function it_does_not_create_duplicate_fields(){

        $field1 = Field::factory()->create([
            'form_id' => 'form-id-123',
            'id' => 'field1',
            'type' => 'boolean',
            'title' => 'Field 1'
        ]);

        $payload = new DummyPayload([
            'fields' => [
                ['id' => 'field1', 'type' => 'boolean', 'title' => 'Field 1'],
                ['id' => 'field2', 'type' => 'text', 'title' => 'Field 2'],
                ['id' => 'field3', 'type' => 'boolean', 'title' => 'Field 3'],
            ], 'answers' => []
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_fields', [
            'form_id' => 'form-id-123',
            'id' => 'field2',
            'type' => 'text',
            'title' => 'Field 2'
        ]);

        $this->assertDatabaseHas('typeform_fields', [
            'form_id' => 'form-id-123',
            'id' => 'field3',
            'type' => 'boolean',
            'title' => 'Field 3'
        ]);

        $this->assertEquals(3, Field::count());
    }

    /** @test */
    public function it_creates_an_answer_model_for_each_field(){
        $payload = new DummyPayload([
            'fields' => [
                ['id' => 'field1', 'type' => 'boolean', 'title' => 'Field 1'],
                ['id' => 'field2', 'type' => 'text', 'title' => 'Field 2'],
            ], 'answers' => [
                [
                    'field' => ['id' => 'field1'],
                    'type' => 'boolean_abc',
                    'boolean_abc' => 'a_value_here'
                ],
                [
                    'field' => ['id' => 'field2'],
                    'type' => 'boolean_abcd',
                    'boolean_abcd' => 'a_value2_here'
                ]
            ]
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_answers', [
            'field_id' => 'field1',
            'response_id' => 'some-response-id',
            'type' => 'boolean_abc',
            'answer' => 'a_value_here'
        ]);

        $this->assertDatabaseHas('typeform_answers', [
            'field_id' => 'field2',
            'response_id' => 'some-response-id',
            'type' => 'boolean_abcd',
            'answer' => 'a_value2_here'
        ]);

        $this->assertEquals(2, Answer::count());
    }

    /** @test */
    public function it_does_not_create_an_answer_model_if_the_field_is_not_found(){
        $payload = new DummyPayload([
            'fields' => [
                ['id' => 'field1', 'type' => 'boolean', 'title' => 'Field 1'],
            ], 'answers' => [
                [
                    'field' => ['id' => 'field1'],
                    'type' => 'boolean_abc',
                    'boolean_abc' => 'a_value_here'
                ],
                [
                    'field' => ['id' => 'field2'],
                    'type' => 'boolean_abcd',
                    'boolean_abcd' => 'a_value2_here'
                ]
            ]
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_answers', [
            'field_id' => 'field1',
            'response_id' => 'some-response-id',
            'type' => 'boolean_abc',
            'answer' => 'a_value_here'
        ]);

        $this->assertEquals(1, Answer::count());
    }

    /** @test */
    public function it_does_not_create_an_answer_model_if_no_answer_found(){
        $payload = new DummyPayload([
            'fields' => [
                ['id' => 'field1', 'type' => 'boolean', 'title' => 'Field 1'],
                ['id' => 'field2', 'type' => 'text', 'title' => 'Field 2'],
            ], 'answers' => [
                [
                    'field' => ['id' => 'field1'],
                    'type' => 'boolean_abc',
                    'boolean_abc' => 'a_value_here'
                ]
            ]
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        $this->assertDatabaseHas('typeform_answers', [
            'field_id' => 'field1',
            'response_id' => 'some-response-id',
            'type' => 'boolean_abc',
            'answer' => 'a_value_here'
        ]);

        $this->assertEquals(1, Answer::count());
    }

    /** @test */
    public function it_returns_null_if_the_payload_activity_instance_id_returns_null(){
        $payload = $this->prophesize(Payload::class);
        $payload->activityInstanceId()->shouldBeCalled()->willReturn(null);

        $handler = new ResponseHandler();
        $this->assertNull($handler->handle($payload->reveal()));
    }

    /** @test */
    public function it_returns_null_if_the_payload_module_instance_id_returns_null(){
        $payload = $this->prophesize(Payload::class);
        $payload->activityInstanceId()->shouldBeCalled()->willReturn(1);
        $payload->moduleInstanceId()->shouldBeCalled()->willReturn(null);

        $handler = new ResponseHandler();
        $this->assertNull($handler->handle($payload->reveal()));
    }

    /** @test */
    public function it_returns_null_if_the_payload_submitter_id_returns_null(){
        $payload = $this->prophesize(Payload::class);
        $payload->activityInstanceId()->shouldBeCalled()->willReturn(1);
        $payload->moduleInstanceId()->shouldBeCalled()->willReturn(1);
        $payload->submitterId()->shouldBeCalled()->willReturn(null);

        $handler = new ResponseHandler();
        $this->assertNull($handler->handle($payload->reveal()));
    }

    /** @test */
    public function it_fires_an_event_for_a_new_response_created(){
        Event::fake(NewResponse::class);

        $payload = new DummyPayload([
            'fields' => [], 'answers' => []
        ]);

        $handler = new ResponseHandler();
        $handler->handle($payload);

        Event::assertDispatched(NewResponse::class, function($event) {
            return $event instanceof NewResponse && $event->response->id === 'some-response-id';
        });
    }
}

class DummyPayload extends Payload
{

    public function formId()
    {
        return 'form-id-123';
    }

    public function responseId()
    {
        return 'some-response-id';
    }

    public function submitterId()
    {
        return 1;
    }

    public function submittedAt()
    {
        return Carbon::create(0001, 01, 01, 00, 00,  00);
    }

    public function activityInstanceId()
    {
        return 3;
    }

    public function fields()
    {
        return $this->property('fields');
    }

    public function answers()
    {
        return $this->property('answers');
    }

    public function moduleInstanceId()
    {
        return 4;
    }
}
