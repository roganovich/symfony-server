# Symfony Server APP
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
## Doctrine
```bash
php bin/console doctrine:schema:validate
```
## Migrations
```bash
php bin/console doctrine:migrations:diff
```
```bash
php bin/console doctrine:migrations:migrate
```
## Routes
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
## Dispatching the Message
Команда для получения сообщений
```bash
php bin/console messenger:consume async -vv
```
Выполните команду symfony server:log и добавьте комментарий к любой конференции,
чтобы увидеть в терминале, как один за другим происходят переходы состояний.
```bash
php bin/console messenger:failed:show
```
## Log
```bash
php bin/console server:log
```
## Workflow
Выполните следующую команду, чтобы получить список сервисов, в имени которых содержится "workflow":
```bash
php bin/console debug:container workflow
```
## Cache
Очистка HTTP-кеша для тестирования
```bash
php bin/console cache:clear
```
```bash
rm -rf var/cache/dev/http_cache/
```
## Command
```bash
php bin/console make:command app:step:info
```
```bash
php bin/console app:step:info
```

