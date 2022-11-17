Description
------------

For now this project is mainly for educational.
I want to use technologies, which I learn.

Functions
----------------
For now there are some simple functions:
    * You can manage your goals
    * manage categories
    * create schedule for goals
    * checking tasks
    * simple statistics
    * admin panel

Technologies / Components
----------------
    * Symfony 5.4
    * Stimulus Chartjs component
    * EasyAdmin
    * https://scripture.api.bible/
    * Webpack


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

to compile css

```sh
yarn 
yarn watch
```

change your connection for database in .evn file
set WEB_API_TOKEN if you need random bible verse
I use website https://scripture.api.bible/


if you need sample data run 

```sh
php bin/console doctrine:fixtures:load
```

then you can run 

```sh
php bin/console server:start 
```

I hope you can now enjoy my simple website on http://127.0.0.1:8000/
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