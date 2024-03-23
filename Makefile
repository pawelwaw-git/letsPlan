phpstan:
	composer phpstan

fix-cs:
	composer fix-cs

phpunit_test:
	composer phpunit_test
behat_test:
	composer behat_test

build:
	make fix-cs phpunit_test behat_test phpstan
