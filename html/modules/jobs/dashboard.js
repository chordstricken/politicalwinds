var _vueObj = {
    data: {
        counts: {},
        jobs: {},
    },
    created: function() {
        var scope = this;
        scope.search({status: 'queued'});

        scope.getCounts();
        setInterval(scope.getCounts, 5000);
    },
    methods: {
        getCounts: function() {
            var countType,
                scope = this;

            $.getJSON('/api/jobs/dashboard-counts', function(result) {
                var newCounts = {};
                for (var i in result) {
                    var jobName = result[i].name.split('\\').pop();

                    if (!newCounts[jobName])
                        newCounts[jobName] = {
                            queued: 0,
                            in_progress: 0,
                            complete: 0,
                            failed: 0,
                        };

                    newCounts[jobName][result[i].status] = result[i].total;
                }
                scope.counts = newCounts;
            });

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