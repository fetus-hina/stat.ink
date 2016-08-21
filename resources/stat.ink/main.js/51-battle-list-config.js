window.battleListConfig = function() {
    var storage = window.localStorage;
    var loadAllConfig = function() {
        var json = storage.getItem("battle-list");
        var config = {};
        if (json) {
            config = JSON.parse(json);
        }
        var defaults = {
            hscroll: false,
            "cell-lobby": false,
            "cell-rule": true,
            "cell-rule-short": false,
            "cell-map": true,
            "cell-map-short": false,
            "cell-main-weapon": true,
            "cell-main-weapon-short": false,
            "cell-sub-weapon": false,
            "cell-special": false,
            "cell-rank": false,
            "cell-rank-after": false,
            "cell-level": false,
            "cell-level-after": false,
            "cell-result": true,
            "cell-kd": true,
            "cell-kill-ratio": true,
            "cell-kill-rate": false,
            "cell-point": false,
            "cell-rank-in-team": false,
            "cell-datetime": true,
            "cell-reltime": false
        };
        for (var i in defaults) {
            if (defaults.hasOwnProperty(i)) {
                if (config[i] === undefined) {
                    config[i] = defaults[i];
                }
            }
        }
        return config;
    };
    var loadConfig = function(key) {
        var config = loadAllConfig();
        return config[key];
    };
    var updateConfig = function(key, enable) {
        var config = loadAllConfig();
        config[key] = !!enable;
        storage.setItem("battle-list", JSON.stringify(config));
    };
    var changeTableHScroll = function(enable) {
        if (enable) {
            $(".table-responsive").addClass("table-responsive-force");
        } else {
            $(".table-responsive").removeClass("table-responsive-force");
        }
    };
    var changeCellVisibility = function(klass, enable) {
        if (enable) {
            $("." + klass).show();
        } else {
            $("." + klass).hide();
        }
    };
    var loadConfigAndUpdateUI = function() {
        $("#table-hscroll").each(function() {
            var enable = loadConfig("hscroll");
            $(this).prop("checked", enable);
            changeTableHScroll(enable);
        });
        $(".table-config-chk").each(function() {
            var klass = $(this).attr("data-klass");
            var enable = loadConfig(klass);
            $(this).prop("checked", enable);
            changeCellVisibility(klass, enable);
        });
    };
    loadConfigAndUpdateUI();
    $("#table-hscroll").click(function() {
        var enable = $(this).prop("checked");
        changeTableHScroll(enable);
        updateConfig("hscroll", enable);
    });
    $(".table-config-chk").click(function() {
        var klass = $(this).attr("data-klass");
        var enable = $(this).prop("checked");
        changeCellVisibility(klass, enable);
        updateConfig(klass, enable);
    });
    $(window).on("storage", function($ev) {
        loadConfigAndUpdateUI();
    });
};
