var _vueObj = {
    data: {
        params: {},
        error: false,
        domain: {},
    },
    created: function() {
        var scope = this;
        console.log(scope);
        $.get({
            dataType: 'json',
            url: '/api/domains/get?name=' + scope.params.domainName,
            success: function(result) {
                scope.error  = false;
                scope.domain = result;
            },
            error: function(jqXHR) {
                scope.error = jqXHR.responseText;
            },
        });
    },
    methods: {

    },
};