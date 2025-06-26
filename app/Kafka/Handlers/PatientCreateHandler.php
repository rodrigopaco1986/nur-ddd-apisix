<?php

namespace App\Kafka\Handlers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\ConsumerMessage;

class PatientCreateHandler
{
    public function __invoke(ConsumerMessage $message)
    {
        $body = $message->getBody();

        $email = $body['email'];
        $password = $body['dni'];

        Log::info('Saving new patient as user listened from kafka...');

        $exitCode = Artisan::call('auth:setup-personal-client', [
            '--email' => $email,
            '--password' => $password,
        ]);

        echo '<pre>';
        print_r($exitCode);
        echo '</pre>';
    }
}
