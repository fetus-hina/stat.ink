STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/fest.ink/fest.js/*.js)

FAVICON_TARGETS=resources/.compiled/favicon/favicon.ico \
	resources/.compiled/favicon/76x76-precomposed.png \
	resources/.compiled/favicon/120x120-precomposed.png \
	resources/.compiled/favicon/152x152-precomposed.png \
	resources/.compiled/favicon/180x180-precomposed.png

STARTUP_TARGETS=\
	resources/.compiled/apple-startup/p-768x1024@2x.png \
	resources/.compiled/apple-startup/p-768x1024@1x.png \
	resources/.compiled/apple-startup/p-414x736@3x.png \
	resources/.compiled/apple-startup/p-375x667@2x.png \
	resources/.compiled/apple-startup/p-320x568@2x.png \
	resources/.compiled/apple-startup/p-320x480@2x.png \
	resources/.compiled/apple-startup/p-320x480@1x.png \
	resources/.compiled/apple-startup/l-768x1024@2x.png \
	resources/.compiled/apple-startup/l-768x1024@1x.png \
	resources/.compiled/apple-startup/l-414x736@3x.png \
	resources/.compiled/apple-startup/l-375x667@2x.png \
	resources/.compiled/apple-startup/l-320x568@2x.png \
	resources/.compiled/apple-startup/l-320x480@2x.png \
	resources/.compiled/apple-startup/l-320x480@1x.png

RESOURCE_TARGETS=resources/.compiled/fest.ink/fest.css.gz \
	resources/.compiled/fest.ink/fest.js.gz \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz \
	resources/.compiled/heading-ikamodoki/heading-ikamodoki.js.gz \
	resources/.compiled/ikamodoki/ikamodoki.css.gz \
	resources/.compiled/paintball/paintball.css.gz \
	resources/.compiled/pixiv/pixiv_logo.png \
	resources/.compiled/tz-data/tz-init.js.gz

all: \
	composer.phar \
	vendor \
	node_modules \
	config/google-analytics.php \
	config/twitter.php \
	config/cookie-secret.php \
	resource \
	favicon-maybe \
	apple-startup \
	db/fest.sqlite

favicon: $(FAVICON_TARGETS)

favicon-maybe:
	test -f config/favicon.license.txt && make favicon || true

apple-startup: $(STARTUP_TARGETS)

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

clean: clean-resource clean-apple-startup
	rm -rf \
		composer.phar \
		node_modules \
		runtime/favicon \
		runtime/ikamodoki1.zip \
		runtime/tzdata-latest.tar.gz \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		runtime/tzdata \
		web/assets/*

clean-favicon:
	rm -rf \
		resources/.compiled/favicon \
		runtime/favicon

clean-apple-startup:
	rm -rf $(STARTUP_TARGETS)

composer.phar:
	curl -sS https://getcomposer.org/installer | php

resources/.compiled/favicon/favicon.ico: runtime/favicon/face-320x320.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/face-320x320.png \
		\( -clone 0 -resize 16x16 -sharpen 0x1.0 \) \
		\( -clone 0 -resize 32x32 -sharpen 0x.4 \) \
		\( -clone 0 -resize 48x48 \) \
		\( -clone 0 -resize 64x64 \) \
		-delete 0 -alpha off -colors 256 resources/.compiled/favicon/favicon.ico

resources/.compiled/favicon/76x76-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 76x76 -sharpen 0x.4 runtime/favicon/bust-76x76.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-76x76.png resources/.compiled/favicon/76x76-precomposed.png

resources/.compiled/favicon/120x120-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 120x120 runtime/favicon/bust-120x120.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-120x120.png resources/.compiled/favicon/120x120-precomposed.png

resources/.compiled/favicon/152x152-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 152x152 runtime/favicon/bust-152x152.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-152x152.png resources/.compiled/favicon/152x152-precomposed.png

resources/.compiled/favicon/180x180-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 180x180 runtime/favicon/bust-180x180.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-180x180.png resources/.compiled/favicon/180x180-precomposed.png

runtime/favicon/face-320x320.png: data/favicon/ikagirl.png
	mkdir -p runtime/favicon || true
	convert data/favicon/ikagirl.png -crop 320x320+225+112 runtime/favicon/face-320x320.png

runtime/favicon/bust-500x500.png: data/favicon/ikagirl.png
	mkdir -p runtime/favicon || true
	convert data/favicon/ikagirl.png -crop 500x500+86+107 runtime/favicon/bust-500x500.png

data/favicon/ikagirl.png: vendor data/favicon/ikagirl.dat
	./yii favicon/decrypt

resources/.compiled/fest.ink/fest.js.gz: node_modules $(JS_SRCS)
	./node_modules/.bin/gulp fest-ink-js

resources/.compiled/fest.ink/fest.css.gz: node_modules resources/fest.ink/fest.less
	./node_modules/.bin/gulp fest-ink-css

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.js
	./node_modules/.bin/gulp gh-fork

resources/.compiled/ikamodoki/ikamodoki.css.gz: node_modules resources/.compiled/ikamodoki/font/ikamodoki1_0.woff resources/ikamodoki/ikamodoki.less
	./node_modules/.bin/gulp ikamodoki

resources/.compiled/tz-data/tz-init.js.gz: node_modules runtime/tzdata resources/tz-data/tz-init.js
	./node_modules/.bin/gulp tz-data

resources/.compiled/heading-ikamodoki/heading-ikamodoki.js.gz: node_modules resources/heading-ikamodoki/heading-ikamodoki.js
	./node_modules/.bin/gulp heading-ikamodoki

resources/.compiled/ikamodoki/font/ikamodoki1_0.woff: resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf
	webify resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf

resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf: runtime/ikamodoki1.zip
	mkdir -p resources/.compiled/ikamodoki/font || true
	unzip -j runtime/ikamodoki1.zip ikamodoki/ikamodoki1_0.ttf -d resources/.compiled/ikamodoki/font
	touch resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf

runtime/ikamodoki1.zip:
	wget -O runtime/ikamodoki1.zip http://aramugi.com/wp-content/uploads/2015/07/ikamodoki1.zip

resources/.compiled/paintball/paintball.css.gz: node_modules resources/.compiled/paintball/font/paintball.woff resources/paintball/paintball.less
	./node_modules/.bin/gulp paintball

resources/.compiled/paintball/font/paintball.woff: resources/.compiled/paintball/font/paintball.ttf
	webify resources/.compiled/paintball/font/paintball.ttf

resources/.compiled/paintball/font/paintball.ttf: runtime/paintball/paintball.ttf
	mkdir -p resources/.compiled/paintball/font || true
	cp runtime/paintball/paintball.ttf resources/.compiled/paintball/font/paintball.ttf

runtime/paintball/paintball.ttf:
	mkdir -p runtime/paintball || true
	wget -O runtime/paintball/paintball.ttf http://download1474.mediafire.com/q35sqe1n8qxg/6zxa6x11hqb6o3h/Paintball_Beta_3.ttf

resources/.compiled/pixiv/pixiv_logo.png: runtime/pixiv_logo/pixiv_logo.png
	mkdir -p resources/.compiled/pixiv || true
	pngcrush -rem allb -l 9 runtime/pixiv_logo/pixiv_logo.png resources/.compiled/pixiv/pixiv_logo.png

runtime/pixiv_logo/pixiv_logo.png: runtime/pixiv_logo/pixiv_logo.svg
	convert -background none runtime/pixiv_logo/pixiv_logo.svg -resize 77x30 runtime/pixiv_logo/pixiv_logo.png

runtime/pixiv_logo/pixiv_logo.svg: runtime/pixiv_logo/pixiv_logo.zip
	unzip -j runtime/pixiv_logo/pixiv_logo.zip pixiv_logo/pixiv_logo.svg -d runtime/pixiv_logo
	touch runtime/pixiv_logo/pixiv_logo.svg

runtime/pixiv_logo/pixiv_logo.zip:
	mkdir -p runtime/pixiv_logo || true
	wget -O runtime/pixiv_logo/pixiv_logo.zip 'http://source.pixiv.net/www/images/pixiv_logo.zip'

db/fest.sqlite: vendor runtime/tzdata FORCE
	./yii migrate/up --interactive=0
	sqlite3 db/fest.sqlite VACUUM

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

config/twitter.php:
	cp config/twitter.php.sample config/twitter.php

config/google-analytics.php:
	echo '<?php' > config/google-analytics.php
	echo 'return "";' >> config/google-analytics.php

runtime/tzdata: runtime/tzdata-latest.tar.gz
	mkdir runtime/tzdata || true
	tar -C runtime/tzdata -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

PATH_COMPILED_APPLE_STARTUP=resources/.compiled/apple-startup
PATH_RUNTIME_APPLE_STARTUP=runtime/apple-startup

$(PATH_COMPILED_APPLE_STARTUP)/%.png: $(PATH_RUNTIME_APPLE_STARTUP)/%.png
	mkdir $(PATH_COMPILED_APPLE_STARTUP) || true
	pngcrush -rem allb -l 9 $< $@

$(PATH_RUNTIME_APPLE_STARTUP)/%.png: resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf config/console.php
	mkdir $(PATH_RUNTIME_APPLE_STARTUP) || true
	./yii apple-startup/create --ttf=resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf $@

.PHONY: all favicon apple-startup resource check-style fix-style clean clean-resource clean-favicon FORCE
