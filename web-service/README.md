# Web API Gateway Service

A simple gateway API service

## Application Architecture

##### Route to other services:

All the routes are defined in the Nginx conf file `docker-nginx/default.conf`.

##### Application Entry Point:

The application entry is pointed at `public` directory, which holds only the `index.php` file and can also have assets files which can be accessed publicly. Other than this, no code or file outside this folder can be accessed directly. The idea is to protect all the files outside this directory.

##### Acceptance Testing:

To test all our application service platform, we've used [Codeception](https://github.com/codeception/codeception), Out of the box it allows us to have all three types of tests i-e unit, functional, and acceptance tests in a unified framework. In our case, we have acceptance tests, which checks the integration & functionality of all the API's. All the test case, are stored in the `tests` directory. and can be run by the following command:

`$ php vendor/bin/codecept run --steps`

