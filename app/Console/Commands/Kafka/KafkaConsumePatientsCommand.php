<?php

namespace App\Console\Commands\Kafka;

use App\Kafka\PatientBrokerHandler;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;

class KafkaConsumePatientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     *
     * @var string
     */
    protected $signature = 'kafka:consume-patients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts a single Kafka consumer to listen for patients topics.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $topics = array_keys(config('kafka_topics_consumers.patients'));
        $consumerGroupId = config('kafka.consumer_group_id');
        $brokers = config('kafka.brokers');

        if (empty($topics)) {
            $this->error('No topics are configured in config/kafka_consumers.php or provided via --topics option. Exiting.');

            return;
        }

        $this->info('Starting Kafka patient consumer...');
        $this->info("Consumer Group ID: [{$consumerGroupId}]");
        $this->info('Listening on topics: ['.implode(', ', $topics).']');

        // Create a single consumer instance, subscribing to all topics related to patients,
        $consumer = Kafka::consumer($topics, $consumerGroupId)
            ->withBrokers($brokers)
            ->withConsumerGroupId($consumerGroupId)
            ->withHandler(new PatientBrokerHandler)
            ->withAutoCommit(false)
            ->build();

        $consumer->consume();
    }
}
