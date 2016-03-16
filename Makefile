STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/stat.ink/main.js/*.js)
COMPOSER_VERSION=1.0.0-alpha11
GULP=./node_modules/.bin/gulp

RESOURCE_TARGETS_MAIN=\
	resources/.compiled/activity/activity.js \
	resources/.compiled/flot-graph-icon/jquery.flot.icon.js \
	resources/.compiled/gears/calc.js \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js \
	resources/.compiled/stat.ink/favicon.png \
	resources/.compiled/stat.ink/main.css \
	resources/.compiled/stat.ink/main.js \
	resources/.compiled/stat.ink/no-image.png

RESOURCE_TARGETS=\
	$(RESOURCE_TARGETS_MAIN) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.br) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.gz) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.br) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.gz) \

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
	config/backup-gpg.php \
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

node_modules: package.json
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
	curl -sS https://getcomposer.org/installer | php -- --version=$(COMPOSER_VERSION)

%.br: %.gz
	rm -f $@
	zcat $< | bro --quality 11 --output $@

%.css.gz: %.css
	gzip -9c $< > $@

%.js.gz: %.js
	gzip -9c $< > $@

$(GULP): node_modules
	touch $(GULP)

resources/.compiled/stat.ink/main.js: $(JS_SRCS) $(GULP)
	$(GULP) js --in 'resources/stat.ink/main.js/*.js' --out $@

resources/.compiled/stat.ink/main.css: resources/stat.ink/main.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js: resources/gh-fork-ribbon/gh-fork-ribbon.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css: resources/gh-fork-ribbon/gh-fork-ribbon.css $(GULP)
	$(GULP) css --in $< --out $@

resources/.compiled/flot-graph-icon/jquery.flot.icon.js: resources/flot-graph-icon/jquery.flot.icon.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/activity/activity.js: resources/activity/activity.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/gears/calc.js: resources/gears/calc.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/no-image.png: resources/stat.ink/no-image.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/no-image.png resources/.compiled/stat.ink/no-image.png

resources/.compiled/stat.ink/favicon.png: resources/stat.ink/favicon.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/favicon.png resources/.compiled/stat.ink/favicon.png

migrate-db: vendor config/db.php
	./yii migrate/up --interactive=0
	./yii cache/flush-schema --interactive=0

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

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

config/backup-gpg.php:
	echo '<?php'                            >  $@
	echo 'return ['                         >> $@
	echo "    'userId' => '0xBC77B5B8',"    >> $@
	echo '];'                               >> $@

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
