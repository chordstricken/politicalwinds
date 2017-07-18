var _vueObj = {
    data: {
        params: {},
        error: false,
        job: {},
    },
    created: function() {
        var scope = this;
        $.get({
            dataType: 'json',
            url: '/api/jobs/get?id=' + scope.params.jobId,
            success: function(result) {
                scope.error  = false;
                scope.job = result;
            },
            error: function(jqXHR) {
                scope.error = jqXHR.responseText;
            },
        });
    },
    methods: {

    },
};