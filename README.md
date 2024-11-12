# Репозиторий для [публичного урока](https://otus.ru/lessons/symfony/#event-4896) по курсу [Symfony Framework](https://otus.ru/lessons/symfony/)

## Инициализация проекта

1. Запустить контейнеры командой `docker-compose up -d`
2. Войти в контейнер командой `docker exec -it php sh`
3. Установить необходимые пакеты командой `composer install`
4. Инициализировать БД командой `php bin/console doctrine:migrations:migrate `
5. В файле `docs/dashboard.json` лежит описание Dashboard для Grafana, при импорте нужно заменить uid datasource на
корректный

## Запуск эксперимента на получение сообщений

  - Инициализировать счётчики ошибок командой `php bin/console test:error`, на графиках должны появиться по одной
ошибке для каждого пользователя
  - Запустить команду `php bin/console test:consume`

## Запуск эксперимента на отправку сообщений 

  - Запустить фоновую команду `php -dmemory_limit=2G bin/console test:outbox:publish &`
  - Запустить одновременно в фоне две команды
```shell
php bin/console test:produce &
php bin/console test:produce -o &
```

Автор: [Михаил Каморин](mailto:m.v.kamorin@gmail.com)
