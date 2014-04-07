function MoyoInfinite(options) {
    this.options = jQuery.extend(MoyoInfinite.defaultOptions, options);

    if (this.validate()) {
        this.setAction(this.options);
    }
}

MoyoInfinite.defaultOptions = {
    'trigger': '#btn-load-more',
    'container': '#container',
    'link': '',
    'loading': false,
    'offset': 20,
    'limit': 20,
    'done': false,
    'count': 0
};

MoyoInfinite.prototype.validate = function() {
    if (!this.options.link) {
        console.log('No link given.');
        return false;
    }

    return true;
};

MoyoInfinite.prototype.onBeforeLoading = function() {

};

MoyoInfinite.prototype.onAfterLoading = function() {

};

MoyoInfinite.prototype.onNothingMoreToLoad = function() {

};

MoyoInfinite.prototype.setAction = function() {
    var self = this;
    var options = this.options;

    jQuery.noConflict()(function($) {
        $(options.trigger).click(function() {
            if (options.loading || options.done) {
                return;
            }

            options.loading = true;
            self.onBeforeLoading();

            $.get(options.link + '&limit=' + options.limit + '&offset=' + options.offset)
                .done(function(data) {
                    $(options.container).append(data);

                    options.offset += options.limit;

                    if (options.offset >= options.count) {
                        options.done = true;
                    }

                    options.loading = false;

                    self.onAfterLoading();

                    if (options.done) {
                        self.onNothingMoreToLoad();
                    }
                })
                .fail(function() {
                    console.log("Failed to load more.");
                });
        });
    });
};