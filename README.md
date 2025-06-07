# Auth Micro service and Api gateway

This is an, in progress, micro service to manage authentication using APISIX, VAULT and laravel 12
Video to setup: 

## Installation of APISIX, VAULT and microservice to manage the authentication

- clone the repository

```bash
git clone https://github.com/rodrigopaco1986/nur-ddd-apisix
```

- access to the folder and run the containers (etcd, apisix, apisix-dashboard, vault, auth-app, webserver, db)
```bash
cd folder
docker compose up -d
```

- access to auth-app container logs to get user key and client secret for default created user.

```bash
docker logs auth-app
```

Find data, like:

|Field|Value|
|---|---|
|Client ID|019721a8-3998-73c5-976b-5cb154251e58|
|User Key|1|
|Client Secret|9cWhvRCkisQR3tEuNZKLt2soP0ChfshWH00nz1Ib|

WARN The client secret will not be shown again, so don't lose it!


- Copy User Key and Client Secret to later make requests to api gateway.
Default credentials are:
username: admin@gmail.com
password: Admin123_


- If you want to create another user, access to the terminal of the auth-app container and run the command to create new user:
```bash
php artisan auth:setup newUserEmail@gmail.com SomePassword
```

- Then run the command, in the same container, to push routes/consumers to apisix
```bash
docker exec -it auth-app bash
php artisan apisix:push-routes
```

## Make request to endpoints

```bash
curl -k -i -X POST http://localhost:9080/api/login \
  -d "grant_type=password" \
  -d 'client_id=<User Key>' \
  -d 'client_secret=<Client Secret>' \
  -d "username=<username>" \
  -d "password=<password>"
```

```bash
curl -i -L http://localhost:9080/api/users \
  -H "Authorization: Bearer <TOKEN FROM API LOGIN>" \
  -H "Accept: application/json"
```

```bash
curl -i -L GET http://localhost:9080/api/posts \
  -H "Authorization: Bearer <TOKEN FROM API LOGIN>" \
  -H "Accept: application/json"
```

## DOCKER SERVICES/PORTS

- Network
  - Internal: apisix
  - External: shared-services-network

|SERVICE NAME|SERVICE DESCRIPTION|INTERNAL PORT|EXTERNAL PORT|URL|Credentials|
|---|---|---|---|---|---|
|etcd|Key-Value store|2379|2379|-|-|
|apisix|API Gateway|9180|9080|-|-|
|apisix-dashboard|Apisix Dashboard|9000|9000|[Admin](http://localhost:9000)|admin:admin|
|vault|Secure secrets|8200|8200|[Admin UI](http://localhost:8200/ui/)|Token:root|
|vault-init|Initial secret keys and policies|-|-|-|-|
|zookeeper|coordinate Kafka brokers|2181|-|-|-|
|kafka|Broker|9092|-|-|-|
|kafka UI|Broker UI|8080|9001|[Link](http://localhost:9001/ui/)|-|
|auth-app|Identity (Laravel passport)|9000|-|[Login Api](http://localhost:9080/api/login)|
|webserver|Web Server (nginx)|80 or 443|8000 or 8443|[Example](http://localhost:9080/sales/order/create)|
|db|Database Server (Mysql)|3306|3307|-|-|
|telescope|Insights for app||-|8444|[Host Link](https://myinvoice.local:8444/telescope)|

<br>
