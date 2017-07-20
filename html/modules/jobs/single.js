var _vueObj = {
    data: {
        params: {},
        alert: {},
        error: false,
        job: {},
    },
    created: function() {
        var scope = this;
        $.ajaxSetup({
            error: scope.xhrError
        });

        $.get({
            dataType: 'json',
            url: '/api/jobs/get?id=' + scope.params.jobId,
            success: function(result) {
                scope.alert = {};
                scope.job = result;
            },
        });
    },
    methods: {
        xhrError: function(jqXHR) {
            this.alert = {error: jqXHR.responseText};
        },

        deleteJob: function() {
            $.get('/api/jobs/delete?id=' + this.job.id, function(result) {
                location.href = '#/jobs';
            });
        }
    },
};