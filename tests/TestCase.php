<?php

namespace BristolSU\Module\Tests\Typeform;

use BristolSU\Module\Typeform\ModuleServiceProvider;
use BristolSU\Support\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function alias(): string
    {
        return 'typeform';
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            ModuleServiceProvider::class
        ]);
    }
    
}