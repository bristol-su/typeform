<?php

namespace BristolSU\Module\Typeform\Http\Controllers\Participant;

use BristolSU\Module\Typeform\Http\Controllers\Controller;
use BristolSU\Module\Typeform\Models\Answer;
use BristolSU\Module\Typeform\Typeform\Client;
use BristolSU\Support\Activity\Activity;
use BristolSU\Support\ModuleInstance\Contracts\Connection\ModuleInstanceServiceRepository;
use BristolSU\Support\ModuleInstance\ModuleInstance;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DownloadFileController extends Controller
{

    public function download(Activity $activity, ModuleInstance $moduleInstance, Answer $answer)
    {
        $this->authorize('view-form');
        
        /** @var Client $client */
        $client = app(Client::class,
            ['connector' => app(ModuleInstanceServiceRepository::class)->getConnectorForService('typeform', $moduleInstance->id)]
        );
        
        $tmpFile = tempnam(sys_get_temp_dir(), $answer->response_id . '_');
        
        $typeformResponse = $client->downloadFileFromAnswer($answer, $tmpFile);
        
        if($typeformResponse->getStatusCode() === 200) {
            $response = \Illuminate\Support\Facades\Response::download($tmpFile);
            $response->headers->set('content-disposition', $typeformResponse->getHeader('content-disposition'));
            $response->headers->set('X-Vapor-Base64-Encode', 'True');
            return $response;
        }
        
        throw new HttpException(404, 'Could not find the file');
    }
    
}