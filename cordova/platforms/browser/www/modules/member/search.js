var _vueObj = {
    el: '#vue-app',
    data: {
        alerts: {},
        isBusy: {
            loading: true,
            findLoc: false,
        },
        query: '',
        searchResults: [],
        _congressIndex: {},

        myMembers: DB.get('my.members') || {},
    },
    created: function() {
        this.isBusy.loading = false;
    },
    computed: {
        resultCount: function() {
            return Object.keys(this.searchResults).length;
        }
    },
    methods: {
        toggleFollowing: function(mId) {
            if (this.myMembers[mId]) {
                delete this.myMembers[mId];
            } else {
                this.myMembers[mId] = 1;
            }

            DB.set('my.members', clone(this.myMembers));
            this.$forceUpdate();
        },

        /**
         * @param isMatchFn {Function<member>}
         */
        getCongress: function(isMatchFn) {
            var scope = this;

            if (!scope._congressIndex) {
                scope.isBusy.loading = true;
                API.getJSON({
                    path:    '/api/static/us/congress.json',
                    success: function(result) {
                        scope._congressIndex = result;
                        scope.searchResults  = objectValues(scope._congressIndex).filter(isMatchFn);
                        scope.isBusy.loading = false;
                    },
                });

            } else {
                scope.searchResults  = objectValues(scope._congressIndex).filter(isMatchFn);
                scope.isBusy.loading = false;

            }
        },

        /**
         * Uses geolocation to find representatives
         */
        findMyReps: function() {
            var scope = this;
            if (!navigator.geolocation) {
                scope.alerts = {error: "Geolocator not found."};
                return false;
            }

            scope.isBusy.findLoc = true;

            function doneFindingLoc() {
                scope.isBusy.findLoc = false;
            }

            // var latlng = {latitude: 47.5916181, longitude: -122.3312154}; // Seattle, WA
            // var latlng = {latitude: 58.3795683, longitude: -135.2974767}; // Alaskan island
            // var latlng  = {latitude: 33.45551, longitude: -112.0693319}; // Phoenix, AZ
            scope.query = '';

            navigator.geolocation.getCurrentPosition(function(loc) {
                console.log('location:', loc)

                getStateFromLocation(loc.coords, function(state) {
                    if (!state) {
                        scope.alerts = {error: 'State not found'};
                        return doneFindingLoc();
                    }

                    getDistrictFromLocation(state, loc.coords, function(district) {
                        console.log(state, district);
                        if (district === false) {
                            scope.alerts = {error: 'District not found'};
                            return doneFindingLoc();
                        }

                        scope.getCongress(function(member) {
                            var isMatch = false;
                            isMatch     = isMatch || (member.office === 'prez') || (member.office === 'viceprez');
                            isMatch     = isMatch || (!member.district && member.state === state);
                            isMatch     = isMatch || (member.state === state && member.district === district);
                            return isMatch;
                        });

                        return doneFindingLoc();

                    });
                });

            });

        },

        /**
         * Manually searches for representatives
         */
        searchForReps: function() {
            var scope     = this;
            var rexpQuery = new RegExp(this.query.replace(' ', '.*'), 'i');
            scope.getCongress(function(member) {

                var isMatch = member.id.toString().match(rexpQuery);
                isMatch     = isMatch || member.name && member.name.match(rexpQuery);
                isMatch     = isMatch || member.state && member.state.match(rexpQuery) || stateFull(member.state || '').match(rexpQuery);
                return isMatch;

            });
        }
    }
}