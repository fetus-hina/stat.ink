STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/fest.ink/fest.js/*.js)

RESOURCE_TARGETS=resources/.compiled/fest.ink/fest.css.gz \
	resources/.compiled/fest.ink/fest.js.gz \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz

all: \
	composer.phar \
	vendor \
	node_modules \
	config/google-analytics.php \
	config/twitter.php \
	config/cookie-secret.php \
	config/db.php \
	resource \
	migrate-db

resource: $(RESOURCE_TARGETS)

vendor: composer.phar
	php composer.phar install

node_modules:
	npm install

check-style: vendor
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)
	vendor/bin/check-author.php --php-files $(STYLE_TARGETS)

fix-style: vendor
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean: clean-resource
	rm -rf \
		composer.phar \
		node_modules \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		web/assets/*

composer.phar:
	curl -sS https://getcomposer.org/installer | php

resources/.compiled/fest.ink/fest.js.gz: node_modules $(JS_SRCS)
	./node_modules/.bin/gulp fest-ink-js

resources/.compiled/fest.ink/fest.css.gz: node_modules resources/fest.ink/fest.less
	./node_modules/.bin/gulp fest-ink-css

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.js
	./node_modules/.bin/gulp gh-fork

migrate-db: vendor config/db.php
	./yii migrate/up --interactive=0

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

config/db.php: vendor
	test -f config/db.php || ./yii secret/db
	touch config/db.php

config/google-analytics.php:
	echo '<?php' > config/google-analytics.php
	echo 'return "";' >> config/google-analytics.php

.PHONY: all resource check-style fix-style clean clean-resource FORCE
