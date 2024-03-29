<?php

namespace BristolSU\Module\Typeform\CompletionConditions;

use BristolSU\Module\Typeform\Models\Response;
use BristolSU\Support\ActivityInstance\ActivityInstance;
use BristolSU\Support\Completion\Contracts\CompletionCondition;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstance;
use FormSchema\Schema\Form;

class NumberOfResponsesSubmitted extends CompletionCondition
{

    /**
     * Is the condition fully complete?
     *
     * @param array $settings Settings of the completion condition
     * @param ActivityInstance $activityInstance Activity instance to test
     * @param ModuleInstance $moduleInstance Module instance to test
     * @return bool If the condition is complete
     */
    public function isComplete($settings, ActivityInstance $activityInstance, ModuleInstance $moduleInstance): bool
    {
        return Response::forResource($activityInstance->id, $moduleInstance->id)->count() >= ( $settings['number_of_responses'] ?? 1);
    }

    public function percentage($settings, ActivityInstance $activityInstance, ModuleInstance $moduleInstance): int
    {
        $count = Response::forResource($activityInstance->id, $moduleInstance->id)->count();
        $needed = ( $settings['number_of_responses'] ?? 1);

        $percentage = (int) round(($count/$needed) * 100, 0);

        if($percentage > 100) {
            return 100;
        }
        return $percentage;
    }


    /**
     * Options required by the completion condition.
     *
     * This allows for you to get user input to modify the behaviour of this class. For example, you could give an
     * option of a 'number of files' to be approved before the condition is complete.
     *
     * Any settings requested in here will be passed into the percentage or isComplete methods.
     *
     * @return Form
     * @throws \Exception
     */
    public function options(): Form
    {
        return \FormSchema\Generator\Form::make()->withField(
            \FormSchema\Generator\Field::number('number_of_responses')->setLabel('Number of Responses')
                ->setRequired(true)->setValue(1)->setHint('The number of times a user needs to submit a response')
                ->setTooltip('The number of times a user should submit a response before the module is marked as complete. 1 will mark the module as complete on the first submission, 2 on the second etc.')
        )->getSchema();
    }

    /**
     * A name for the completion condition
     *
     * @return string
     */
    public function name(): string
    {
        return 'Number of responses submitted';
    }

    /**
     * A description of the completion condition
     *
     * @return string
     */
    public function description(): string
    {
        return 'Marks the module as complete when a number of responses have been submitted';
    }

    /**
     * The alias of the completion condition
     *
     * @return string
     */
    public function alias(): string
    {
        return 'number_of_responses_submitted';
    }
}
