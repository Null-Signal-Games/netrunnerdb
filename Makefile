install:
	composer install
	php bin/console assets:install --symlink web

phpstan:
	php vendor/bin/phpstan --memory-limit=-1 --verbose analyze src --level 2

test:
	vendor/bin/phpstan analyze src --level 7
