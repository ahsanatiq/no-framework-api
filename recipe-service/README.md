# Recipe API Service

A simple recipe API service

## Application Architecture

> Below are the key-design points, and how its been handled & implemented.

##### Packages Depending Management:

[Composer](https://getcomposer.org) is used to manage all the dependencies of the application. All the required packages / libraries are declared in the `composer.json` file. And can be installed by using the `composer install` command.

##### Application Entry Point:

The application entry is pointed at `public` directory, which holds only the `index.php` file and can also have assets files which can be accessed publicly. Other than this, no code or file outside this folder can be accessed directly. The idea is to protect all the files outside this directory.

##### Simple Routing with Middleware Support:

All the requests and how they need to be handled are defined in the `routes/api.php`. We can also attach the middlewares with the routes. In our case, "authentication" middleware is applied to the protected routes. The routing is implemented using [Illuminate Routing](https://github.com/illuminate/routing)


##### Database & Migration:

To interact with the postgres, we've used [Illuminate Database](https://github.com/illuminate/database) aka "Eloquent". It allows us to interact with our database via Object Relational Mapping "ORM".

To migrate & seed the database, we've used framework agnostic package [Phinx](https://github.com/robmorgan/phinx). Over the period of time application code evolves and database also evolves with it. To keep track of the code changes we use source versioning tools like git. With the migration scripts we can also keep track of the database changes. Specially helpful when working in a team environment. When team-mates pull your changes, if they see the migration scripts they can simply run it with the simple command to upgrade their local database schema changes.

##### Class Dependency Injection & Service Container:

For managing class dependencies and performing dependency injection, we've used [Illuminate Container](https://github.com/illuminate/container). With this we can invert the control of dependencies from application to class hence the pattern "Inversion of Control".

##### Simple Application Configuration Management & Environment Variables:

For handling the configuration settings for application, we've used the [Illuminate Config](https://github.com/illuminate/config). All the configurations are stored in the `config` directory. Also used [Symfony Dotenv](https://github.com/symfony/dotenv) to load the variables defined in `.env` file, then access them via getenv() function. This is useful to have different configuration settings for each environment i-e dev, staging, production.

##### Event Based System:

Events provide a simple observer implementation, allowing you to subscribe and listen for various events that occur in your application. Events serve as a great way to decouple various aspects of your application, since a single event can have multiple listeners that do not depend on each other. We've used [Illuminate Events](https://github.com/illuminate/events). Event classes are typically stored in the `app/Events` directory, while their listeners are stored in `app/Listeners`.

##### Contextual Logging:

To understand about what's happening within our application, we've used [Monolog](https://github.com/Seldaek/monolog) library which provides robust logging services that allow you to log messages.

##### Validation of HTTP Request:

To validate your application's incoming HTTP request data, We've used [Illuminate Validation](https://github.com/illuminate/validation), which provides a variety of powerful validation rules.

##### Standard & Consistent Output of API HTTP Response:

To provide a standard & consistent output of API response data, we've used [League Fractal](https://github.com/league/fractal), so we can have presentation and transformation layer for our output data. All the transformation classes are stored in the `app/Transformers` directory. For the pagination of our results from the Eloqent models, we've used [Illuminate Pagination](https://github.com/illuminate/pagination).

##### Unit Testing:

To test our application service, we've used [Codeception](https://github.com/codeception/codeception), Out of the box it allows us to have all three types of tests i-e unit, functional, and acceptance tests in a unified framework. In our case, we have unit tested the core business object "recipe", also written acceptance tests to check the integration & functionality of all the API's. All the test case, are stored in the `tests` directory. and can be run by the following command:

`$ php vendor/bin/codecept run --steps`

##### Message Queue to Redis:

For the implementation of message queue to the redis, we've used [Illuminate Queue](https://github.com/illuminate/queue). When an event happened on the recipes, they are being transferred to the queue in redis with the recipe information. So other services who are interested in these events can subscribe and consume the messages from the redis.

##### Validation of Access Tokens:

To protect our routes, we've applied Authentication middleware `App\Middlewares\Authentication` on them. This will check the presence and validity of the access tokens in the request headers. As our tokens are in the form of JWT, so in-order to check the validity we have used the library [JWT](https://github.com/lcobucci/jwt).

##### Code-standards and style guides:

To maintain the coding standards accross the team I've created the file `phpcs.xml`, in which all the coding standards are defined. and all the code files can be checked according to this file by running the follwoing command:

`$ php vendor/bin/phpcs`


