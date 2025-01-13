PHP_CS_FIXER_IGNORE_ENV = 1

.PHONY: fix
fix: vendor/autoload.php
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php --diff
