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
        getMemberHeadshot: function(member) {
            return '/api/members/photos/' + member.id.bioguide[0] + '/' + member.id.bioguide + '.jpg';
            // return 'background-image: url(img/headshot-default.svg), url("' + headshot + '");'
        },

        search_show_results: function() {
            var scope = this, i;
            scope.results = {};

            var rexpQuery = new RegExp(this.query.replace(' ', '.*'), 'i');

            for (i in scope._index) {
                var member = scope._index[i];

                if (member.name && member.name.official_full && member.name.official_full.match(rexpQuery))
                    scope.results[i] = member;
                else if (i.match(rexpQuery))
                    scope.results[i] = member;

            }

            scope.isbusy = false;
        },
        search: function() {
            var scope = this;
            if (!scope._index) {
                scope.isbusy = true;
                $.get('/api/us/current.json', function(result) {
                    scope._index = result instanceof Object ? result : JSON.parse(result);
                    scope.search_show_results();
                });

            } else {
                scope.search_show_results()

            }

        }
    }
}