Description
------------

For now this project is strictly educational.
I will try to use technologies which I learn.

Functions
----------------

For now there are some simple functions:
    - You can manage your goals
    - manage categories
    - create schedule for goals
    - checking tasks
    - simle statistics
    - login admin

Technolgies / Components
----------------
    - Symfony 5.4
    - Stimulus Chartjs component
    - EasyAdmin
    - https://scripture.api.bible/
    - Webpack


Instalation
----------------

In order to install

```sh
git clone https://github.com/pawelwaw-git/letsPlan.git
```
then use composer and yarn

```sh
compser install 
```

to compile css use

```sh
yarn 
yarn watch
```

change your connection for database in .evn file
you also need to set key for webapi from https://scripture.api.bible/, if not there is no problem


if you need sample data run 

```sh
php bin/console doctrine:fixtures:load
```

then you can run 

```sh
php bin/console server:start 
```

I hope you can now enjoy my simple website on http://127.0.0.1:8000/
You can login using data 
admin@example.com
adminpass to login
if you used fixtures

if not you can creata use 
please use commands 

```
php bin/console make:user
```

and to generate password

```
php bin/console security:hash-password
```

and add roles ["ROLE_USER", "ROLE_ADMIN"] to created user
then is no registration form yet.