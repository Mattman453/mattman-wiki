# Mattman Wiki
## IMPORTANT INFORMATION FOR DEVELOPMENT
When running the app in dev mode, avoid using windows. If you are on a Windows platform, run within WSL as doing otherwise will result in **extremely long** load times and non-timely reactivity. Though you will need Docker Desktop installed on windows with the feature to use the WSL 2 based engine enabled.
## Running the App (dev)
### 1. Make a copy of `.env.example` and rename it to `.env`
Fill in the MONGODB_CONNECTION_STRING and all email related variables.

*note: this only needs to happen once when first running the app*
### 2. Add permission for directories
Laravel requires R/W access to `bootstrap` and `storage`:
```
chmod -R o+rw bootstrap storage
```
### 3. Build and run the containers
```
docker-compose build app // build the container for running the application
docker-compose up -d     // launch the containers in detached mode
docker-compose ps        // check if things are up and running
```
### 4. Execute into the container
```
docker-compose exec app bash
```
*note: if you are using Git Bash on Windows, you might have to prefix the command with `winpty`*
### 5. Dependency installations and misc. setup
```
composer install
yarn install
php artisan key:generate    // this is required once per setup of this application
                            // which should populate the APP_KEY field in .env
```
*note: make sure you're in the `/var/www` directory while inside the container before running these commands*
### 6. Running the app
```
yarn dev    // for local development, enables HMR (hot module replacement) to quickly reflect changes on the web page
```
You may now access the application at http://localhost:8000 if everything went smoothly

To spin up the application again in the future for local development, make sure you've done:
```
docker-compose up -d            // to spin up the containers
docker-compose exec app bash    // enter the container
yarn dev                        // start the vite server for development
```
### 7. Stop the app
Simply stop the containers by:
```
docker-compose down
```
*note: this can only be ran after you've exited the container*
## Running the App (production)
### 1. Make a copy of `.env.example` and rename it to `.env`
Fill in the MONGODB_CONNECTION_STRING and all email related variables.

*note: this only needs to happen once when first running the app*
### 2. Add permission for directories
Laravel requires R/W access to `bootstrap` and `storage`:
```
chmod -R o+rw bootstrap storage
```
### 3. Build the App
Most of this can be automated using the inbuilt scripts. Run:
```
sh build.sh
```
*note: this will need to be run whenever you wish to update the app. It will automatically grab the newest commit from the branch you are on.*
### 4. Execute into the container
```
docker-compose exec app bash
```
*note: if you are using Git Bash on Windows, you might have to prefix the command with `winpty`*
### 5. First time launch
If you are launching for the first time, you will need to populate the APP_KEY .env variable. Run:
```
php artisan key:generate
```
*note: make sure you're in the `/var/www` directory while inside the container before running these commands*
### 6. Updating the app
Outside the container, run:
```
sh build.sh
```
### 7. Stop the app
Simply stop the containers by:
```
docker-compose down
```
*note: this can only be ran after you've exited the container*
