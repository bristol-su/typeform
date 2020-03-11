<?php

namespace BristolSU\Module\Tests\Typeform\Typeform\Contracts;

use BristolSU\Module\Tests\Typeform\TestCase;
use BristolSU\Module\Typeform\Typeform\Contracts\Payload;

class PayloadTest extends TestCase
{

    /** @test */
    public function property_returns_a_property_from_the_bundle(){
        $bundle = [
            'items' => 4,
            'fields' => [
                'dummy' => 'test'
            ]
        ];
        
        $payload = new DummyPayload($bundle);
        $this->assertEquals(4, $payload->property('items'));
    }

    /** @test */
    public function property_allows_for_dot_notation(){
        $bundle = [
            'items' => 4,
            'fields' => [
                'dummy' => 'test'
            ]
        ];

        $payload = new DummyPayload($bundle);
        $this->assertEquals('test', $payload->property('fields.dummy'));
    }

}

class DummyPayload extends Payload
{

    
    
    public function formId()
    {
        // TODO: Implement formId() method.
    }

    public function responseId()
    {
        // TODO: Implement responseId() method.
    }

    public function submitterId()
    {
        // TODO: Implement submitterId() method.
    }

    public function submittedAt()
    {
        // TODO: Implement submittedAt() method.
    }

    public function activityInstanceId()
    {
        // TODO: Implement activityInstanceId() method.
    }

    public function fields()
    {
        // TODO: Implement fields() method.
    }

    public function answers()
    {
        // TODO: Implement answers() method.
    }

    public function moduleInstanceId()
    {
        // TODO: Implement moduleInstanceId() method.
    }
}