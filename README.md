## Описание

Backend-часть тестового задания для RR на PHP & Laravel

## Запуск

Скопировать конфиг

```bash
cp .env.example .env
```

Сгенерировать ключ приложения

```bash
php artisan key:generate
```

Накатить миграции и сгенерировать конфиг

```bash
php artisan migrate --seed
```

По дефолту тесты выполняются с использованием SQLite, в случае его отсутствия нужно закомментировать в файле `phpunit.xml` строки

```
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

Запуск тестов

```ba
php artisan test
```
