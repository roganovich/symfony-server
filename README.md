# SymfonyDocker

## Docker
```bash
DOCKER_BUILDKIT=0 docker-compose -f docker/docker-compose.yml build
```
```bash
DOCKER_BUILDKIT=0 docker-compose -f docker/docker-compose.yml up -d
```
```bash
docker exec -it symfony-server-php-fpm bash
```
```Очистить контейнеры
docker container stop $(docker container ls -aq)
docker container rm $(docker container ls -aq)
```
```bash
php bin/console cache:clear
```

## Doctrine
```bash
php bin/console doctrine:schema:validate
```
### Migrations
```bash
php bin/console doctrine:migrations:diff
```
```bash
php bin/console doctrine:migrations:migrate
```
### Routes
```bash
php bin/console debug:router
```
## Tests
```bash
php bin/phpunit
```
## Fixtures
```bash
php bin/console doctrine:fixtures:load --env=test
```
## Console
```bash
php bin/console list make
```