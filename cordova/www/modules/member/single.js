var _vueObj = {
    el: '#vue-app',
    data: {
        params: {},
        isbusy: true,
        member: {},
    },
    created: function() {
        var scope = this;
        $.getJSON('/api/static/members/' + scope.params.memberId[0] + '/' + scope.params.memberId + '.json', function(result) {
            scope.member = result;
            scope.isbusy = false;
            scope.$forceUpdate();
        });
    },

    computed: {
        currentTerm: function() {
            return this.member.terms[this.member.terms.length - 1];
        },
    },

    methods: {
    }
};