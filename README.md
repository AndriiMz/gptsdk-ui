## Create Auth0 application on PROD
```
./auth0 apps create \
--name "GptSdk" \
--type "regular" \
--auth-method "post" \
--callbacks "http://<url>/callback" \
--logout-urls "http://<url>" \
--reveal-secrets \
--no-input \
--json > .auth0.app.json
```


## Local Installation
#### Preparation:
before start make sure you have php min version 8 
and node min version 18

1. Create .env from .env.example.
2. Create .auth0.api.json. Ask teammate to share.
3. Run `composer install` and `npm i`
4. Run `docker compose up -d`
5. Run `npm run dev`
6. Enjoy

## Server Installation 
