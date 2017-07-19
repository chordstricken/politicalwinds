var _vueObj = {
    data: {
        searchQuery: '',
        domains: {},
    },
    created: function() {
        var scope = this;
        $.getJSON('/api/domains/list', function(result) {
            scope.setDomains(result);
        });
    },
    methods: {
        setDomains: function(domains) {
            // sanitize domains
            for (var i in domains) {
                domains[i].scrape  = domains[i].scrape instanceof Object ? domains[i].scrape : {};
                domains[i].moz     = domains[i].moz instanceof Object ? domains[i].moz : {};
                domains[i].sitemap = domains[i].sitemap instanceof Object ? domains[i].sitemap : {};
            }

            this.domains = domains;
        },

        search: function() {
            var scope = this;
            $.get({
                url: '/api/domains/list',
                dataType: 'json',
                data: {query: scope.searchQuery},
                success: function(result) {
                    scope.setDomains(result);
                }
            });
        },
    },
};