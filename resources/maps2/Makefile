STAGES := \
	ama \
	battera \
	chozame \
	combu \
	fujitsubo \
	gangaze \
	hokke \
	kombu \
	tachiuo
BLUR := 2x2

FILENAMES := $(addsuffix .jpg, $(STAGES))

all: _daytime _daytime-blur _gray-blur
.PHONY: all

_daytime: $(addprefix assets/daytime/, $(FILENAMES))
.PHONY: _daytime

_daytime-blur: $(addprefix assets/daytime-blur/, $(FILENAMES))
.PHONY: _daytime-blur

_gray-blur: $(addprefix assets/gray-blur/, $(FILENAMES))
.PHONY: _gray-blur

assets/daytime/%.jpg: %.jpg assets/daytime
	convert $< -resize 320x180 -quality 95 $@
	jpegoptim -qs $@

assets/daytime-blur/%.jpg: %.jpg assets/daytime-blur
	convert $< -resize 320x180 -blur $(BLUR) -quality 95 $@
	jpegoptim -qs $@

assets/gray-blur/%.jpg: %.jpg assets/gray-blur
	convert $< -fx 'r*0.299+g*0.587+b*0.114' -colorspace Gray -resize 320x180 -blur $(BLUR) -quality 95 $@
	jpegoptim -qs $@

assets/daytime:
	mkdir -p $@

assets/daytime-blur:
	mkdir -p $@

assets/gray-blur:
	mkdir -p $@

ama.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/e/ed/Inkblot_Art_Academy.jpg'

battera.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/c/c6/Splatoon_2_-_The_Reef.jpg'

chozame.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/2/2d/S2_SturgeonShipyard.jpg'

combu.jpg: kombu.jpg
	cp kombu.jpg combu.jpg

fujitsubo.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/f/fd/BarnacleSportsClub.jpg'

gangaze.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/9/96/Longspine_Urchin_Outskirts_Concert_Hall_1.jpg'

hokke.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/8/85/S2_PortMackerel.jpg'

kombu.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/a/a6/Humpback_Pump_Track_2.jpg'

tachiuo.jpg:
	curl -o $@ -sSL 'https://cdn.wikimg.net/splatoonwiki/images/d/db/S2_MorayTowers.jpg'

clean:
	rm -rf *.jpg assets
.PHONY: clean
.PRECIOUS: %.jpg
