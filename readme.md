*Lumen with oauth2-server*

*This example is created with laravel lumen but its decoupled so it can be used in every app with small changes*

1. Database tables
```sql
CREATE TABLE public.members
(
  id integer NOT NULL DEFAULT nextval('members_id_seq'::regclass),
  username text NOT NULL,
  password text NOT NULL,
  created_at timestamp(0) without time zone,
  updated_at timestamp(0) without time zone,
  CONSTRAINT members_pkey PRIMARY KEY (id)
)

CREATE TABLE public.oauth_access_tokens
(
  id integer NOT NULL DEFAULT nextval('oauth_access_tokens_id_seq'::regclass),
  member_id integer NOT NULL,
  access_token text NOT NULL,
  client_id character varying(100) NOT NULL,
  expiry_timestamp integer NOT NULL,
  CONSTRAINT oauth_access_tokens_pkey PRIMARY KEY (id),
  CONSTRAINT oauth_access_tokens_access_token_unique UNIQUE (access_token)
)

CREATE TABLE public.oauth_access_tokens_scopes
(
  id integer NOT NULL DEFAULT nextval('oauth_access_tokens_scopes_id_seq'::regclass),
  access_token_id integer NOT NULL,
  scope_id text,
  CONSTRAINT oauth_access_tokens_scopes_pkey PRIMARY KEY (id),
  CONSTRAINT oauth_access_tokens_scopes_access_token_id_foreign FOREIGN KEY (access_token_id)
      REFERENCES public.oauth_access_tokens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)

CREATE TABLE public.oauth_clients
(
  id integer NOT NULL DEFAULT nextval('oauth_clients_id_seq'::regclass),
  name character varying(100) NOT NULL,
  CONSTRAINT oauth_clients_pkey PRIMARY KEY (id)
)

CREATE TABLE public.oauth_refresh_tokens
(
  id integer NOT NULL DEFAULT nextval('oauth_refresh_tokens_id_seq'::regclass),
  refresh_token text NOT NULL,
  access_token text NOT NULL,
  expiry_timestamp integer NOT NULL,
  CONSTRAINT oauth_refresh_tokens_pkey PRIMARY KEY (id),
  CONSTRAINT oauth_refresh_tokens_refresh_token_unique UNIQUE (refresh_token)
)

CREATE TABLE public.oauth_scopes
(
  id integer NOT NULL DEFAULT nextval('oauth_scopes_id_seq'::regclass),
  name character varying(100) NOT NULL,
  CONSTRAINT oauth_scopes_pkey PRIMARY KEY (id)
)
```

If you want you can use the lumen migrations. 
Upon DB tables creation, some of the tables are seeded with some default clients like my two apps, some members and some auth scopes.

```php
DB::table('oauth_clients')->insert([
    ['name' => 'app1'], ['name' => 'app2']
]);


DB::table('oauth_scopes')->insert([
    ['name' => 'member'], ['name' => 'client'], ['name' => 'partner']
]);

DB::table('members')->insert([
    ['username' => 'vladetd', 'password' => Hash::make('password')]
]);
```
In the `routes/web.php` file a route is created for issuing access tokens.
In the `app/Providers/AppServiceProvider` the Auth Server is booted. The Auth server has some dependencies which are implemented in `app/Services/Oauth`.
Those are the repositories and the entities. 

The Auth Server is booted and grants are enabled with:
```php
        $authServer = new AuthorizationServer(
            new ClientRepository($db),
            new AccessTokenRepository($db),
            new ScopeRepository($db),
            file_get_contents(storage_path('private.key')),
            Key::loadFromAsciiSafeString("def000002000cc73e607027287ab92eddb32c88b399a582ce9306df53b210d09d58a1483afc818ef0b110fa01ab9c74f6607a73a1c846e1859bdf69aec4574ef242722b3")
        );

        $resourceServer = new ResourceServer(
            new AccessTokenRepository($db),
            file_get_contents(storage_path('public.key'))
        );

        $grant = new \League\OAuth2\Server\Grant\PasswordGrant(
            new UserRepository($db),
            new RefreshTokenRepository($db)
        );

        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month
        $authServer->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );


        $refreshTokenGrant = new RefreshTokenGrant(
            new RefreshTokenRepository($db)
        );
        $authServer->enableGrantType(
            $refreshTokenGrant
        );
```

Hitting the route `POST: auth/access_token` will call the `app/Http/Controllers/AuthController::issueAccessToken`. Issuing the access token is done by calling `$oauthService->respondToAccessTokenRequest($psrRequest, $psrResponse);`

This route can serve the password and refresh token grant based on the `grant_type` parameter in the request.


To ensure that the other routes can be accessed only by authorized members, the best way is to create a middleware like `app/Http/Middleware/OauthMiddleware`. The middleware via the resource server checks if the Authorization Header is correct `$this->resourceServer->validateAuthenticatedRequest($psrRequest);`


In the same way, other grants can be enabled on the auth server.