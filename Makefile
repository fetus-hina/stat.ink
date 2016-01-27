STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/stat.ink/main.js/*.js)

RESOURCE_TARGETS=resources/.compiled/stat.ink/main.css.gz \
	resources/.compiled/stat.ink/main.js.gz \
	resources/.compiled/stat.ink/no-image.png \
	resources/.compiled/flot-graph-icon/jquery.flot.icon.js.gz \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css.gz \
	resources/graph-icon/generated/dead/358.png \
	resources/graph-icon/generated/dead/default.png \
	resources/graph-icon/generated/gachi/358.png \
	resources/graph-icon/generated/gachi/default.png \
	resources/graph-icon/generated/killed/358.png \
	resources/graph-icon/generated/killed/default.png

all: init migrate-db

init: \
	composer.phar \
	composer-plugin \
	vendor \
	vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php \
	node_modules \
	config/debug-ips.php \
	config/google-analytics.php \
	config/google-recaptcha.php \
	config/google-adsense.php \
	config/amazon-s3.php \
	config/backup-s3.php \
	config/cookie-secret.php \
	config/backup-secret.php \
	config/db.php \
	resource

docker: all
	sudo docker build -t jp3cki/statink .

ikalog: all runtime/ikalog runtime/ikalog/repo runtime/ikalog/winikalog.html
	cd runtime/ikalog/repo && git fetch --all --prune && git rebase origin/master
	./yii ikalog/update-ikalog
	./yii ikalog/update-winikalog

resource: $(RESOURCE_TARGETS)

composer-plugin: composer.phar
	grep '"fxp/composer-asset-plugin"' ~/.composer/composer.json >/dev/null || ./composer.phar global require 'fxp/composer-asset-plugin:^1.0'

vendor: composer.phar composer.lock
	php composer.phar install --prefer-dist
	touch -r composer.lock vendor

node_modules:
	npm install

check-style: vendor
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 $(STYLE_TARGETS)
	vendor/bin/check-author.php --php-files $(STYLE_TARGETS)

fix-style: vendor
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean: clean-resource
	rm -rf \
		composer.phar \
		node_modules \
		runtime/ikalog \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		web/assets/*

composer.phar:
	curl -sS https://getcomposer.org/installer | php

resources/.compiled/stat.ink/main.js.gz: node_modules $(JS_SRCS)
	./node_modules/.bin/gulp main-js

resources/.compiled/stat.ink/main.css.gz: node_modules resources/stat.ink/main.less
	./node_modules/.bin/gulp main-css

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.js
	./node_modules/.bin/gulp gh-fork

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css.gz: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.css
	./node_modules/.bin/gulp gh-fork-css

resources/.compiled/flot-graph-icon/jquery.flot.icon.js.gz: node_modules resources/flot-graph-icon/jquery.flot.icon.js
	./node_modules/.bin/gulp flot-icon

resources/.compiled/stat.ink/no-image.png: resources/stat.ink/no-image.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/no-image.png resources/.compiled/stat.ink/no-image.png

resources/graph-icon/generated/dead:
	mkdir -p resources/graph-icon/generated/dead

resources/graph-icon/generated/gachi:
	mkdir -p resources/graph-icon/generated/gachi

resources/graph-icon/generated/killed:
	mkdir -p resources/graph-icon/generated/killed

resources/graph-icon/generated/dead/358.png: vendor resources/graph-icon/generated/dead resources/graph-icon/dead.png
	./yii graph-icon/generate resources/graph-icon/dead.png resources/graph-icon/generated/dead

resources/graph-icon/generated/gachi/358.png: vendor resources/graph-icon/generated/gachi resources/graph-icon/gachi.png
	./yii graph-icon/generate resources/graph-icon/gachi.png resources/graph-icon/generated/gachi

resources/graph-icon/generated/killed/358.png: vendor resources/graph-icon/generated/killed resources/graph-icon/killed.png
	./yii graph-icon/generate resources/graph-icon/killed.png resources/graph-icon/generated/killed

resources/graph-icon/generated/dead/default.png: resources/graph-icon/generated/dead resources/graph-icon/dead.png
	pngcrush -l 9 -rem allb resources/graph-icon/dead.png resources/graph-icon/generated/dead/default.png

resources/graph-icon/generated/gachi/default.png: resources/graph-icon/generated/gachi resources/graph-icon/gachi.png
	pngcrush -l 9 -rem allb resources/graph-icon/gachi.png resources/graph-icon/generated/gachi/default.png

resources/graph-icon/generated/killed/default.png: resources/graph-icon/generated/killed resources/graph-icon/killed.png
	pngcrush -l 9 -rem allb resources/graph-icon/killed.png resources/graph-icon/generated/killed/default.png

migrate-db: vendor config/db.php
	./yii migrate/up --interactive=0
	./yii cache/flush-schema --interactive=0

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

config/backup-secret.php: vendor
	test -f config/backup-secret.php || ./yii secret/backup
	touch config/backup-secret.php

config/db.php: vendor
	test -f config/db.php || ./yii secret/db
	touch config/db.php

config/google-analytics.php:
	echo '<?php' > config/google-analytics.php
	echo 'return "";' >> config/google-analytics.php

config/google-recaptcha.php:
	echo '<?php'                >  config/google-recaptcha.php
	echo 'return ['             >> config/google-recaptcha.php
	echo "    'siteKey' => ''," >> config/google-recaptcha.php
	echo "    'secret'  => ''," >> config/google-recaptcha.php
	echo '];'                   >> config/google-recaptcha.php

config/google-adsense.php:
	echo '<?php'                >  config/google-adsense.php
	echo 'return ['             >> config/google-adsense.php
	echo "    'client' => '',"  >> config/google-adsense.php
	echo "    'slot'   => '',"  >> config/google-adsense.php
	echo '];'                   >> config/google-adsense.php

config/amazon-s3.php:
	echo '<?php'                               >  config/amazon-s3.php
	echo 'return ['                            >> config/amazon-s3.php
	echo '    ['                               >> config/amazon-s3.php
	echo "        'name'      => 'Amazon S3'," >> config/amazon-s3.php
	echo "        'endpoint'  => 's3-ap-northeast-1.amazonaws.com'," >> config/amazon-s3.php
	echo "        'accessKey' => '',"          >> config/amazon-s3.php
	echo "        'secret'    => '',"          >> config/amazon-s3.php
	echo "        'bucket'    => '',"          >> config/amazon-s3.php
	echo '    ],'                 	           >> config/amazon-s3.php
	echo '];'                     	           >> config/amazon-s3.php

config/backup-s3.php:
	echo '<?php'                           >  config/backup-s3.php
	echo 'return ['                        >> config/backup-s3.php
	echo "    'endpoint'  => 's3-ap-northeast-1.amazonaws.com'," >> config/backup-s3.php
	echo "    'accessKey' => '',"          >> config/backup-s3.php
	echo "    'secret'    => '',"          >> config/backup-s3.php
	echo "    'bucket'    => '',"          >> config/backup-s3.php
	echo '];'                 	           >> config/backup-s3.php

config/debug-ips.php:
	echo '<?php'                >  config/debug-ips.php
	echo 'return ['             >> config/debug-ips.php
	echo "    '127.0.0.1',"     >> config/debug-ips.php
	echo "    '::1',"           >> config/debug-ips.php
	echo '];'                   >> config/debug-ips.php

runtime/ikalog:
	mkdir -p runtime/ikalog

runtime/ikalog/repo:
	git clone --recursive -o origin https://github.com/hasegaw/IkaLog.git $@

runtime/ikalog/winikalog.html: FORCE
	curl -o $@ 'https://dl.dropboxusercontent.com/u/14421778/IkaLog/download.html'

vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php: vendor FORCE
	head -n 815 vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php | tail -n 10 | grep '\\1 \\2' > /dev/null && \
		patch -d vendor/smarty/smarty -p1 -Nst < data/patch/smarty-strip.patch || /bin/true

.PHONY: all init resource check-style fix-style clean clean-resource migrate-db composer-plugin ikalog FORCE
