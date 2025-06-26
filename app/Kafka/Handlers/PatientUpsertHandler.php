<?php

namespace App\Kafka\Handlers;

use Junges\Kafka\Contracts\ConsumerMessage;
use Src\Sales\Patient\Domain\Entities\Patient;
use Src\Sales\Patient\Infraestructure\Repositories\PatientRepository;

class PatientUpsertHandler
{
    public function __invoke(ConsumerMessage $message)
    {
        $body = $message->getBody();

        $patient = new Patient(
            $body['user_id'],
            $body['full_name'],
            $body['dni'],
            '',
            $body['phone'],
            $body['email'],
        );

        $patientRepository = new PatientRepository;
        $patientRepository->upsert($patient);
    }
}
