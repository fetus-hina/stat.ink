PRODUCTS := \
	android \
	chrome \
	edge \
	firefox \
	internet-explorer_9-11 \
	opera \
	safari \
	safari-ios \
	samsung-internet \
	silk

ICONS := $(addprefix logos/,$(addsuffix .png, $(PRODUCTS)))

.PHONY: all
all: $(ICONS)

.PHONY: clean dist-clean
dist-clean: clean
clean:
	rm -rf logos archives

logos/%.png:
	mkdir -p logos
	curl -fsSL -o $@ \
		$(subst %,$(basename $(notdir $@)),https://github.com/alrra/browser-logos/raw/master/src/%/%.png)

archives/%.png:
	mkdir -p archives logos
	curl -fsSL -o $@ \
		$(subst %,$(basename $(notdir $@)),https://github.com/alrra/browser-logos/raw/master/src/archive/%/%.png)

logos/android.png: archives/android.png
	cp $< $@

logos/internet-explorer_9-11.png: archives/internet-explorer_9-11.png
	cp $< $@
