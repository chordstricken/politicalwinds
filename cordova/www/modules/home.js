var _vueObj = {
    el: '#vue-app',
    data: {
        alert: {},
        isbusy: true,
        query: '',
        searchResults: {},
        _index: {},

        myMembers: DB.get('my.members') || {},
    },
    created: function() {
        this.isbusy = false;
    },
    computed: {
        resultCount: function() {
            return Object.keys(this.searchResults).length;
        }
    },
    methods: {

        /**
         * Uses geolocation to find representatives
         */
        findMyReps: function() {
            var scope = this;
            if (!navigator.geolocation) {
                scope.alert = {error: "Geolocator not found."};
                return false;
            }
            var latlng   = [33.45551, -112.0693319];
            var state    = false;
            var distance = false;

            getStateFromLocation(latlng);

            //
            // navigator.geolocation.getCurrentPosition(function success(pos) {
            //     console.log(pos.coords.latitude, pos.coords.longitude)
            // }, function error(msg) {
            //
            // });
        },

        /**
         * Iterates through member index and sets matches in searchResults
         */
        showSearchResults: function() {
            var scope = this, i;
            scope.searchResults = {};

            var rexpQuery = new RegExp(this.query.replace(' ', '.*'), 'i');

            for (i in scope._index) {
                var member = scope._index[i];

                var isMatch = i.match(rexpQuery);
                isMatch = isMatch || member.name && member.name.match(rexpQuery);
                isMatch = isMatch || member.state && member.state.match(rexpQuery) || stateFull(member.state || '').match(rexpQuery);

                if (isMatch) scope.searchResults[i] = member;

            }

            scope.isbusy = false;
        },

        /**
         * Manually searches for representatives
         */
        searchForReps: function() {
            var scope = this;
            if (!scope._index) {
                scope.isbusy = true;
                $.get('/api/static/us/congress.json', function(result) {
                    scope._index = result instanceof Object ? result : JSON.parse(result);
                    scope.showSearchResults();
                });

            } else {
                scope.showSearchResults()

            }

        }
    }
}