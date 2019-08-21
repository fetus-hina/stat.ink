STYLE_TARGETS := actions assets commands components controllers models
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
	resources/.compiled/irasutoya/eto/0.png \
	resources/.compiled/irasutoya/eto/1.png \
	resources/.compiled/irasutoya/eto/10.png \
	resources/.compiled/irasutoya/eto/11.png \
	resources/.compiled/irasutoya/eto/2.png \
	resources/.compiled/irasutoya/eto/3.png \
	resources/.compiled/irasutoya/eto/4.png \
	resources/.compiled/irasutoya/eto/5.png \
	resources/.compiled/irasutoya/eto/6.png \
	resources/.compiled/irasutoya/eto/7.png \
	resources/.compiled/irasutoya/eto/8.png \
	resources/.compiled/irasutoya/eto/9.png \
	resources/.compiled/irasutoya/inkling.png \
	resources/.compiled/irasutoya/octoling.png \
	resources/.compiled/ostatus/ostatus.min.svg \
	resources/.compiled/ostatus/ostatus.min.svg.br \
	resources/.compiled/ostatus/ostatus.min.svg.gz \
	resources/.compiled/ostatus/remote-follow.js \
	resources/.compiled/slack/slack.js \
	resources/.compiled/stat.ink/active-reltime.js \
	resources/.compiled/stat.ink/agent.js \
	resources/.compiled/stat.ink/auto-tooltip.js \
	resources/.compiled/stat.ink/battle-detail.css \
	resources/.compiled/stat.ink/battle-edit.js \
	resources/.compiled/stat.ink/battle-input-2.js \
	resources/.compiled/stat.ink/battle-input.css \
	resources/.compiled/stat.ink/battle-list-config.js \
	resources/.compiled/stat.ink/battle-list.js \
	resources/.compiled/stat.ink/battle-summary-dialog.css \
	resources/.compiled/stat.ink/battle-summary-dialog.js \
	resources/.compiled/stat.ink/battle-thumb-list.css \
	resources/.compiled/stat.ink/battle-thumb-list.js \
	resources/.compiled/stat.ink/battle2-players-point-inked.js \
	resources/.compiled/stat.ink/battles-simple.css \
	resources/.compiled/stat.ink/blackout-hint.css \
	resources/.compiled/stat.ink/blackout-hint.js \
	resources/.compiled/stat.ink/blog-entries.css \
	resources/.compiled/stat.ink/browser-icon-widget.js \
	resources/.compiled/stat.ink/color-scheme.js \
	resources/.compiled/stat.ink/current-time.js \
	resources/.compiled/stat.ink/downloads.css \
	resources/.compiled/stat.ink/favicon.png \
	resources/.compiled/stat.ink/fest-power-history.css \
	resources/.compiled/stat.ink/fest-power-history.js \
	resources/.compiled/stat.ink/festpower2-diff-winpct.js \
	resources/.compiled/stat.ink/flot-support.css \
	resources/.compiled/stat.ink/fluid-layout.js \
	resources/.compiled/stat.ink/freshness-history.css \
	resources/.compiled/stat.ink/freshness-history.js \
	resources/.compiled/stat.ink/game-modes.css \
	resources/.compiled/stat.ink/ie-warning.css \
	resources/.compiled/stat.ink/ie-warning.js \
	resources/.compiled/stat.ink/kd-win.css \
	resources/.compiled/stat.ink/kd-win.js \
	resources/.compiled/stat.ink/knockout.css \
	resources/.compiled/stat.ink/knockout.js \
	resources/.compiled/stat.ink/language-dialog.css \
	resources/.compiled/stat.ink/language-dialog.js \
	resources/.compiled/stat.ink/link-prevnext.js \
	resources/.compiled/stat.ink/main.css \
	resources/.compiled/stat.ink/no-image.png \
	resources/.compiled/stat.ink/os-icon-widget.js \
	resources/.compiled/stat.ink/permalink-dialog.js \
	resources/.compiled/stat.ink/private-note.js \
	resources/.compiled/stat.ink/rewrite-link-for-ios-app.js \
	resources/.compiled/stat.ink/salmon-work-list-config.js \
	resources/.compiled/stat.ink/salmon-work-list-hazard.js \
	resources/.compiled/stat.ink/salmon-work-list.js \
	resources/.compiled/stat.ink/schedule.css \
	resources/.compiled/stat.ink/smooth-scroll.js \
	resources/.compiled/stat.ink/stat-by-map-rule.js \
	resources/.compiled/stat.ink/stat-by-map.js \
	resources/.compiled/stat.ink/stat-by-rule.js \
	resources/.compiled/stat.ink/summary-legends.png \
	resources/.compiled/stat.ink/table-responsive-force.css \
	resources/.compiled/stat.ink/theme.js \
	resources/.compiled/stat.ink/timezone-dialog.js \
	resources/.compiled/stat.ink/user-miniinfo.css \
	resources/.compiled/stat.ink/user-stat-2-nawabari-inked.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-runner.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-stats.js \
	resources/.compiled/stat.ink/user-stat-2-nawabari-winpct.js \
	resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css \
	resources/.compiled/stat.ink/user-stat-gachi-rank.js \
	resources/.compiled/stat.ink/user-stat-gachi-winpct.js \
	resources/.compiled/stat.ink/user-stat-nawabari-inked.js \
	resources/.compiled/stat.ink/user-stat-nawabari-wp.js \
	resources/.compiled/stat.ink/user-stat-report.css \
	resources/.compiled/stat.ink/weapon2.js \
	resources/.compiled/stat.ink/weapons-use.js \
	resources/.compiled/stat.ink/weapons.js \
	resources/.compiled/stat.ink/xpower-history.css \
	resources/.compiled/stat.ink/xpower-history.js \
	web/static-assets/cc/cc-by.svg \
	web/static-assets/cc/cc-by.svg.br \
	web/static-assets/cc/cc-by.svg.gz \
	web/static-assets/rect-danger.min.svg \
	web/static-assets/rect-danger.min.svg.br \
	web/static-assets/rect-danger.min.svg.gz

SUB_RESOURCES := \
	resources/browser-logos \
	resources/os-logos

RESOURCE_TARGETS := \
	$(RESOURCE_TARGETS_MAIN) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.br) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.gz) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.br) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.gz) \
	$(SUB_RESOURCES)

VENDOR_ARCHIVE_FILE := runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz
VENDOR_ARCHIVE_SIGN := runtime/vendor-archive/vendor-$(VENDOR_SHA256).tar.xz.asc

SIMPLE_CONFIG_TARGETS := \
	config/amazon-s3.php \
	config/backup-gpg.php \
	config/backup-s3.php \
	config/debug-ips.php \
	config/google-adsense.php \
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
	config/authkey-secret.php \
	config/db.php \
	resource \
	geoip

init-by-archive: \
	composer.phar \
	composer-update \
	vendor-by-archive \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/version.php \
	config/cookie-secret.php \
	config/authkey-secret.php \
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
	@touch -r composer.json composer.phar

vendor: composer.phar composer.lock
	php composer.phar install --prefer-dist --profile
	@touch -r composer.lock vendor

node_modules: package-lock.json
	npm install --unsafe-perm
	@touch $@

package-lock.json: package.json
	npm update --unsafe-perm
	@touch $@

check-syntax:
	find . \( -type d \( -name '.git' -or -name 'vendor' -or -name 'node_modules' -or -name 'runtime' \) -prune \) -or \( -type f -name '*.php' -print \) | xargs -n 1 php -l

check-style: check-style-js check-style-css check-style-php

check-style-php: vendor
	php -d memory_limit=-1 vendor/bin/phpcs --standard=phpcs-customize.xml --encoding=UTF-8 --runtime-set ignore_warnings_on_exit 1 $(STYLE_TARGETS)

check-style-js: node_modules
	node_modules/.bin/updates
	npx eslint "resources/**/*.es" "resources/**/*.js"

check-style-css: node_modules
	npx stylelint "resources/**/*.less" "resources/**/*.css"

fix-style: vendor node_modules
	node_modules/.bin/updates -u
	vendor/bin/phpcbf --standard=PSR12 --encoding=UTF-8 $(STYLE_TARGETS)
	npx eslint --fix "resources/**/*.es" "resources/**/*.js"

clean: clean-resource
	rm -rf \
		composer.phar \
		data/GeoIP \
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
	test -e $(VENDOR_ARCHIVE_FILE) || curl -o $(VENDOR_ARCHIVE_FILE) -fsSL https://src-archive.stat.ink/vendor/vendor-$(VENDOR_SHA256).tar.xz
	test -e $(VENDOR_ARCHIVE_SIGN) || curl -o $(VENDOR_ARCHIVE_SIGN) -fsSL https://src-archive.stat.ink/vendor/vendor-$(VENDOR_SHA256).tar.xz.asc

composer.phar:
	curl -fsSL https://getcomposer.org/installer | php
	@touch -r composer.json composer.phar

composer.lock: composer.json composer.phar
	php -d memory_limit=-1 ./composer.phar update -vvv
	@touch -r composer.json composer.lock

BROTLI := $(shell if [ -e /usr/bin/brotli ]; then echo brotli; else echo bro; fi )
%.br: %
ifeq ($(BROTLI),bro)
	bro --quality 11 --force --input $< --output $@
else
	brotli -Zfo $@ $<
endif
	@chmod 644 $@
	@touch $@

%.gz: % node_modules
	@rm -f $@
	npx zopfli -i 15 $<
	@chmod 644 $@

%.min.svg: %.svg node_modules
	./node_modules/.bin/svgo --output $@ --input $< -q

resources/.compiled/ostatus/ostatus.svg:
	@mkdir -p $(dir $@)
	curl -fsSL -o $@ 'https://github.com/OStatus/assets/raw/master/ostatus.svg'

web/static-assets/cc/cc-by.svg:
	@mkdir -p $(dir $@)
	curl -fsSL -o $@ http://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by.svg

define less2css
	@mkdir -p $(dir $(1))
	npx lessc $(2) | npx postcss --no-map -o $(1)
endef

define es2js
	@mkdir -p $(dir $(1))
	cat $(2) | \
		npx babel -s false -f jsfile | \
		npx uglifyjs -c -m -b beautify=false,ascii_only=true --comments '/license|copyright/i' -o $(1)
endef

define png
	@mkdir -p $(dir $(1))
	@rm -f $(1)
	npx optipng -quiet -strip all -o7 -out $(1) $(2)
endef

WEAPON2_JS := $(wildcard resources/stat.ink/weapon2.js/*.js)
resources/.compiled/stat.ink/weapon2.js: $(WEAPON2_JS) node_modules
	$(call es2js,$@,$(WEAPON2_JS))

resources/.compiled/counter/counter.css: resources/counter/counter.less node_modules
resources/.compiled/flexbox/flexbox.css: resources/flexbox/flexbox.less node_modules
resources/.compiled/flot-graph-icon/jquery.flot.icon.js: resources/flot-graph-icon/jquery.flot.icon.js node_modules
resources/.compiled/gears/calc.js: resources/gears/calc.js node_modules
resources/.compiled/ostatus/remote-follow.js: resources/ostatus/remote-follow.js node_modules
resources/.compiled/slack/slack.js: resources/slack/slack.js node_modules
resources/.compiled/stat.ink/active-reltime.js: resources/stat.ink/active-reltime.js node_modules
resources/.compiled/stat.ink/agent.js: resources/stat.ink/agent.es node_modules
resources/.compiled/stat.ink/auto-tooltip.js: resources/stat.ink/auto-tooltip.es node_modules
resources/.compiled/stat.ink/battle-detail.css: resources/stat.ink/battle-detail.less node_modules
resources/.compiled/stat.ink/battle-edit.js: resources/stat.ink/battle-edit.js node_modules
resources/.compiled/stat.ink/battle-input-2.js: resources/stat.ink/battle-input-2.es node_modules
resources/.compiled/stat.ink/battle-input.css: resources/stat.ink/battle-input.less node_modules
resources/.compiled/stat.ink/battle-list-config.js: resources/stat.ink/battle-list-config.es node_modules
resources/.compiled/stat.ink/battle-list.js: resources/stat.ink/battle-list.es node_modules
resources/.compiled/stat.ink/battle-summary-dialog.css: resources/stat.ink/battle-summary-dialog.less node_modules
resources/.compiled/stat.ink/battle-summary-dialog.js: resources/stat.ink/battle-summary-dialog.es node_modules
resources/.compiled/stat.ink/battle-thumb-list.css: resources/stat.ink/battle-thumb-list.less node_modules
resources/.compiled/stat.ink/battle-thumb-list.js: resources/stat.ink/battle-thumb-list.es node_modules
resources/.compiled/stat.ink/battle2-players-point-inked.js: resources/stat.ink/battle2-players-point-inked.es node_modules
resources/.compiled/stat.ink/battles-simple.css: resources/stat.ink/battles-simple.less node_modules
resources/.compiled/stat.ink/blackout-hint.css: resources/stat.ink/blackout-hint.less node_modules
resources/.compiled/stat.ink/blackout-hint.js: resources/stat.ink/blackout-hint.js node_modules
resources/.compiled/stat.ink/blog-entries.css: resources/stat.ink/blog-entries.less node_modules
resources/.compiled/stat.ink/browser-icon-widget.js: resources/stat.ink/browser-icon-widget.es
resources/.compiled/stat.ink/color-scheme.js: resources/stat.ink/color-scheme.es node_modules
resources/.compiled/stat.ink/current-time.js: resources/stat.ink/current-time.es node_modules
resources/.compiled/stat.ink/downloads.css: resources/stat.ink/downloads.less node_modules
resources/.compiled/stat.ink/fest-power-history.css: resources/stat.ink/fest-power-history.less node_modules
resources/.compiled/stat.ink/fest-power-history.js: resources/stat.ink/fest-power-history.es node_modules
resources/.compiled/stat.ink/festpower2-diff-winpct.js: resources/stat.ink/festpower2-diff-winpct.es node_modules
resources/.compiled/stat.ink/flot-support.css: resources/stat.ink/flot-support.less node_modules
resources/.compiled/stat.ink/fluid-layout.js: resources/stat.ink/fluid-layout.es node_modules
resources/.compiled/stat.ink/freshness-history.css: resources/stat.ink/freshness-history.less node_modules
resources/.compiled/stat.ink/freshness-history.js: resources/stat.ink/freshness-history.es node_modules
resources/.compiled/stat.ink/game-modes.css: resources/stat.ink/game-modes.less node_modules
resources/.compiled/stat.ink/ie-warning.css: resources/stat.ink/ie-warning.less node_modules
resources/.compiled/stat.ink/ie-warning.js: resources/stat.ink/ie-warning.es node_modules
resources/.compiled/stat.ink/kd-win.css: resources/stat.ink/kd-win.less node_modules
resources/.compiled/stat.ink/kd-win.js: resources/stat.ink/kd-win.js node_modules
resources/.compiled/stat.ink/knockout.css: resources/stat.ink/knockout.less node_modules
resources/.compiled/stat.ink/knockout.js: resources/stat.ink/knockout.es node_modules
resources/.compiled/stat.ink/language-dialog.css: resources/stat.ink/language-dialog.less node_modules
resources/.compiled/stat.ink/language-dialog.js: resources/stat.ink/language-dialog.es node_modules
resources/.compiled/stat.ink/link-prevnext.js: resources/stat.ink/link-prevnext.es node_modules
resources/.compiled/stat.ink/main.css: resources/stat.ink/main.less node_modules
resources/.compiled/stat.ink/os-icon-widget.js: resources/stat.ink/os-icon-widget.es node_modules
resources/.compiled/stat.ink/permalink-dialog.js: resources/stat.ink/permalink-dialog.es node_modules
resources/.compiled/stat.ink/private-note.js: resources/stat.ink/private-note.es node_modules
resources/.compiled/stat.ink/rewrite-link-for-ios-app.js: resources/stat.ink/rewrite-link-for-ios-app.es node_modules
resources/.compiled/stat.ink/salmon-work-list-config.js: resources/stat.ink/salmon-work-list-config.es node_modules
resources/.compiled/stat.ink/salmon-work-list-hazard.js: resources/stat.ink/salmon-work-list-hazard.es node_modules
resources/.compiled/stat.ink/salmon-work-list.js: resources/stat.ink/salmon-work-list.es node_modules
resources/.compiled/stat.ink/schedule.css: resources/stat.ink/schedule.less node_modules
resources/.compiled/stat.ink/smooth-scroll.js: resources/stat.ink/smooth-scroll.es node_modules
resources/.compiled/stat.ink/stat-by-map-rule.js: resources/stat.ink/stat-by-map-rule.es node_modules
resources/.compiled/stat.ink/stat-by-map.js: resources/stat.ink/stat-by-map.es node_modules
resources/.compiled/stat.ink/stat-by-rule.js: resources/stat.ink/stat-by-rule.es node_modules
resources/.compiled/stat.ink/table-responsive-force.css: resources/stat.ink/table-responsive-force.less node_modules
resources/.compiled/stat.ink/theme.js: resources/stat.ink/theme.es
resources/.compiled/stat.ink/timezone-dialog.js: resources/stat.ink/timezone-dialog.es node_modules
resources/.compiled/stat.ink/user-miniinfo.css: resources/stat.ink/user-miniinfo.less node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-inked.js: resources/stat.ink/user-stat-2-nawabari-inked.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-runner.js: resources/stat.ink/user-stat-2-nawabari-runner.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-stats.js: resources/stat.ink/user-stat-2-nawabari-stats.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-winpct.js: resources/stat.ink/user-stat-2-nawabari-winpct.es node_modules
resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css: resources/stat.ink/user-stat-by-map-rule-detail.less node_modules
resources/.compiled/stat.ink/user-stat-gachi-rank.js: resources/stat.ink/user-stat-gachi-rank.es node_modules
resources/.compiled/stat.ink/user-stat-gachi-winpct.js: resources/stat.ink/user-stat-gachi-winpct.es node_modules
resources/.compiled/stat.ink/user-stat-nawabari-inked.js: resources/stat.ink/user-stat-nawabari-inked.es node_modules
resources/.compiled/stat.ink/user-stat-nawabari-wp.js: resources/stat.ink/user-stat-nawabari-wp.es node_modules
resources/.compiled/stat.ink/user-stat-report.css: resources/stat.ink/user-stat-report.less node_modules
resources/.compiled/stat.ink/weapons-use.js: resources/stat.ink/weapons-use.js node_modules
resources/.compiled/stat.ink/weapons.js: resources/stat.ink/weapons.js node_modules
resources/.compiled/stat.ink/xpower-history.css: resources/stat.ink/xpower-history.less node_modules
resources/.compiled/stat.ink/xpower-history.js: resources/stat.ink/xpower-history.es node_modules

%.css:
	$(call less2css,$@,$<)

%.js:
	$(call es2js,$@,$<)

resources/.compiled/stat.ink/no-image.png: resources/stat.ink/no-image.png
	$(call png,$@,$<)

resources/.compiled/stat.ink/favicon.png: resources/stat.ink/favicon.png
	$(call png,$@,$<)

resources/.compiled/stat.ink/summary-legends.png: resources/stat.ink/summary-legends.png
	$(call png,$@,$<)

resources/app-link-logos/ikalog.png:
	curl -fsSL -o $@ 'https://cloud.githubusercontent.com/assets/2528004/17077116/6d613dca-50ff-11e6-9357-9ba894459444.png'

resources/.compiled/app-link-logos/ikalog.png: resources/app-link-logos/ikalog.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/app-link-logos/ikadenwa.png:
	curl -fsSL -o $@ 'https://ikadenwa.ink/static/img/ika-mark.png'

resources/.compiled/app-link-logos/ikadenwa.png: resources/app-link-logos/ikadenwa.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/ikanakama.png: resources/app-link-logos/ikanakama.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/app-link-logos/ikanakama.ico:
	curl -fsSL -o $@ $(shell php resources/app-link-logos/favicon.php 'https://ikanakama.ink/')

resources/.compiled/app-link-logos/ikarec-en.png: resources/app-link-logos/ikarec-en.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/app-link-logos/ikarec-en.png:
	curl -fsSL -o $@ 'https://lh3.googleusercontent.com/HUy__vFnwLi32AL-L3KeJACQRkXIcq59PASgIbTscr2Ic-kP3fp4GeIrClAgKBWAlQq2'

resources/.compiled/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-ja.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $<[1] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-en.png
	cp $< $@

resources/.compiled/app-link-logos/festink.png: resources/app-link-logos/festink.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/squidtracks.png: resources/app-link-logos/squidtracks.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/app-link-logos/squidtracks.png:
	curl -fsSL -sSL -o $@ 'https://github.com/hymm/squid-tracks/raw/master/public/icon.png'

resources/.compiled/app-link-logos/nnid.svg: resources/app-link-logos/nnid.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/switch.svg: resources/app-link-logos/switch.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/inkipedia.png: resources/app-link-logos/inkipedia.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $< $@
	@touch -r $< $@

resources/app-link-logos/inkipedia.ico:
	curl -fsSL -o $@ $(shell php resources/app-link-logos/favicon.php 'https://splatoonwiki.org/')

resources/.compiled/irasutoya/inkling.png: resources/irasutoya/inkling.png.tmp
	$(call png,$@,$<)

resources/irasutoya/inkling.png.tmp: resources/irasutoya/inkling.png
	convert $< -trim +repage -resize x100 -gravity center -background none -extent 100x100 $@

resources/.compiled/irasutoya/octoling.png: resources/irasutoya/octoling.png.tmp
	$(call png,$@,$<)

resources/irasutoya/octoling.png.tmp: resources/irasutoya/octoling.png
	convert $< -trim +repage -resize x100 -gravity center -background none -extent 100x100 $@

resources/.compiled/irasutoya/eto/%.png: resources/irasutoya/eto/%.png.tmp
	$(call png,$@,$<)

resources/irasutoya/eto/%.png.tmp: resources/irasutoya/eto/%.png
	convert $< -trim +repage -resize x100 -gravity center -background none $@

data/geoip:
	mkdir -p $@
	geoipupdate -d $@

migrate-db: vendor config/db.php
	./yii migrate/up --interactive=0
	./yii migrate/up --interactive=0 --migration-path="" --migration-namespaces=yii\\queue\\db\\migrations
	./yii cache/flush-schema --interactive=0

config/cookie-secret.php: vendor $(SIMPLE_CONFIG_TARGETS)
	test -f config/cookie-secret.php || ./yii secret/cookie
	@touch config/cookie-secret.php

config/authkey-secret.php: vendor $(SIMPLE_CONFIG_TARGETS)
	test -f config/authkey-secret.php || ./yii secret/authkey
	@touch config/authkey-secret.php

config/db.php: vendor $(SIMPLE_CONFIG_TARGETS)
	test -f config/db.php || ./yii secret/db
	@touch config/db.php

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
config/version.php: vendor config/db.php
	./yii revision-data/update

runtime/ikalog:
	mkdir -p runtime/ikalog

runtime/ikalog/repo:
	git clone --recursive -o origin https://github.com/hasegaw/IkaLog.git $@

runtime/ikalog/winikalog.html: FORCE
	curl -fsSL -o $@ 'https://hasegaw.github.io/IkaLog/'

vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php: vendor FORCE
	head -n 815 vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php | tail -n 10 | grep '\\1 \\2' > /dev/null && \
		patch -d vendor/smarty/smarty -p1 -Nst < data/patch/smarty-strip.patch || /bin/true

$(VENDOR_ARCHIVE_SIGN): $(VENDOR_ARCHIVE_FILE)
	gpg -s -u 0xF6B887CD --detach-sign -a $<

$(VENDOR_ARCHIVE_FILE): vendor runtime/vendor-archive
	tar -Jcf $@ $<

runtime/vendor-archive:
	mkdir -p $@ || true

geoip: \
		data/GeoIP/COPYRIGHT.txt \
		data/GeoIP/GeoLite2-City.mmdb \
		data/GeoIP/GeoLite2-Country.mmdb \
		data/GeoIP/LICENSE.txt

data/GeoIP/%.mmdb: data/GeoIP/%.tar.gz
	@mkdir -p $(dir $@)
	tar -zxf $< --strip=1 --no-same-owner -C data/GeoIP */$(notdir $@)
	@touch $@

data/GeoIP/%.txt: data/GeoIP/GeoLite2-Country.tar.gz
	@mkdir -p $(dir $@)
	tar -zxf $< --strip=1 --no-same-owner -C data/GeoIP */$(notdir $@)
	@touch $@

data/GeoIP/%.tar.gz:
	@mkdir -p $(dir $@)
	curl -fsSL -o $@ https://geolite.maxmind.com/download/geoip/database/$(notdir $@)
	@touch $@

.PRECIOUS: data/GeoIP/%.tar.gz

$(SUB_RESOURCES):
	$(MAKE) -C $@
.PHONY: $(SUB_RESOURCES)

.PHONY: FORCE all check-style clean clean-resource composer-update fix-style ikalog init migrate-db resource vendor-archive vendor-by-archive download-vendor-archive geoip check-syntax check-style-php check-style-js
