var _vueObj = {
    el: '#vue-app',
    data: {
        params: {},
        isBusy: true,
        member: {},
        showRawData: false,
    },
    created: function() {
        var scope = this;

        API.getJSON({
            path: '/api/static/members/' + scope.params.memberId[0] + '/' + scope.params.memberId + '.json',
            success: function(result) {
                if (result.terms && result.terms.sort)
                    result.terms.sort(function(a, b) {
                        return a.start < b.start ? 1 : -1
                    });

                scope.member = result;
                scope.isBusy = false;
            }
        });
    },

    computed: {
        memberFullName: function() {
            var n = this.member.name || {first: 'Unknown'};
            console.log(n);
            return [n.first, n.middle, n.last].filter(isString).join(' ');
        },
        currentTerm: function() {
            if (!this.member.terms || !this.member.terms.length) {
                console.error('Member terms not set.', this.member.terms);
                return {};
            }
            return this.member.terms[0];
        },
    },

    methods: {
    }
};