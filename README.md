### Setup develop environment
1. Setup git hooks folder: ```git config core.hooksPath .githooks```, now for every commit you will see cs fixes for the new code.

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
6. Enjoy


## Run Tests
1. npm run test
2. ./artisan test
