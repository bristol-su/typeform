<?php

namespace BristolSU\Module\Typeform\Commands;

use BristolSU\Module\Typeform\Models\Webhook;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstance;
use BristolSU\Support\ModuleInstance\Contracts\ModuleInstanceRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateWebhooks extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:link {webhook? : The ID of the webhook to link}';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'Create all pending webhooks';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Set up any webhooks for any typeform modules';

    public function handle(ModuleInstanceRepository $moduleInstanceRepository)
    {
        // If the ID is set, just link this webhook.
        // Otherwise, iterate through each webhook. If not linked, link!
        if($this->argument('webhook')) {
            $webhooks = [Webhook::findOrFail($this->argument('webhook'))];
        } else {
            $webhooks = Webhook::toBeLinked()->get();
        }
        foreach($webhooks as $webhook) {
            $webhook->link();
        }
    }

}