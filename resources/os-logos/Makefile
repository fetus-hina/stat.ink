PRODUCTS := \
	android-icon \
	apple \
	chrome \
	ios \
	linux-tux \
	microsoft-windows

ICONS := $(addprefix logos/,$(addsuffix .svg, $(PRODUCTS)))

.PHONY: all
all: $(ICONS)

.PHONY: clean dist-clean
dist-clean: clean
clean:
	rm -rf logos

logos/%.svg:
	mkdir -p logos
	curl -fsSL -o $@ $(subst %,$(basename $(notdir $@)),https://github.com/gilbarbara/logos/raw/master/logos/%.svg)
