<?php

// topics to be subscribed and its consumer
// TOPIC => CONSUMER
return [
    'patients' => [
        'patient.created' => \App\Kafka\Handlers\PatientUpsertHandler::class,
        'patient.updated' => null,
        'patient.deleted' => null,
    ],
];
