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

```sh
yarn & yarn install 
```

change your connection for database in .evn file
you also need to set key for webapi from https://scripture.api.bible/, if not there is no problem


if you need sample data run 

```sh
php bin/console doctrine:fixtures:load
```


