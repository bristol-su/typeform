<?php

namespace BristolSU\Module\Typeform\CompletionConditions;

use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\Completion\Contracts\CompletionCondition;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstance;

class Dummy extends CompletionCondition
{

    public function isComplete($settings, ActivityInstance $activityInstance, ModuleInstance $moduleInstance): bool
    {
        return false;
    }

    public function options(): array
    {
        return [];
    }

    public function name(): string
    {
        return 'Dummy. Always false';
    }

    public function description(): string
    {
        return 'Always returns false';
    }

    public function alias(): string
    {
        return 'dummy';
    }
}