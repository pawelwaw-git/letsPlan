Description
------------

For now this project is mainly for educational.
I want to use technologies, which I learn.

Functions
----------------

For now there are some simple functions:
- manage your goals
- manage your categories
- create and schedule goals
- checking tasks
- simple statistics
- admin panel

Technologies / Components
----------------
- Symfony 5.4
- Stimulus Chartjs component
- EasyAdmin
- https://scripture.api.bible
- Webpack


Instalation
----------------

In order to install

```sh
git clone https://github.com/pawelwaw-git/letsPlan.git
```

then build docker images

```sh
docker-compose up -d
```

then login to container
```sh
docker exec -it lets-plan-php bash
```

and use composer and npm in container

```sh
composer install 
npm install
npm run dev
```

change your connection for database in .evn file
set WEB_API_TOKEN if you need random bible verse
I use website https://scripture.api.bible/


if you need sample data run 

```sh
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

then you can run 

I hope you can now enjoy my simple website on http://localhost:8000/
If you used fixtures, you can login using data 
admin@example.com
adminpass to login

if not you can create use 
please use commands 

```
php bin/console make:user
```

and to generate password

```
php bin/console security:hash-password
```

and add roles ["ROLE_USER", "ROLE_ADMIN"] to created user
then is no registration form yet - so you need set via database.

## install php-cs-fixer

```
mkdir -p tools/php-cs-fixer
composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
```

If you want to validate then you can run 

```
tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src
```