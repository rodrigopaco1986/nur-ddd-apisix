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