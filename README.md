# Auth Micro service and Api gateway

This is an, in progress, micro service to manage authentication using APISIX and laravel 12
Video to setup: https://www.youtube.com/watch?v=Roy_c9Th1Ek

## Installation of APISIX and microservice to manage the authentication

- clone the repository

```bash
git clone https://github.com/nur-university/ms2024-m6-act1-rodrigopaco1986
```

- access to the folder and run the containers
```bash
cd folder
docker compose up -d
```

- Access to the terminal of the auth-app container and run the command to push routes/consumers to apisix
```bash
docker exec -it auth-app bash
php artisan apisix:push-routes
```

Run command to add routes to APISIX inside the auth-app container
```bash
php artisan auth:setup
```

Copy User Key and Client Secret to later make requests to api gateways.
Default credentials are:
username: admin@gmail.com
password: Admin123_

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