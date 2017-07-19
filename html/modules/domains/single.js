var _vueObj = {
    data: {
        params: {},
        alert: {},
        domain: {},
    },
    created: function() {
        var scope = this;
        $.get({
            dataType: 'json',
            url: '/api/domains/get?name=' + scope.params.domainName,
            success: function(result) {
                scope.alert  = {};
                scope.domain = result;
            },
            error: function(jqXHR) {
                scope.alert = {error: jqXHR.responseText};
            },
        });
    },
    methods: {
        schedule: function(jobType) {
            var scope = this;
            $.post({
                url: '/api/jobs/schedule',
                data: {
                    type: jobType,
                    params: {
                        domains: [scope.domain.name]
                    }
                },
                success: function(result) {
                    scope.alert = {success: "Successfully scheduled the job."};
                },
                error: function(jqXHR) {
                    scope.alert = {error: jqXHR.responseText};
                },
            });
        }
    },
};