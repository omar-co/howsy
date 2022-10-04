# Howsy Software Engineer Code Challenge

This solution for the Howsy Code Challenge uses:

- PHP 8.1
- PHPUnit 9
- Mockery 1.5
- PHP DI 6.4
- Docker
- Docker Compose

## Getting started.


1. Clone the repo.
2. Build the container. (this will also install composer dependencies)
```shell
docker-compose build
```
3. Run the container.
```shell
docker-compose up -d
```
4. Run the tests.
```shell
docker-compose exec app ./vendor/bin/phpunit --testdox tests
```
or 
```shell
docker-compose exec app ./vendor/bin/phpunit tests
```

5. Run demo app.
```shell
docker-compose exec app docker-compose exec app php index.php
```

you can customize the contract period changing the variable $period in ./src/index.php:14

```php
$period = 12;
```

6. Evaluate code! :wink: 

