var _vueObj = {
    data: {
        params: {},
        alert: {},
        error: false,
        domain: {
            name: '',
        },
    },
    methods: {
        submit: function() {
            var scope = this;

            // validate
            if (!scope.domain.name.match(/[^\.]\..+/)) {
                scope.alert = {error: "Please provide a valid domain"};
                return;
            }

            $.post({
                dataType: 'json',
                url: '/api/domains/new',
                data: scope.domain,
                success: function(result) {
                    scope.alert = {success: "Successfully added the domain."};
                },
                error: function(jqXHR) {
                    scope.alert = {error: jqXHR.responseText};
                }

            });
        },
    },
};