# OAuth2 API Service

A simple OAuth2 Server API service where users can authenticate and authorize themselves, and then can access the protected APIs accross all the services.

## Application Architecture

All the key-design points of the application are already described in the `recipe-service`. It follows all the same principles and packages except the following:

##### Authentication & Authorization:

For the authentication & authorization of our users, I've used the library [oauth2-server-php](https://github.com/bshaffer/oauth2-server-php), It's an implementation of OAuth 2.0 Server. I've implemented to support the `Client Credentails` and `Password` Grants for the authentication purpose, so users can access the protected routes on the "recipe-service"

