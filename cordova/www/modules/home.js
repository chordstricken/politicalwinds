var _vueObj = {
    el: '#vue-app',
    data: {
        alert: {},
        isBusy: true,
        myMembers: DB.get('my.members') || {},
        results: {},
    },
    created: function() {
        var scope = this;

        if (!count(this.myMembers))
            location.href = '#/member/search';

        scope.getCongress(function(member) {
            return scope.myMembers[member.id];
        });
    },
    methods: {

        /**
         * @param isMatchFn {Function<member>}
         */
        getCongress: function(isMatchFn) {
            var scope = this;

            function indexFilter() {
                scope.results = {};
                for (var i in scope._index)
                    if (isMatchFn(scope._index[i]))
                        scope.results[i] = scope._index[i];

                scope.isBusy = false;
                scope.$forceUpdate();
            }

            if (!scope._index) {
                scope.isBusy = true;
                API.getJSON({
                    path: '/api/static/us/congress.json',
                    success: function(result) {
                        scope._index = result;
                        indexFilter();
                    }
                });

            } else {
                indexFilter();

            }
        },

    }
}