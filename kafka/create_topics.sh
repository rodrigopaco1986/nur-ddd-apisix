#!/bin/bash
set -e

echo "Creating topics…"


# Create topics (ignore if they already exist)
#-----------PATIENTS---------
kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic patient.created \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic patient.updated \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic patient.deleted \
  --partitions 1 \
  --replication-factor 1 \
  || true

#-----------SERVICES---------
kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic service.created \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic service.updated \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic service.deleted \
  --partitions 1 \
  --replication-factor 1 \
  || true


#-----------SALES---------
kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic order.created \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic invoice.created \
  --partitions 1 \
  --replication-factor 1 \
  || true

kafka-topics.sh \
  --bootstrap-server kafka:9092 \
  --create \
  --topic payment.make \
  --partitions 1 \
  --replication-factor 1 \
  || true


echo "Topics created (or already existed)."
