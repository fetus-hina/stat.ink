STYLE_TARGETS := actions assets commands components controllers models

RESOURCE_TARGETS := \
	resources/.compiled/app-link-logos/festink.png \
	resources/.compiled/app-link-logos/ikadenwa.png \
	resources/.compiled/app-link-logos/ikalog.png \
	resources/.compiled/app-link-logos/ikanakama.png \
	resources/.compiled/app-link-logos/ikarec-en.png \
	resources/.compiled/app-link-logos/ikarec-ja.png \
	resources/.compiled/app-link-logos/inkipedia.png \
	resources/.compiled/app-link-logos/nnid.min.svg \
	resources/.compiled/app-link-logos/squidtracks.png \
	resources/.compiled/app-link-logos/switch.min.svg \
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
	resources/.compiled/slack/slack.js \
	resources/.compiled/stat.ink/active-reltime.js \
	resources/.compiled/stat.ink/agent.js \
	resources/.compiled/stat.ink/auto-tooltip.js \
	resources/.compiled/stat.ink/battle-detail.css \
	resources/.compiled/stat.ink/battle-edit.js \
	resources/.compiled/stat.ink/battle-input-2.js \
	resources/.compiled/stat.ink/battle-input.css \
	resources/.compiled/stat.ink/battle-list-config.js \
	resources/.compiled/stat.ink/battle-list-group-header.css \
	resources/.compiled/stat.ink/battle-list.js \
	resources/.compiled/stat.ink/battle-private-note.js \
	resources/.compiled/stat.ink/battle-smooth.js \
	resources/.compiled/stat.ink/battle-summary-dialog.js \
	resources/.compiled/stat.ink/battle-timeline.js \
	resources/.compiled/stat.ink/battle2-players-point-inked.js \
	resources/.compiled/stat.ink/battles-simple.css \
	resources/.compiled/stat.ink/blackout-hint.css \
	resources/.compiled/stat.ink/blackout-hint.js \
	resources/.compiled/stat.ink/blog-entries.css \
	resources/.compiled/stat.ink/browser-icon-widget.js \
	resources/.compiled/stat.ink/cal-heatmap-halloween.css \
	resources/.compiled/stat.ink/color-scheme.js \
	resources/.compiled/stat.ink/cookiealert.css \
	resources/.compiled/stat.ink/cookiealert.js \
	resources/.compiled/stat.ink/downloads.css \
	resources/.compiled/stat.ink/entire-salmon3-tide-event.js \
	resources/.compiled/stat.ink/entire-salmon3-tide-tide.js \
	resources/.compiled/stat.ink/entire-weapon-based-on-k-or-d.js \
	resources/.compiled/stat.ink/entire-weapon-kd-stats.js \
	resources/.compiled/stat.ink/entire-weapon-kd-summary.js \
	resources/.compiled/stat.ink/entire-weapon-stage.js \
	resources/.compiled/stat.ink/entire-weapon-usepct.js \
	resources/.compiled/stat.ink/entire-xpower-distrib3-histogram.js \
	resources/.compiled/stat.ink/fallbackable-image.js \
	resources/.compiled/stat.ink/favicon.png \
	resources/.compiled/stat.ink/fest-power-history.css \
	resources/.compiled/stat.ink/fest-power-history.js \
	resources/.compiled/stat.ink/festpower2-diff-winpct.js \
	resources/.compiled/stat.ink/flot-support.css \
	resources/.compiled/stat.ink/fluid-layout.js \
	resources/.compiled/stat.ink/font.css \
	resources/.compiled/stat.ink/freshness-history.css \
	resources/.compiled/stat.ink/freshness-history.js \
	resources/.compiled/stat.ink/game-modes.css \
	resources/.compiled/stat.ink/gear-ability-number-switcher.js \
	resources/.compiled/stat.ink/hsv2rgb.js \
	resources/.compiled/stat.ink/ie-warning.css \
	resources/.compiled/stat.ink/ie-warning.js \
	resources/.compiled/stat.ink/inline-list.css \
	resources/.compiled/stat.ink/jquery.twemoji.css \
	resources/.compiled/stat.ink/jquery.twemoji.js \
	resources/.compiled/stat.ink/kd-win.css \
	resources/.compiled/stat.ink/kd-win.js \
	resources/.compiled/stat.ink/kill-ratio-column.js \
	resources/.compiled/stat.ink/knockout.css \
	resources/.compiled/stat.ink/knockout.js \
	resources/.compiled/stat.ink/language-dialog.css \
	resources/.compiled/stat.ink/language-dialog.js \
	resources/.compiled/stat.ink/league-power-history.css \
	resources/.compiled/stat.ink/league-power-history.js \
	resources/.compiled/stat.ink/link-external.js \
	resources/.compiled/stat.ink/link-prevnext.js \
	resources/.compiled/stat.ink/main.css \
	resources/.compiled/stat.ink/no-image.png \
	resources/.compiled/stat.ink/os-icon-widget.js \
	resources/.compiled/stat.ink/paintball.css \
	resources/.compiled/stat.ink/permalink-dialog.js \
	resources/.compiled/stat.ink/private-note.js \
	resources/.compiled/stat.ink/ratio.css \
	resources/.compiled/stat.ink/rewrite-link-for-ios-app.js \
	resources/.compiled/stat.ink/salmon-bosses.css \
	resources/.compiled/stat.ink/salmon-players.css \
	resources/.compiled/stat.ink/salmon-stats-history.js \
	resources/.compiled/stat.ink/salmon-waves.css \
	resources/.compiled/stat.ink/salmon-work-list-config.js \
	resources/.compiled/stat.ink/salmon-work-list-hazard.js \
	resources/.compiled/stat.ink/salmon-work-list.js \
	resources/.compiled/stat.ink/salmon3-work-list-config.js \
	resources/.compiled/stat.ink/smooth-scroll.js \
	resources/.compiled/stat.ink/stat-by-map-rule.js \
	resources/.compiled/stat.ink/stat-by-map.js \
	resources/.compiled/stat.ink/stat-by-rule.js \
	resources/.compiled/stat.ink/summary-legends.png \
	resources/.compiled/stat.ink/table-responsive-force.css \
	resources/.compiled/stat.ink/theme.js \
	resources/.compiled/stat.ink/timezone-dialog.js \
	resources/.compiled/stat.ink/user-miniinfo.css \
	resources/.compiled/stat.ink/user-stat-2-monthly-report-pie-winpct.js \
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
	resources/.compiled/stat.ink/user-stat-splatfest.js \
	resources/.compiled/stat.ink/v3-user-stats-win-rate.js \
	resources/.compiled/stat.ink/weapon2.js \
	resources/.compiled/stat.ink/weapons-use.js \
	resources/.compiled/stat.ink/weapons.js \
	resources/.compiled/stat.ink/xpower-history.css \
	resources/.compiled/stat.ink/xpower-history.js \
	web/static-assets/ostatus/ostatus.min.svg \
	web/static-assets/rect-danger.min.svg

SIMPLE_CONFIG_TARGETS := \
	config/amazon-s3.php \
	config/backup-gpg.php \
	config/backup-s3.php \
	config/debug-ips.php \
	config/deepl.php \
	config/img-s3.php \
	config/lepton.php \
	config/twitter.php

REACT_SOURCES := $(shell find resources/react -type f)

all: init migrate-db

init: init-no-resource resource geoip

init-no-resource: \
	composer.phar \
	composer-update \
	vendor \
	node_modules \
	$(SIMPLE_CONFIG_TARGETS) \
	config/version.php \
	config/cookie-secret.php \
	config/authkey-secret.php \
	config/db.php \
	config/cloudflare/ip_ranges.php \

test: init-no-resource
	./composer.phar exec codecept run -v

license: init-no-resource
	./yii license

resource: $(RESOURCE_TARGETS) react $(ADDITIONAL_LICENSES)

.PHONY: react
react: node_modules $(REACT_SOURCES)
	npx webpack-cli

composer-update: composer.phar
	./composer.phar self-update --2

vendor: composer.lock composer.phar
	php composer.phar install --prefer-dist
	@touch vendor

node_modules: package-lock.json
	npm install --unsafe-perm
	@touch $@

check-syntax:
	find . \( -type d \( -name '.git' -or -name 'vendor' -or -name 'node_modules' -or -name 'runtime' \) -prune \) -or \( -type f -name '*.php' -print \) | xargs -n 1 php -l

check-style: check-style-js check-style-css check-style-php

check-style-php: vendor
	php vendor/bin/phpcs -p

.PHONY: phpstan
phpstan: vendor
	php vendor/bin/phpstan --level=0 --memory-limit=1G

check-style-js: node_modules
	npx updates --minor bootstrap,bootswatch
	npx semistandard 'resources/**/*[ej]s'

check-style-css: node_modules
	npx stylelint "resources/**/*.scss" "resources/**/*.css"

fix-style: vendor node_modules
	npx updates -u --minor bootstrap,bootswatch
	vendor/bin/phpcbf -p
	npx semistandard --fix 'resources/**/*[ej]s'

clean: clean-resource
	rm -rf \
		composer.phar \
		data/GeoIP \
		node_modules \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		resources/maps2/*.png \
		resources/maps2/assets \
		web/assets/*

composer.phar:
	curl -fsSL https://getcomposer.org/installer | php

%.min.svg: %.svg node_modules
	npx svgo --output $@ --input $< -q

define scss2css
	@mkdir -p $(dir $(1))
	npx sass $(2) | npx postcss --no-map -o $(1)
	@touch $(1)
endef

define es2js
	@mkdir -p $(dir $(1))
	cat $(2) | \
		npx babel -s false -f jsfile | \
		npx uglifyjs -c -m -b beautify=false,ascii_only=true --comments '/license|copyright/i' -o $(1)
	@touch $(1)
endef

define png
	@mkdir -p $(dir $(1))
	@rm -f $(1)
	npx optipng -quiet -strip all -o7 -out $(1) $(2)
endef

WEAPON2_JS := $(wildcard resources/stat.ink/weapon2.js/*.js)
resources/.compiled/stat.ink/weapon2.js: $(WEAPON2_JS) node_modules
	$(call es2js,$@,$(WEAPON2_JS))

resources/.compiled/flexbox/flexbox.css: resources/flexbox/flexbox.scss node_modules
resources/.compiled/flot-graph-icon/jquery.flot.icon.js: resources/flot-graph-icon/jquery.flot.icon.js node_modules
resources/.compiled/gears/calc.js: resources/gears/calc.js node_modules
resources/.compiled/slack/slack.js: resources/slack/slack.js node_modules
resources/.compiled/stat.ink/active-reltime.js: resources/stat.ink/active-reltime.js node_modules
resources/.compiled/stat.ink/agent.js: resources/stat.ink/agent.es node_modules
resources/.compiled/stat.ink/auto-tooltip.js: resources/stat.ink/auto-tooltip.es node_modules
resources/.compiled/stat.ink/battle-detail.css: resources/stat.ink/battle-detail.scss node_modules
resources/.compiled/stat.ink/battle-edit.js: resources/stat.ink/battle-edit.js node_modules
resources/.compiled/stat.ink/battle-input-2.js: resources/stat.ink/battle-input-2.es node_modules
resources/.compiled/stat.ink/battle-input.css: resources/stat.ink/battle-input.scss node_modules
resources/.compiled/stat.ink/battle-list-config.js: resources/stat.ink/battle-list-config.es node_modules
resources/.compiled/stat.ink/battle-list-group-header.css: resources/stat.ink/battle-list-group-header.scss node_modules
resources/.compiled/stat.ink/battle-list.js: resources/stat.ink/battle-list.es node_modules
resources/.compiled/stat.ink/battle-private-note.js: resources/stat.ink/battle-private-note.es node_modules
resources/.compiled/stat.ink/battle-smooth.js: resources/stat.ink/battle-smooth.es node_modules
resources/.compiled/stat.ink/battle-summary-dialog.js: resources/stat.ink/battle-summary-dialog.es node_modules
resources/.compiled/stat.ink/battle-timeline.js: resources/stat.ink/battle-timeline.es node_modules
resources/.compiled/stat.ink/battle2-players-point-inked.js: resources/stat.ink/battle2-players-point-inked.es node_modules
resources/.compiled/stat.ink/battles-simple.css: resources/stat.ink/battles-simple.scss node_modules
resources/.compiled/stat.ink/blackout-hint.css: resources/stat.ink/blackout-hint.scss node_modules
resources/.compiled/stat.ink/blackout-hint.js: resources/stat.ink/blackout-hint.js node_modules
resources/.compiled/stat.ink/blog-entries.css: resources/stat.ink/blog-entries.scss node_modules
resources/.compiled/stat.ink/browser-icon-widget.js: resources/stat.ink/browser-icon-widget.es node_modules
resources/.compiled/stat.ink/cal-heatmap-halloween.css: resources/stat.ink/cal-heatmap-halloween.scss node_modules
resources/.compiled/stat.ink/color-scheme.js: resources/stat.ink/color-scheme.es node_modules
resources/.compiled/stat.ink/cookiealert.css: resources/stat.ink/cookiealert.scss node_modules
resources/.compiled/stat.ink/cookiealert.js: resources/stat.ink/cookiealert.es node_modules
resources/.compiled/stat.ink/downloads.css: resources/stat.ink/downloads.scss node_modules
resources/.compiled/stat.ink/entire-salmon3-tide-event.js: resources/stat.ink/entire-salmon3-tide-event.es node_modules
resources/.compiled/stat.ink/entire-salmon3-tide-tide.js: resources/stat.ink/entire-salmon3-tide-tide.es node_modules
resources/.compiled/stat.ink/entire-weapon-based-on-k-or-d.js: resources/stat.ink/entire-weapon-based-on-k-or-d.es node_modules
resources/.compiled/stat.ink/entire-weapon-kd-stats.js: resources/stat.ink/entire-weapon-kd-stats.es node_modules
resources/.compiled/stat.ink/entire-weapon-kd-summary.js: resources/stat.ink/entire-weapon-kd-summary.es node_modules
resources/.compiled/stat.ink/entire-weapon-stage.js: resources/stat.ink/entire-weapon-stage.es node_modules
resources/.compiled/stat.ink/entire-weapon-usepct.js: resources/stat.ink/entire-weapon-usepct.es node_modules
resources/.compiled/stat.ink/entire-xpower-distrib3-histogram.js: resources/stat.ink/entire-xpower-distrib3-histogram.es node_modules
resources/.compiled/stat.ink/fallbackable-image.js: resources/stat.ink/fallbackable-image.es node_modules
resources/.compiled/stat.ink/fest-power-history.css: resources/stat.ink/fest-power-history.scss node_modules
resources/.compiled/stat.ink/fest-power-history.js: resources/stat.ink/fest-power-history.es node_modules
resources/.compiled/stat.ink/festpower2-diff-winpct.js: resources/stat.ink/festpower2-diff-winpct.es node_modules
resources/.compiled/stat.ink/flot-support.css: resources/stat.ink/flot-support.scss node_modules
resources/.compiled/stat.ink/fluid-layout.js: resources/stat.ink/fluid-layout.es node_modules
resources/.compiled/stat.ink/font.css: resources/stat.ink/font.scss node_modules
resources/.compiled/stat.ink/freshness-history.css: resources/stat.ink/freshness-history.scss node_modules
resources/.compiled/stat.ink/freshness-history.js: resources/stat.ink/freshness-history.es node_modules
resources/.compiled/stat.ink/game-modes.css: resources/stat.ink/game-modes.scss node_modules
resources/.compiled/stat.ink/gear-ability-number-switcher.js: resources/stat.ink/gear-ability-number-switcher.es node_modules
resources/.compiled/stat.ink/hsv2rgb.js: resources/stat.ink/hsv2rgb.es node_modules
resources/.compiled/stat.ink/ie-warning.css: resources/stat.ink/ie-warning.scss node_modules
resources/.compiled/stat.ink/ie-warning.js: resources/stat.ink/ie-warning.es node_modules
resources/.compiled/stat.ink/inline-list.css: resources/stat.ink/inline-list.scss node_modules
resources/.compiled/stat.ink/jquery.twemoji.css: resources/stat.ink/jquery.twemoji.scss node_modules
resources/.compiled/stat.ink/jquery.twemoji.js: resources/stat.ink/jquery.twemoji.es node_modules
resources/.compiled/stat.ink/kd-win.css: resources/stat.ink/kd-win.scss node_modules
resources/.compiled/stat.ink/kd-win.js: resources/stat.ink/kd-win.js node_modules
resources/.compiled/stat.ink/kill-ratio-column.js: resources/stat.ink/kill-ratio-column.es node_modules
resources/.compiled/stat.ink/knockout.css: resources/stat.ink/knockout.scss node_modules
resources/.compiled/stat.ink/knockout.js: resources/stat.ink/knockout.es node_modules
resources/.compiled/stat.ink/language-dialog.css: resources/stat.ink/language-dialog.scss node_modules
resources/.compiled/stat.ink/language-dialog.js: resources/stat.ink/language-dialog.es node_modules
resources/.compiled/stat.ink/league-power-history.css: resources/stat.ink/league-power-history.scss node_modules
resources/.compiled/stat.ink/league-power-history.js: resources/stat.ink/league-power-history.es node_modules
resources/.compiled/stat.ink/link-external.js: resources/stat.ink/link-external.es node_modules
resources/.compiled/stat.ink/link-prevnext.js: resources/stat.ink/link-prevnext.es node_modules
resources/.compiled/stat.ink/main.css: resources/stat.ink/main.scss node_modules
resources/.compiled/stat.ink/os-icon-widget.js: resources/stat.ink/os-icon-widget.es node_modules
resources/.compiled/stat.ink/paintball.css: resources/stat.ink/paintball.scss node_modules
resources/.compiled/stat.ink/permalink-dialog.js: resources/stat.ink/permalink-dialog.es node_modules
resources/.compiled/stat.ink/private-note.js: resources/stat.ink/private-note.es node_modules
resources/.compiled/stat.ink/ratio.css: resources/stat.ink/ratio.scss node_modules
resources/.compiled/stat.ink/rewrite-link-for-ios-app.js: resources/stat.ink/rewrite-link-for-ios-app.es node_modules
resources/.compiled/stat.ink/salmon-bosses.css: resources/stat.ink/salmon-bosses.scss node_modules
resources/.compiled/stat.ink/salmon-players.css: resources/stat.ink/salmon-players.scss node_modules
resources/.compiled/stat.ink/salmon-stats-history.js: resources/stat.ink/salmon-stats-history.es node_modules
resources/.compiled/stat.ink/salmon-waves.css: resources/stat.ink/salmon-waves.scss node_modules
resources/.compiled/stat.ink/salmon-work-list-config.js: resources/stat.ink/salmon-work-list-config.es node_modules
resources/.compiled/stat.ink/salmon-work-list-hazard.js: resources/stat.ink/salmon-work-list-hazard.es node_modules
resources/.compiled/stat.ink/salmon-work-list.js: resources/stat.ink/salmon-work-list.es node_modules
resources/.compiled/stat.ink/salmon3-work-list-config.js: resources/stat.ink/salmon3-work-list-config.es node_modules
resources/.compiled/stat.ink/smooth-scroll.js: resources/stat.ink/smooth-scroll.es node_modules
resources/.compiled/stat.ink/stat-by-map-rule.js: resources/stat.ink/stat-by-map-rule.es node_modules
resources/.compiled/stat.ink/stat-by-map.js: resources/stat.ink/stat-by-map.es node_modules
resources/.compiled/stat.ink/stat-by-rule.js: resources/stat.ink/stat-by-rule.es node_modules
resources/.compiled/stat.ink/table-responsive-force.css: resources/stat.ink/table-responsive-force.scss node_modules
resources/.compiled/stat.ink/theme.js: resources/stat.ink/theme.es node_modules
resources/.compiled/stat.ink/timezone-dialog.js: resources/stat.ink/timezone-dialog.es node_modules
resources/.compiled/stat.ink/user-miniinfo.css: resources/stat.ink/user-miniinfo.scss node_modules
resources/.compiled/stat.ink/user-stat-2-monthly-report-pie-winpct.js: resources/stat.ink/user-stat-2-monthly-report-pie-winpct.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-inked.js: resources/stat.ink/user-stat-2-nawabari-inked.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-runner.js: resources/stat.ink/user-stat-2-nawabari-runner.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-stats.js: resources/stat.ink/user-stat-2-nawabari-stats.es node_modules
resources/.compiled/stat.ink/user-stat-2-nawabari-winpct.js: resources/stat.ink/user-stat-2-nawabari-winpct.es node_modules
resources/.compiled/stat.ink/user-stat-by-map-rule-detail.css: resources/stat.ink/user-stat-by-map-rule-detail.scss node_modules
resources/.compiled/stat.ink/user-stat-gachi-rank.js: resources/stat.ink/user-stat-gachi-rank.es node_modules
resources/.compiled/stat.ink/user-stat-gachi-winpct.js: resources/stat.ink/user-stat-gachi-winpct.es node_modules
resources/.compiled/stat.ink/user-stat-nawabari-inked.js: resources/stat.ink/user-stat-nawabari-inked.es node_modules
resources/.compiled/stat.ink/user-stat-nawabari-wp.js: resources/stat.ink/user-stat-nawabari-wp.es node_modules
resources/.compiled/stat.ink/user-stat-report.css: resources/stat.ink/user-stat-report.scss node_modules
resources/.compiled/stat.ink/user-stat-splatfest.js: resources/stat.ink/user-stat-splatfest.es node_modules
resources/.compiled/stat.ink/v3-user-stats-win-rate.js: resources/stat.ink/v3-user-stats-win-rate.es node_modules
resources/.compiled/stat.ink/weapons-use.js: resources/stat.ink/weapons-use.js node_modules
resources/.compiled/stat.ink/weapons.js: resources/stat.ink/weapons.js node_modules
resources/.compiled/stat.ink/xpower-history.css: resources/stat.ink/xpower-history.scss node_modules
resources/.compiled/stat.ink/xpower-history.js: resources/stat.ink/xpower-history.es node_modules

%.css:
	$(call scss2css,$@,$<)

%.js:
	$(call es2js,$@,$<)

resources/.compiled/stat.ink/no-image.png: resources/stat.ink/no-image.png
	$(call png,$@,$<)

resources/.compiled/stat.ink/favicon.png: resources/stat.ink/favicon.png
	$(call png,$@,$<)

resources/.compiled/stat.ink/summary-legends.png: resources/stat.ink/summary-legends.png
	$(call png,$@,$<)

resources/.compiled/app-link-logos/ikalog.png: resources/app-link-logos/ikalog.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/ikadenwa.png: resources/app-link-logos/ikadenwa.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/ikanakama.png: resources/app-link-logos/ikanakama.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/ikarec-en.png: resources/app-link-logos/ikarec-en.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/ikarec-ja.png: resources/app-link-logos/ikarec-ja.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/festink.png: resources/app-link-logos/festink.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $<[3] -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/squidtracks.png: resources/app-link-logos/squidtracks.png
	@mkdir -p resources/.compiled/app-link-logos
	convert $< -trim +repage -unsharp 1.5x1+0.7+0.02 -scale x28 $@
	@touch -r $< $@

resources/.compiled/app-link-logos/nnid.svg: resources/app-link-logos/nnid.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/switch.svg: resources/app-link-logos/switch.svg
	xmllint --format $< > $@

resources/.compiled/app-link-logos/inkipedia.png: resources/app-link-logos/inkipedia.ico
	@mkdir -p resources/.compiled/app-link-logos
	convert $< $@
	@touch -r $< $@

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

config/amazon-s3.php:
	@echo '<?php' > $@
	@echo '' >> $@
	@echo 'declare(strict_types=1);' >> $@
	@echo '' >> $@
	@echo 'return [' >> $@
	@echo '    [' >> $@
	@echo "        'name'      => 'Amazon S3'," >> $@
	@echo "        'endpoint'  => 's3-ap-northeast-1.amazonaws.com'," >> $@
	@echo "        'accessKey' => ''," >> $@
	@echo "        'secret'    => ''," >> $@
	@echo "        'bucket'    => ''," >> $@
	@echo '    ],' >> $@
	@echo '];' >> $@

config/backup-s3.php:
	@echo '<?php' > $@
	@echo '' >> $@
	@echo 'declare(strict_types=1);' >> $@
	@echo '' >> $@
	@echo 'return [' >> $@
	@echo "    'endpoint'  => 's3-ap-northeast-1.amazonaws.com'," >> $@
	@echo "    'accessKey' => ''," >> $@
	@echo "    'secret'    => ''," >> $@
	@echo "    'bucket'    => ''," >> $@
	@echo '];' >> $@

config/img-s3.php:
	php config/_generator/img-s3.php > $@

config/backup-gpg.php:
	@echo '<?php' > $@
	@echo '' >> $@
	@echo 'declare(strict_types=1);' >> $@
	@echo '' >> $@
	@echo 'return [' >> $@
	@echo "    'userId' => '0xBC77B5B8'," >> $@
	@echo '];' >> $@

config/debug-ips.php:
	@echo '<?php' > $@
	@echo '' >> $@
	@echo 'declare(strict_types=1);' >> $@
	@echo '' >> $@
	@echo 'return [' >> $@
	@echo "    '127.0.0.1'," >> $@
	@echo "    '::1'," >> $@
	@echo '];' >> $@

config/deepl.php:
	echo Creating $@
	@echo '<?php' > $@
	@echo '' >> $@
	@echo 'declare(strict_types=1);' >> $@
	@echo '' >> $@
	@echo "return '';" >> $@

config/lepton.php:
	cp config/lepton.sample.php $@

config/twitter.php:
	cp config/twitter.sample.php $@

.PHONY: config/version.php
config/version.php: vendor config/db.php
	./yii revision-data/update

.PHONY: config/cloudflare/ip_ranges.php
config/cloudflare/ip_ranges.php: vendor config/db.php
	./yii cloudflare/update-ip-ranges

geoip: init-no-resource
	./yii geoip/update || true

.PHONY: FORCE all check-style clean clean-resource composer-update fix-style init init-no-resource migrate-db resource geoip check-syntax check-style-php check-style-js license
