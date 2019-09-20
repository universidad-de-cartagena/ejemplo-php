# Ejemplo PHP

## Links para documentacion original

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

## Links utiles

- [Estructura de carpetas](https://laravel.com/docs/5.8/structure)
- [Buen ejemplo php-fpm en docker](https://github.com/BretFisher/php-docker-good-defaults/blob/master/Dockerfile#L44)

## Docker

git clean -xd -e .env -e .vscode/ -e vendor/ -n

```shell
FROM php:7.2-cli
RUN docker-php-source extract \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable pdo_mysql \
    && docker-php-source delete
```

```shell
docker run --rm -it -v $PWD:/app -w /app -p 8000:8000 --user 1000:1000 php:7.2-cli bash
export PS1='php:\w\$ '

docker run --rm -it -v $PWD:/app -w /app --user 1000:1000 composer:1.7.2 bash
export PS1='composer:\w\$ '
```

// Create a test in the Feature directory...
php artisan make:test UserTest

// Create a test in the Unit directory...
php artisan make:test UserTest --unit

If you define your own setUp / tearDown methods within a test class, be sure to call the respective parent::setUp() / parent::tearDown() methods on the parent class.

Logging XML, HTML, Coverage
https://phpunit.readthedocs.io/en/8.3/logging.html#test-results-xml

Coverage
https://phpunit.readthedocs.io/en/8.3/code-coverage-analysis.html
https://phpunit.readthedocs.io/en/8.3/textui.html#command-line-options
https://phpunit.readthedocs.io/en/8.3/configuration.html#the-logging-element

Database
https://phpunit.de/manual/6.5/en/database.html

Some managed database providers such as Heroku provide a single database "URL" that contains all of the connection information for the database in a single string. An example database URL may look something like the following:

mysql://root:password@127.0.0.1/forge?charset=UTF-8
These URLs typically follow a standard schema convention:

driver://username:password@host:port/database?options
For convenience, Laravel supports these URLs as an alternative to configuring your database with multiple configuration options. If the url (or corresponding DATABASE_URL environment variable) configuration option is present, it will be used to extract the database connection and credential information.

vendor/bin/phpunit --log-junit tests.xml

docker run --rm -it --init -v $PWD:/app:ro -w /app -p 5555:80 node:10.16.3-alpine sh -c "npm install -g xunit-viewer && xunit-viewer --watch --results=tests.xml --port=80"
