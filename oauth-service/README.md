# OAuth2 Server API Service

A simple OAuth2 Server API service where users can authenticate and authorize themselves, and then can access the protected APIs accross all the services including our `recipe-service`.

## Application Architecture

> All the key-design points of the application are already described in the `recipe-service`. It follows all the same principles with the following additional packages:

##### Authentication via OAuth2:

To access the protected data enpoints at recipes-service, it requires an OAuth2 Bearer `token`. which can be generated using this library [oauth2-server-php](https://github.com/bshaffer/oauth2-server-php), It's an implementation of OAuth 2.0 Server. I've implemented to support the `Client Credentails` and `User Credentails` Grants for the authentication of users who want to access the protected routes.

The relevant credentials are:

1st party client (**grant_type**: client_credentials):

```plain
client id: testclient
client secret: testpass
```

3rd party client (**grant_type**: user_credentials):

```plain
client id: testclient
client secret: testpass
username: testuser
password: testpass
```

###### Client Credentials Grant:

To get the access token with the client credentials grant, we use the `client_credentials` grant type against the `oauth/token` endpoint:

```
$ curl -X POST http://localhost:8001/token \
    -H "Accept: application/json" \
    -H "Content-Type: application/json" \
    -d '{
    "grant_type": "client_credentials",
    "client_id": "testclient",
    "client_secret": "testpass",
    }'
```


If it succeeds, you will get a response containing JSON like this:

```
{
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c",
    "expires_in": 3600,
    "token_type": "Bearer",
    "scope": null
}
```

You can then access the protected API at the `recipe-service` by including the access_token in the Authorization header:

```
$ curl -X DELETE http://recipe-service/recipes\123 \
    -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c" \
    -H "Accept: application/json"
```

##### Access Token Format "JWT":

JWT (JSON Web Token) is a JSON-based open standard (RFC 7519) for creating access tokens that assert some number of claims. The above `oauth/token` endpoint generates the access token in this format. Its been generated using the private key which is being stored at `/server/keys` directory. Now all the protected services endpoints can verify the validaty of the jwt tokens without making any calls to this oauth service. all they need is the public_key stored in the `/server/keys` directory.




