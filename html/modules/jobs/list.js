var _vueObj = {
    data: {
        searchQuery: "",
        counts: {
            queued: 0,
            in_progress: 0,
            complete: 0,
            failed: 0,
        },
        jobs: {},
    },
    created: function() {
        var scope = this;
        $.getJSON('/api/jobs/list', function(result) {
            scope.jobs = result;
        });

        scope.getCounts();
    },
    methods: {
        getCounts: function() {
            var countType,
                scope = this;

            var types = Object.keys(scope.counts);

            function getCount(type) {
                var queryString = JSON.stringify({status: type});
                $.get({
                    url: '/api/jobs/count',
                    data: {query: queryString},
                    success: function(count) {
                        scope.counts[type] = count;
                    }
                });
            }

            types.map(getCount);
        },
        search: function() {
            var scope = this;
            $.get({
                url: '/api/jobs/list',
                dataType: 'json',
                data: scope.searchQuery,
                success: function(result) {
                    scope.jobs = result;
                }
            });
        },
    },
};