STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/stat.ink/main.js/*.js)
GULP=./node_modules/.bin/gulp
VENDOR_SHA256=$(shell sha256sum -t composer.lock | awk '{print $$1}')

RESOURCE_TARGETS_MAIN=\
	resources/.compiled/activity/activity.js \
	resources/.compiled/app-link-logos/festink.png \
	resources/.compiled/app-link-logos/ikadenwa.png \
	resources/.compiled/app-link-logos/ikalog.png \
	resources/.compiled/app-link-logos/ikanakama.png \
	resources/.compiled/app-link-logos/ikarec-en.png \
	resources/.compiled/app-link-logos/ikarec-ja.png \
	resources/.compiled/app-link-logos/splatnet.png \
	resources/.compiled/counter/counter.css \
	resources/.compiled/counter/counter.js \
	resources/.compiled/dseg/dseg14.css \
	resources/.compiled/dseg/fonts/DSEG14Classic-Italic.ttf \
	resources/.compiled/dseg/fonts/DSEG14Classic-Italic.woff \
	resources/.compiled/flot-graph-icon/jquery.flot.icon.js \
	resources/.compiled/gears/calc.js \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js \
	resources/.compiled/ip-version/badge.css \
	resources/.compiled/slack/slack.js \
	resources/.compiled/stat.ink/active-reltime.js \
	resources/.compiled/stat.ink/battle-edit.js \
	resources/.compiled/stat.ink/battle-input.css \
	resources/.compiled/stat.ink/battle-input.js \
	resources/.compiled/stat.ink/battle-thumb-list.css \
	resources/.compiled/stat.ink/battles-simple.css \
	resources/.compiled/stat.ink/blackout-hint.css \
	resources/.compiled/stat.ink/blackout-hint.js \
	resources/.compiled/stat.ink/downloads.css \
	resources/.compiled/stat.ink/favicon.png \
	resources/.compiled/stat.ink/main.css \
	resources/.compiled/stat.ink/main.js \
	resources/.compiled/stat.ink/no-image.png \
	resources/.compiled/stat.ink/session-calendar.js \
	resources/.compiled/stat.ink/swipebox-runner.js \
	resources/.compiled/stat.ink/user-miniinfo.css \
	resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css \
	resources/.compiled/stat.ink/weapons-use.js \
	resources/paintball/paintball.css \
	web/static-assets/cc/cc-by.svg \
	web/static-assets/cc/cc-by.svg.br \
	web/static-assets/cc/cc-by.svg.gz

RESOURCE_TARGETS=\
	$(RESOURCE_TARGETS_MAIN) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.br) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.gz) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.br) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.gz) \

VENDOR_ARCHIVE_FILE=runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz
VENDOR_ARCHIVE_SIGN=runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz.asc

SIMPLE_CONFIG_TARGETS=\
	config/amazon-s3.php \
	config/backup-gpg.php \
	config/backup-s3.php \
	config/debug-ips.php \
	config/google-adsense.php \
	config/google-analytics.php \
	config/google-recaptcha.php \
	config/lepton.php \
	config/twitter.php

all: init migrate-db

init: \
	composer.phar \
	composer-update \
	composer-plugin \
	vendor \
	vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/cookie-secret.php \
	config/db.php \
	resource

init-by-archive: \
	composer.phar \
	composer-update \
	composer-plugin \
	vendor-by-archive \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/cookie-secret.php \
	config/db.php \
	resource

test: init
	./composer.phar exec codecept run

docker: init-by-archive migrate-db
	sudo docker build -t jp3cki/statink .

ikalog: all runtime/ikalog runtime/ikalog/repo runtime/ikalog/winikalog.html
	cd runtime/ikalog/repo && git fetch --all --prune && git rebase origin/master
	./yii ikalog/update-ikalog
	./yii ikalog/update-winikalog

resource: $(RESOURCE_TARGETS)

composer-update: composer.phar
	./composer.phar self-update
	touch -r composer.json composer.phar

composer-plugin: composer.phar
	grep '"fxp/composer-asset-plugin"' ~/.composer/composer.json >/dev/null || ./composer.phar global require 'fxp/composer-asset-plugin:^1.1'
	grep '"hirak/prestissimo"' ~/.composer/composer.json >/dev/null && ./composer.phar global remove 'hirak/prestissimo' || true
	./composer.phar global update -vvv

vendor: composer.phar composer.lock
	php composer.phar install --prefer-dist --profile
	touch -r composer.lock vendor

node_modules: package.json
	npm install

check-style: vendor
	vendor/bin/phpcs --standard=phpcs-customize.xml --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 $(STYLE_TARGETS)
	vendor/bin/check-author.php --php-files $(STYLE_TARGETS) messages migrations

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

vendor-archive: $(VENDOR_ARCHIVE_FILE) $(VENDOR_ARCHIVE_SIGN)
	rsync -av --progress \
		$(VENDOR_ARCHIVE_FILE) $(VENDOR_ARCHIVE_SIGN) \
		statink-src-archive@src-archive.stat.ink:public_html/vendor/

vendor-by-archive: download-vendor-archive
	gpg --verify $(VENDOR_ARCHIVE_SIGN)
	tar -Jxf $(VENDOR_ARCHIVE_FILE)
	touch -r composer.lock vendor

download-vendor-archive: runtime/vendor-archive
	test -e $(VENDOR_ARCHIVE_FILE) || curl -o $(VENDOR_ARCHIVE_FILE) -sS https://src-archive.stat.ink/vendor/vendor-$(VENDOR_SHA256).tar.xz
	test -e $(VENDOR_ARCHIVE_SIGN) || curl -o $(VENDOR_ARCHIVE_SIGN) -sS https://src-archive.stat.ink/vendor/vendor-$(VENDOR_SHA256).tar.xz.asc

composer.phar:
	curl -sS https://getcomposer.org/installer | php
	touch -r composer.json composer.phar

composer.lock: composer.json composer.phar
	./composer.phar update -vvv
	touch -r composer.json composer.lock

%.br: %.gz
	rm -f $@
	zcat $< | bro --quality 11 --output $@

%.css.gz: %.css
	gzip -9c $< > $@

%.js.gz: %.js
	gzip -9c $< > $@

%.svg.gz: %.svg
	gzip -9c $< > $@

$(GULP): node_modules
	touch $(GULP)

web/static-assets/cc/cc-by.svg:
	mkdir -p `dirname $@` || true
	curl -o $@ http://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by.svg

resources/.compiled/stat.ink/main.js: $(JS_SRCS) $(GULP)
	$(GULP) js --in 'resources/stat.ink/main.js/*.js' --out $@

resources/.compiled/stat.ink/main.css: resources/stat.ink/main.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/battle-thumb-list.css: resources/stat.ink/battle-thumb-list.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/user-miniinfo.css: resources/stat.ink/user-miniinfo.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/swipebox-runner.js: resources/stat.ink/swipebox-runner.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battles-simple.css: resources/stat.ink/battles-simple.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/active-reltime.js: resources/stat.ink/active-reltime.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-edit.js: resources/stat.ink/battle-edit.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-input.js: resources/stat.ink/battle-input.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-input.css: resources/stat.ink/battle-input.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/blackout-hint.js: resources/stat.ink/blackout-hint.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/blackout-hint.css: resources/stat.ink/blackout-hint.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/downloads.css: resources/stat.ink/downloads.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/weapons-use.js: resources/stat.ink/weapons-use.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/session-calendar.js: resources/stat.ink/session-calendar.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css: resources/stat.ink/user-stat-by-map-rule-detail.less $(GULP)
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

resources/.compiled/counter/counter.js: resources/counter/counter.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/counter/counter.css: resources/counter/counter.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/dseg/fonts/DSEG14Classic-Italic.ttf: resources/dseg/DSEG_v030.zip
	mkdir -p resources/.compiled/dseg/fonts
	unzip -joq $< DSEG14/Classic/DSEG14Classic-Italic.ttf -d resources/.compiled/dseg/fonts
	touch -r $< $@

resources/.compiled/dseg/fonts/DSEG14Classic-Italic.woff: resources/dseg/DSEG_v030.zip
	mkdir -p resources/.compiled/dseg/fonts
	unzip -joq $< DSEG14/Classic/DSEG14Classic-Italic.woff -d resources/.compiled/dseg/fonts
	touch -r $< $@

resources/.compiled/dseg/dseg14.css: resources/dseg/dseg14.less $(GULP)
	$(GULP) less --in $< --out $@

resources/dseg/DSEG_v030.zip:
	test -d resources/dseg || mkdir resources/dseg
	curl -o $@ http://www.keshikan.net/archive/DSEG_v030.zip

resources/.compiled/slack/slack.js: resources/slack/slack.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/ip-version/badge.css: resources/ip-version/badge.less $(GULP)
	$(GULP) less --in $< --out $@

resources/paintball/paintball.css: resources/paintball/paintball.less $(GULP)
	$(GULP) less --in $< --out $@

resources/app-link-logos/ikalog.png:
	curl -o $@ 'https://cloud.githubusercontent.com/assets/2528004/17077116/6d613dca-50ff-11e6-9357-9ba894459444.png'

resources/.compiled/app-link-logos/ikalog.png: resources/app-link-logos/ikalog.png
	mkdir -p resources/.compiled/app-link-logos
	convert $< -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikadenwa.png:
	curl -o $@ 'https://ikadenwa.ink/static/img/ika-mark.png'

resources/.compiled/app-link-logos/ikadenwa.png: resources/app-link-logos/ikadenwa.png
	mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/.compiled/app-link-logos/ikanakama.png: resources/app-link-logos/ikanakama.ico
	mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikanakama.ico:
	curl -o $@ $(shell php resources/app-link-logos/favicon.php 'http://ikazok.net/')

resources/.compiled/app-link-logos/splatnet.png: resources/app-link-logos/splatnet.ico
	mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/splatnet.ico:
	curl -o $@ $(shell php resources/app-link-logos/favicon.php 'https://splatoon.nintendo.net/')

resources/.compiled/app-link-logos/ikarec-en.png: resources/app-link-logos/ikarec-en.png
	mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikarec-en.png:
	curl -o $@ $(shell php resources/app-link-logos/googleplay.php ink.pocketgopher.ikarec)

resources/.compiled/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-ja.png
	mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikarec-ja.png:
	curl -o $@ $(shell php resources/app-link-logos/googleplay.php com.syanari.merluza.ikarec)

resources/.compiled/app-link-logos/festink.png: resources/app-link-logos/festink.ico
	mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

migrate-db: vendor config/db.php
	./yii migrate/up --interactive=0
	./yii cache/flush-schema --interactive=0

config/cookie-secret.php: vendor $(SIMPLE_CONFIG_TARGETS)
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

config/db.php: vendor $(SIMPLE_CONFIG_TARGETS)
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

config/lepton.php:
	cp config/lepton.sample.php $@

config/twitter.php:
	cp config/twitter.sample.php $@

runtime/ikalog:
	mkdir -p runtime/ikalog

runtime/ikalog/repo:
	git clone --recursive -o origin https://github.com/hasegaw/IkaLog.git $@

runtime/ikalog/winikalog.html: FORCE
	curl -o $@ 'https://hasegaw.github.io/IkaLog/'

vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php: vendor FORCE
	head -n 815 vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php | tail -n 10 | grep '\\1 \\2' > /dev/null && \
		patch -d vendor/smarty/smarty -p1 -Nst < data/patch/smarty-strip.patch || /bin/true

$(VENDOR_ARCHIVE_SIGN): $(VENDOR_ARCHIVE_FILE)
	gpg -s -u 0xF6B887CD --detach-sign -a $<

$(VENDOR_ARCHIVE_FILE): vendor runtime/vendor-archive
	tar -Jcf $@ $<

runtime/vendor-archive:
	mkdir -p $@ || true

.PHONY: FORCE all check-style clean clean-resource composer-plugin composer-update fix-style ikalog init migrate-db resource vendor-archive vendor-by-archive download-vendor-archive
