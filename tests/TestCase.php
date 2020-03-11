<?php

namespace BristolSU\Module\Tests\Typeform;

use BristolSU\Module\Typeform\ModuleServiceProvider;
use BristolSU\Support\Testing\AssertsEloquentModels;
use BristolSU\Support\Testing\CreatesModuleEnvironment;
use BristolSU\Support\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesModuleEnvironment, AssertsEloquentModels;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->createModuleEnvironment('typeform');
    }

    protected function getPackageProviders($app)
    {
        return array_merge(parent::getPackageProviders($app), [
            ModuleServiceProvider::class
        ]);
    }
    
}