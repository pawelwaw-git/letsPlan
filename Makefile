phpstan:
	composer phpstan

fix-cs:
	composer fix-cs

tests:
	vendor/bin/phpunit
	vendor/bin/behat

build:
	make fix-cs tests phpstan
