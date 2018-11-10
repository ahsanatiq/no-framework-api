# OAuth2 API Service

A simple OAuth2 Server API service where users can authenticate and authorize themselves, and then can access the protected APIs accross all the services.

## Application Architecture

All the key-design points of the application are already described in the `recipe-service`. It follows all the same principles and packages except the following:

##### Authentication via OAuth2:

To access the protected data enpoints at recipes-service, it requires an OAuth2 Bearer `token`. which can be generated using this library [oauth2-server-php](https://github.com/bshaffer/oauth2-server-php), It's an implementation of OAuth 2.0 Server. I've implemented to support the `Client Credentails` and `Password` Grants for the authentication of users who want to access the protected routes.

