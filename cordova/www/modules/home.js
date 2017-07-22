var _vueObj = {
    el: '#vue-app',
    data: {
        isbusy: true,
        query: '',
        results: {},
        _index: {},
    },
    created: function() {
        this.isbusy = false;
    },
    computed: {
        resultCount: function() {
            return Object.keys(this.results).length;
        }
    },
    methods: {
        /** Member Index Object **/
        getMemberHeadshot: function(member) {
            return '/api/static/members/photos/' + member.id[0] + '/' + member.id + '.jpg';
        },

        getOfficeLabel: function(office) {
            switch (office) {
                case 'rep': return 'Representative';
                case 'sen': return 'Senator';
                case 'prez': return 'President';
                case 'viceprez': return 'Vice President';
                default: return office;
            }
        },

        getPartyLabel: function(party) {
            switch (party.toLowerCase()) {
                case 'democrat': return '<span class="label label-primary">Dem</span>';
                case 'republican': return '<span class="label label-danger">Rep</span>';
                case 'libertarian': return '<span class="label label-warning">Lib</span>';
                case 'independent': return '<span class="label label-default">Ind</span>';
                default: return '<span class="label label-default">' + party + '</span>';
            }
        },

        search_show_results: function() {
            var scope = this, i;
            scope.results = {};

            var rexpQuery = new RegExp(this.query.replace(' ', '.*'), 'i');

            for (i in scope._index) {
                var member = scope._index[i];

                var isMatch = i.match(rexpQuery);
                isMatch = isMatch || member.name && member.name.match(rexpQuery);
                isMatch = isMatch || member.state && member.state.match(rexpQuery) || stateFull(member.state).match(rexpQuery);

                if (isMatch) scope.results[i] = member;

            }

            scope.isbusy = false;
        },
        search: function() {
            var scope = this;
            if (!scope._index) {
                scope.isbusy = true;
                $.get('/api/static/us/congress.json', function(result) {
                    scope._index = result instanceof Object ? result : JSON.parse(result);
                    scope.search_show_results();
                });

            } else {
                scope.search_show_results()

            }

        }
    }
}