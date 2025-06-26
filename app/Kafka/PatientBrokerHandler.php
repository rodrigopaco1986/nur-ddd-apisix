<?php

namespace App\Kafka;

use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\MessageConsumer;
use Throwable;

class PatientBrokerHandler
{
    protected array $topicMap;

    public function __construct()
    {
        $this->topicMap = config('kafka_topics_consumers.patients', []);
    }

    /**
     * The main entry point for all consumed messages.
     * It finds the correct handler for the message's topic and invokes it.
     *
     * @param  \Junges\Kafka\Contracts\KafkaConsumerMessage  $message
     */
    public function __invoke(ConsumerMessage $message, MessageConsumer $consumer): void
    {
        $topicName = $message->getTopicName();

        // Check if a handler is registered for the received topic.
        if (! isset($this->topicMap[$topicName])) {
            Log::warning("No Kafka handler registered for topic [{$topicName}]. Ignoring message.");

            return;
        }

        $handlerClass = $this->topicMap[$topicName];

        if (! class_exists($handlerClass)) {
            Log::error("Kafka handler class [{$handlerClass}] for topic [{$topicName}] does not exist.");

            return;
        }

        try {

            $handler = app($handlerClass);
            $handler($message);

        } catch (Throwable $e) {
            Log::error("Exception occurred while handling Kafka message for topic [{$topicName}].", [
                'handler' => $handlerClass,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
