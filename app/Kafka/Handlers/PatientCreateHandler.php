<?php

namespace App\Kafka\Handlers;

use App\Models\User;
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
            '--user-role' => User::R0LE_PATIENT,
        ]);
    }
}
