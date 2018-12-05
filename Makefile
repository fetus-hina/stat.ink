STYLE_TARGETS := actions assets commands components controllers models
JS_SRCS := $(shell ls -1 resources/stat.ink/main.js/*.js)
GULP := ./node_modules/.bin/gulp
VENDOR_SHA256 := $(shell sha256sum -t composer.lock | awk '{print $$1}')

RESOURCE_TARGETS_MAIN := \
	resources/.compiled/app-link-logos/festink.png \
	resources/.compiled/app-link-logos/ikadenwa.png \
	resources/.compiled/app-link-logos/ikalog.png \
	resources/.compiled/app-link-logos/ikanakama.png \
	resources/.compiled/app-link-logos/ikarec-en.png \
	resources/.compiled/app-link-logos/ikarec-ja.png \
	resources/.compiled/app-link-logos/inkipedia.png \
	resources/.compiled/app-link-logos/nnid.min.svg \
	resources/.compiled/app-link-logos/nnid.min.svg.br \
	resources/.compiled/app-link-logos/nnid.min.svg.gz \
	resources/.compiled/app-link-logos/squidtracks.png \
	resources/.compiled/app-link-logos/switch.min.svg \
	resources/.compiled/app-link-logos/switch.min.svg.br \
	resources/.compiled/app-link-logos/switch.min.svg.gz \
	resources/.compiled/counter/counter.css \
	resources/.compiled/flexbox/flexbox.css \
	resources/.compiled/flot-graph-icon/jquery.flot.icon.js \
	resources/.compiled/gears/calc.js \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js \
	resources/.compiled/irasutoya/inkling.png \
	resources/.compiled/irasutoya/octoling.png \
	resources/.compiled/ostatus/ostatus.min.svg \
	resources/.compiled/ostatus/ostatus.min.svg.br \
	resources/.compiled/ostatus/ostatus.min.svg.gz \
	resources/.compiled/ostatus/remote-follow.js \
	resources/.compiled/slack/slack.js \
	resources/.compiled/stat.ink/active-reltime.js \
	resources/.compiled/stat.ink/agent.js \
	resources/.compiled/stat.ink/battle-edit.js \
	resources/.compiled/stat.ink/battle-input-2.js \
	resources/.compiled/stat.ink/battle-input.css \
	resources/.compiled/stat.ink/battle-summary-dialog.css \
	resources/.compiled/stat.ink/battle-summary-dialog.js \
	resources/.compiled/stat.ink/battle-thumb-list.css \
	resources/.compiled/stat.ink/battle-thumb-list.js \
	resources/.compiled/stat.ink/battle2-players-point-inked.js \
	resources/.compiled/stat.ink/battles-simple.css \
	resources/.compiled/stat.ink/blackout-hint.css \
	resources/.compiled/stat.ink/blackout-hint.js \
	resources/.compiled/stat.ink/downloads.css \
	resources/.compiled/stat.ink/favicon.png \
	resources/.compiled/stat.ink/kd-win.js \
	resources/.compiled/stat.ink/knockout.js \
	resources/.compiled/stat.ink/main.css \
	resources/.compiled/stat.ink/main.js \
	resources/.compiled/stat.ink/no-image.png \
	resources/.compiled/stat.ink/permalink-dialog.js \
	resources/.compiled/stat.ink/private-note.js \
	resources/.compiled/stat.ink/salmon-work-list-config.js \
	resources/.compiled/stat.ink/salmon-work-list-hazard.js \
	resources/.compiled/stat.ink/salmon-work-list.js \
	resources/.compiled/stat.ink/summary-legends.png \
	resources/.compiled/stat.ink/swipebox-runner.js \
	resources/.compiled/stat.ink/user-miniinfo.css \
	resources/.compiled/stat.ink/user-stat-2-nawabari-inked.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-runner.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-stats.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-winpct.js \
	resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css \
	resources/.compiled/stat.ink/weapon2.js \
	resources/.compiled/stat.ink/weapons-use.js \
	resources/.compiled/stat.ink/weapons.js \
	web/static-assets/cc/cc-by.svg \
	web/static-assets/cc/cc-by.svg.br \
	web/static-assets/cc/cc-by.svg.gz

RESOURCE_TARGETS := \
	$(RESOURCE_TARGETS_MAIN) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.br) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.gz) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.br) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.gz) \

VENDOR_ARCHIVE_FILE := runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz
VENDOR_ARCHIVE_SIGN := runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz.asc

SIMPLE_CONFIG_TARGETS := \
	config/amazon-s3.php \
	config/backup-gpg.php \
	config/backup-s3.php \
	config/debug-ips.php \
	config/google-adsense.php \
	config/google-analytics.php \
	config/google-recaptcha.php \
	config/img-s3.php \
	config/lepton.php \
	config/twitter.php

all: init migrate-db

init: \
	composer.phar \
	composer-update \
	vendor \
	vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/version.php \
	config/cookie-secret.php \
	config/db.php \
	resource

init-by-archive: \
	composer.phar \
	composer-update \
	vendor-by-archive \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/version.php \
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

resource: $(RESOURCE_TARGETS) $(ADDITIONAL_LICENSES)

composer-update: composer.phar
	./composer.phar self-update
	touch -r composer.json composer.phar

vendor: composer.phar composer.lock
	php composer.phar install --prefer-dist --profile
	touch -r composer.lock vendor

node_modules: package-lock.json
	npm install
	touch $@

package-lock.json: package.json
	npm update
	touch $@

check-style: vendor node_modules
	node_modules/.bin/updates
	vendor/bin/phpcs --standard=phpcs-customize.xml --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 $(STYLE_TARGETS)
	vendor/bin/check-author.php --php-files $(STYLE_TARGETS) messages migrations

fix-style: vendor node_modules
	node_modules/.bin/updates -u
	vendor/bin/phpcbf --standard=PSR12 --encoding=UTF-8 $(STYLE_TARGETS)

clean: clean-resource
	rm -rf \
		composer.phar \
		node_modules \
		runtime/ikalog \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		resources/maps2/*.png \
		resources/maps2/assets \
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

%.br: %
	bro --quality 11 --force --input $< --output $@
	chmod 644 $@
	touch $@

%.gz: %
	rm -f $@
	zopfli -i15 $<
	chmod 644 $@

%.min.svg: %.svg node_modules
	./node_modules/.bin/svgo --output $@ --input $< -q

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

resources/.compiled/stat.ink/battle-thumb-list.js: resources/stat.ink/battle-thumb-list.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-miniinfo.css: resources/stat.ink/user-miniinfo.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/swipebox-runner.js: resources/stat.ink/swipebox-runner.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battles-simple.css: resources/stat.ink/battles-simple.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/active-reltime.js: resources/stat.ink/active-reltime.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-edit.js: resources/stat.ink/battle-edit.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-input.css: resources/stat.ink/battle-input.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/battle-input-2.js: resources/stat.ink/battle-input-2.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/blackout-hint.js: resources/stat.ink/blackout-hint.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/blackout-hint.css: resources/stat.ink/blackout-hint.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/downloads.css: resources/stat.ink/downloads.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/weapons-use.js: resources/stat.ink/weapons-use.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/weapons.js: resources/stat.ink/weapons.js $(GULP)
	$(GULP) js --in $< --out $@

WEAPON2_JS := $(shell ls -1 resources/stat.ink/weapon2.js/*.js)
resources/.compiled/stat.ink/weapon2.js: $(WEAPON2_JS) $(GULP)
	$(GULP) js --in 'resources/stat.ink/weapon2.js/*.js' --out $@

resources/.compiled/stat.ink/knockout.js: resources/stat.ink/knockout.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-2-nawabari-inked.js: resources/stat.ink/user-stat-2-nawabari-inked.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-2-nawabari-winpct.js: resources/stat.ink/user-stat-2-nawabari-winpct.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-2-nawabari-stats.js: resources/stat.ink/user-stat-2-nawabari-stats.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-2-nawabari-runner.js: resources/stat.ink/user-stat-2-nawabari-runner.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css: resources/stat.ink/user-stat-by-map-rule-detail.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/battle2-players-point-inked.js: resources/stat.ink/battle2-players-point-inked.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/kd-win.js: resources/stat.ink/kd-win.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/agent.js: resources/stat.ink/agent.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-summary-dialog.js: resources/stat.ink/battle-summary-dialog.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/battle-summary-dialog.css: resources/stat.ink/battle-summary-dialog.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/stat.ink/permalink-dialog.js: resources/stat.ink/permalink-dialog.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/private-note.js: resources/stat.ink/private-note.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/salmon-work-list-config.js: resources/stat.ink/salmon-work-list-config.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/salmon-work-list.js: resources/stat.ink/salmon-work-list.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/salmon-work-list-hazard.js: resources/stat.ink/salmon-work-list-hazard.es $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/ostatus/remote-follow.js: resources/ostatus/remote-follow.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/ostatus/ostatus.svg:
	mkdir -p $(dir $@)
	curl -sSL -o $@ 'https://github.com/OStatus/assets/raw/master/ostatus.svg'

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js: resources/gh-fork-ribbon/gh-fork-ribbon.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.css: resources/gh-fork-ribbon/gh-fork-ribbon.css $(GULP)
	$(GULP) css --in $< --out $@

resources/.compiled/flot-graph-icon/jquery.flot.icon.js: resources/flot-graph-icon/jquery.flot.icon.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/flexbox/flexbox.css: resources/flexbox/flexbox.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/gears/calc.js: resources/gears/calc.js $(GULP)
	$(GULP) js --in $< --out $@

resources/.compiled/stat.ink/no-image.png: resources/stat.ink/no-image.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/no-image.png resources/.compiled/stat.ink/no-image.png

resources/.compiled/stat.ink/favicon.png: resources/stat.ink/favicon.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/favicon.png resources/.compiled/stat.ink/favicon.png

resources/.compiled/stat.ink/summary-legends.png: resources/stat.ink/summary-legends.png
	mkdir -p resources/.compiled/stat.ink || /bin/true
	pngcrush -rem allb -l 9 resources/stat.ink/summary-legends.png resources/.compiled/stat.ink/summary-legends.png

resources/.compiled/counter/counter.css: resources/counter/counter.less $(GULP)
	$(GULP) less --in $< --out $@

resources/.compiled/slack/slack.js: resources/slack/slack.js $(GULP)
	$(GULP) js --in $< --out $@

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
	curl -o $@ $(shell php resources/app-link-logos/favicon.php 'https://ikanakama.ink/')

resources/.compiled/app-link-logos/ikarec-en.png: resources/app-link-logos/ikarec-en.png
	mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikarec-en.png:
	curl -o $@ 'https://lh3.googleusercontent.com/HUy__vFnwLi32AL-L3KeJACQRkXIcq59PASgIbTscr2Ic-kP3fp4GeIrClAgKBWAlQq2'

resources/.compiled/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-ja.png
	mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-en.png
	cp $< $@

resources/.compiled/app-link-logos/festink.png: resources/app-link-logos/festink.ico
	mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/.compiled/app-link-logos/squidtracks.png: resources/app-link-logos/squidtracks.png
	mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	touch -r $< $@

resources/app-link-logos/squidtracks.png:
	curl -sSL -o $@ 'https://github.com/hymm/squid-tracks/raw/master/public/icon.png'

resources/.compiled/app-link-logos/nnid.svg: resources/app-link-logos/nnid.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/switch.svg: resources/app-link-logos/switch.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/inkipedia.png: resources/app-link-logos/inkipedia.ico
	mkdir -p resources/.compiled/app-link-logos
	convert $< $@
	touch -r $< $@

resources/app-link-logos/inkipedia.ico:
	curl -o $@ $(shell php resources/app-link-logos/favicon.php 'https://splatoonwiki.org/')

resources/.compiled/irasutoya/inkling.png: resources/irasutoya/inkling.png
	mkdir -p resources/.compiled/irasutoya
	convert $< -trim +repage -resize x100 -gravity center -background none -extent 100x100 $@
	pngcrush -rem allb -l 9 -ow $@

resources/.compiled/irasutoya/octoling.png: resources/irasutoya/octoling.png
	mkdir -p resources/.compiled/irasutoya
	convert $< -trim +repage -resize x100 -gravity center -background none -extent 100x100 $@
	pngcrush -rem allb -l 9 -ow $@

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

config/img-s3.php:
	echo '<?php' > $@
	echo 'return [' >> $@
	echo "    'class' => 'app\components\ImageS3'," >> $@
	echo "    'enabled' => false," >> $@
	echo "    'endpoint' => 's3-ap-northeast-1.amazonaws.com'," >> $@
	echo "    'accessKey' => ''," >> $@
	echo "    'secret' => ''," >> $@
	echo "    'bucket' => ''," >> $@
	echo '];' >> $@

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

.PHONY: config/version.php
config/version.php: vendor
	./yii revision-data/update

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

$(SUB_RESOURCES):
	$(MAKE) -C $@
.PHONY: $(SUB_RESOURCES)

.PHONY: FORCE all check-style clean clean-resource composer-update fix-style ikalog init migrate-db resource vendor-archive vendor-by-archive download-vendor-archive
