var _vueObj = {
    data: {
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
        scope.search({status: 'queued'});
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
        getStatusLabel: function(status) {
            switch (status) {
                case 'queued':      return '<span class="label label-info">Queued</span>';
                case 'in_progress': return '<span class="label label-warning">In Progress</span>';
                case 'complete':    return '<span class="label label-success">Complete</span>';
                case 'failed':      return '<span class="label label-danger">Failed</span>';
            }
        },
        search: function(query) {
            var scope = this;
            $.get({
                url: '/api/jobs/list',
                dataType: 'json',
                data: {query: query},
                success: function(result) {
                    scope.jobs = result;
                }
            });
        },
    },
};