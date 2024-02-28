phpstan:
	composer phpstan

fix-cs:
	composer fix-cs

test:
	vendor/bin/phpunit
	vendor/bin/behat
