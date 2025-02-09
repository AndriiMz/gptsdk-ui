## Create Auth0 application on PROD
```
./auth0 apps create \
--name "GptSdk" \
--type "regular" \
--auth-method "post" \
--callbacks "https://app.gpt-sdk.com/callback" \
--logout-urls "https://app.gpt-sdk.com" \
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
5. Run `./artisan key:generate` and `./artisan migrate`
6. Run `npm run dev`
7. Enjoy

## Server Installation
1. Create ssh key. Add to repository settings.
2. Run `ssh-agent bash -c 'ssh-add ~/.ssh/gptsdk-ui_rsa; git clone git@github.com:AndriiMz/gptsdk-ui.git .'`
2. Create .env from .env.example.
3. Create .auth0.api.json. Ask teammate to share.
4. Run `composer install` and `npm i`
5. Run `./artisan key:generate` and `./artisan migrate`
6. Create Auth0
