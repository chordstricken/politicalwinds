(function() { "use strict"; })();
var console = console || {log: function() {}, error: this.log};
/**
 * WP-Json interface with API Key handling
 * @type {{apiKey, setApiKey: Window.API.setApiKey, get: Window.API.get, post: Window.API.post, send: Window.API.send}}
 */
window.API = {
    pending: {},

    getPath: function(path) {
        return 'http://d2fgvqi101p3vq.cloudfront.net' + path;
        // return 'http://politics.blackmast.org' + path;
        // return 'http://s3-us-west-2.amazonaws.com/politics.blackmast.org' + path;
    },

    get: function (jqXHR) {
        jqXHR.method = 'get';
        return this.send(jqXHR);
    },
    getJSON: function (jqXHR) {
        jqXHR.method   = 'get';
        jqXHR.dataType = 'json';
        return this.send(jqXHR);
    },
    post: function (jqXHR) {
        jqXHR.method = 'post';
        return this.send(jqXHR);
    },

    send: function (jqXHR) {
        var scope = this;
        jqXHR = jqXHR || {};

        if (!jqXHR.path)
            return console.error('API action not set');

        // duplicate call protection
        jqXHR.id          = jqXHR.id || uniqueId();
        jqXHR.crossDomain = true;

        jqXHR.url = this.getPath(jqXHR.path);
        return this.pending[jqXHR.id] = $.ajax(jqXHR).done(function(data, status, xhr) {
            delete scope.pending[xhr.id];
        });

    },

    abortPending: function() {
        for (var id in this.pending) {
            if (this.pending[id].abort)
                this.pending[id].abort();
            delete this.pending[id];
        }
    }
}

/**
 * Local Storage database
 * @type {{set: DB.set, get: DB.get, del: DB.del}}
 */
var DB = {
    set: function(key, val) {
        localStorage.setItem(key, JSON.stringify(val));
    },
    get: function(key) {
        return JSON.parse(localStorage.getItem(key));
    },
    del: function(key) {
        localStorage.removeItem(key);
    },
};


function isNumber(param) {
    return typeof param === "number";
}
function isObject(param) {
    return typeof param === "object";
}
function isString(param) {
    return typeof param === "string";
}

function getDateTime(timestamp) {
    if (!timestamp || timestamp < 2000)
        return '';

    var d = new Date(timestamp * 1000);
    return d.toLocaleString()
}

function numberFormat(num, precision) {
    precision = precision === undefined ? 2 : precision;
    return parseFloat(num).toFixed(precision)
}

function timeFormat(seconds) {
    var res = '';
    if (!seconds)
        return 'N/A';

    if (seconds >= 86400) {
        var days = Math.floor(seconds / 86400);
        seconds -= 86400 * days;
        res += days + 'd';
    }

    if (seconds >= 3600) {
        var hours = Math.floor(seconds / 3600);
        seconds -= hours * 3600;
        res += ' ' + hours + 'h';
    }

    if (seconds >= 60) {
        var minutes = Math.floor(seconds / 60);
        seconds -= minutes * 60;
        res += ' ' + minutes + 'm';
    }

    if (seconds >= 0)
        res += ' ' + seconds + 's';

    return res.trim();
}

var stateCenters = {
    AL: {longitude: -86.461598260826, latitude: 31.558323254783},
    AK: {longitude: -129.53264113906, latitude: 57.411171722093},
    AZ: {longitude: -114.13200902023, latitude: 34.490083790771},
    AR: {longitude: -91.167689752395, latitude: 34.825612736681},
    CA: {longitude: -119.56620152122, latitude: 35.775650065703},
    CO: {longitude: -105.22945513846, latitude: 39.168746103846},
    CT: {longitude: -72.638462730942, latitude: 41.607331520179},
    DC: {longitude: -77.053584849558, latitude: 38.868182946903},
    DE: {longitude: -75.447814143939, latitude: 39.463020166667},
    FL: {longitude: -83.368063722611, latitude: 29.025065603963},
    GA: {longitude: -82.649539542271, latitude: 32.347425083092},
    HI: {longitude: -159.71067229186, latitude: 21.692225018663},
    IA: {longitude: -94.590052428619, latitude: 42.264647441354},
    ID: {longitude: -114.51303218042, latitude: 45.559912951595},
    IL: {longitude: -89.220247291144, latitude: 38.974626950119},
    IN: {longitude: -86.941168252252, latitude: 38.50597001138},
    KS: {longitude: -96.376621511013, latitude: 39.161692371513},
    KY: {longitude: -84.947206633593, latitude: 37.697949235457},
    LA: {longitude: -91.907744448892, latitude: 30.940426027589},
    ME: {longitude: -69.589760227318, latitude: 45.418792212692},
    MD: {longitude: -77.962420463816, latitude: 39.14216562218},
    MA: {longitude: -70.919473435816, latitude: 41.83679059271},
    MI: {longitude: -87.917637122084, latitude: 45.615777607444},
    MN: {longitude: -95.020240810605, latitude: 47.051377446396},
    MS: {longitude: -90.461896107143, latitude: 32.222255843985},
    MO: {longitude: -91.51883894735, latitude: 38.146070565186},
    MT: {longitude: -113.34723555036, latitude: 45.892642106115},
    NE: {longitude: -97.180436201852, latitude: 41.610369784568},
    NV: {longitude: -115.25212658983, latitude: 37.052441305085},
    NH: {longitude: -71.626214916603, latitude: 44.134476194465},
    NJ: {longitude: -74.935092290061, latitude: 40.277732773834},
    NM: {longitude: -105.99729408333, latitude: 33.596915383929},
    NY: {longitude: -74.396213503731, latitude: 42.916848325249},
    NC: {longitude: -81.824489135663, latitude: 35.599164577978},
    ND: {longitude: -97.205517120421, latitude: 47.373020252155},
    OH: {longitude: -82.26610151315, latitude: 39.348933544711},
    OK: {longitude: -97.190786443625, latitude: 34.155530526882},
    OR: {longitude: -120.0583775197, latitude: 44.5658378399},
    PA: {longitude: -75.624571789923, latitude: 41.014506524338},
    RI: {longitude: -71.546939847584, latitude: 41.496174762082},
    SC: {longitude: -81.839538039142, latitude: 33.666559879735},
    SD: {longitude: -97.184383966861, latitude: 43.370411375731},
    TN: {longitude: -84.996913348267, latitude: 35.84034069926},
    TX: {longitude: -99.31935176126, latitude: 30.579847097291},
    UT: {longitude: -111.73786616296, latitude: 39.462916355556},
    VT: {longitude: -72.411699543783, latitude: 44.066145496935},
    VA: {longitude: -79.969351740788, latitude: 37.894487998306},
    WA: {longitude: -121.48105397368, latitude: 46.910835853547},
    WV: {longitude: -80.289594140083, latitude: 38.63614732656},
    WI: {longitude: -90.150134359307, latitude: 45.312952414585},
    WY: {longitude: -107.72223330104, latitude: 43.124743342561}
};

var states = {
    'AL': 'Alabama',                'MT': 'Montana',
    'AK': 'Alaska',                 'NE': 'Nebraska',
    'AZ': 'Arizona',                'NV': 'Nevada',
    'AR': 'Arkansas',               'NH': 'New Hampshire',
    'CA': 'California',             'NJ': 'New Jersey',
    'CO': 'Colorado',               'NM': 'New Mexico',
    'CT': 'Connecticut',            'NY': 'New York',
    'DC': 'District of Columbia',   'NC': 'North Carolina',
    'DE': 'Delaware',               'ND': 'North Dakota',
    'FL': 'Florida',                'OH': 'Ohio',
    'GA': 'Georgia',                'OK': 'Oklahoma',
    'HI': 'Hawaii',                 'OR': 'Oregon',
    'IA': 'Iowa',                   'PA': 'Pennsylvania',
    'ID': 'Idaho',                  'RI': 'Rhode Island',
    'IL': 'Illinois',               'SC': 'South Carolina',
    'IN': 'Indiana',                'SD': 'South Dakota',
    'KS': 'Kansas',                 'TN': 'Tennessee',
    'KY': 'Kentucky',               'TX': 'Texas',
    'LA': 'Louisiana',              'UT': 'Utah',
    'ME': 'Maine',                  'VT': 'Vermont',
    'MD': 'Maryland',               'VA': 'Virginia',
    'MA': 'Massachusetts',          'WA': 'Washington',
    'MI': 'Michigan',               'WV': 'West Virginia',
    'MN': 'Minnesota',              'WI': 'Wisconsin',
    'MS': 'Mississippi',            'WY': 'Wyoming',
    'MO': 'Missouri',
};

function stateFull(abbrev) {
    return states[abbrev.toUpperCase()] || 'N/A';
}

/** Member Index Object **/
function getMemberHeadshot(member) {
    var mId = member.id ? member.id.toString() : '0';
    return API.getPath('/api/static/members/photos/' + mId[0] + '/' + mId + '.jpg');
}
/**
 * member headshot style
 * @param member
 * @returns {string}
 */
function getHeadshotBackgroundImage(member) {
    return 'url(' + getMemberHeadshot(member) + '), url(/res/img/headshot-default.svg);';
}

/**
 * Outputs a pretty version of the member's Office
 * @param office
 * @returns {*}
 */
function getOfficeLabel(office) {
    switch (office) {
        case 'rep': return 'Representative';
        case 'sen': return 'Senator';
        case 'prez': return 'President';
        case 'viceprez': return 'Vice President';
        default: return office;
    }
}

/**
 * Outputs a party label
 * @param party
 * @returns {*}
 */
function getPartyLabelShort(party) {
    switch (party.toLowerCase()) {
        case 'democrat': return '<span class="label label-primary">Dem</span>';
        case 'republican': return '<span class="label label-danger">Rep</span>';
        case 'libertarian': return '<span class="label label-warning">Lib</span>';
        case 'independent': return '<span class="label label-default">Ind</span>';
        default: return '<span class="label label-default">' + party + '</span>';
    }
}

/**
 * Outputs a party label
 * @param party
 * @returns {*}
 */
function getPartyLabelLong(party) {
    switch (party.toLowerCase()) {
        case 'democrat': return '<span class="label label-primary">' + party + '</span>';
        case 'republican': return '<span class="label label-danger">' + party + '</span>';
        case 'libertarian': return '<span class="label label-warning">' + party + '</span>';
        default: return '<span class="label label-default">' + party + '</span>';
    }
}

function getDatePretty(date) {
    return new Date(date).toLocaleDateString();
}

var _asynccache = {};
function loadStylesheet(path) {
    path = encodeURI(path);
    if (_asynccache[path]) return;
    $('body').append("<link rel='stylesheet' type='text/css' href='" + path + "'>");
    _asynccache[path] = true;
}
function loadScript(path) {
    path = encodeURI(path);
    if (_asynccache[path]) return;
    $('body').append("<script type='text/javascript' src='" + path + "'></script>");
    _asynccache[path] = true;
}

// function selectText($el)

function uniqueId() {
    return 'r' + Math.random().toString().replace(/^\d+\./, '');
}

function json_encode(param) {
    try {
        return JSON.stringify(param);
    } catch (e) {
        console.error(e);
        return null;
    }
}
function json_decode(param) {
    try {
        return JSON.parse(param);
    } catch (e) {
        console.error(e);
        return null;
    }
}

/**
 * Clones an object
 * @param obj
 */
function clone(obj) {
    return $.extend(true, {}, obj);
}

/**
 * @param obj
 * @returns {*}
 */
function count(obj) {
    return isObject(obj) ? Object.keys(obj).length : 0;
}

/**
 * Converts the values of an object into an array
 * @param obj
 * @returns {*}
 */
function objectValues(obj) {
    return Object.keys(obj).reduce(function(total, curr) {
        total.push(obj[curr]);
        return total;
    }, []);
}

/**
 * Acquires a State based on the provided location
 * @param loc {latitude, longitude}
 * @param done
 */
function getStateFromLocation(loc, done) {
    var sArray = Object.keys(window.states);
    var locArray = [parseFloat(loc.longitude), parseFloat(loc.latitude)];

    // Sort states by distance from loc based on state centers
    var sDistance = {};
    for (var state in stateCenters) {
        sDistance[state] = haversine(loc, stateCenters[state], {unit: 'mile'});
    }
    sArray.sort(function(a, b) {
        return sDistance[a] > sDistance[b] ? 1 : -1;
    });

    function checkState(state, cb) {
        if (!state) {
            done(false);
            return;
        }

        API.getJSON({
            path: '/api/static/us/states/' + state + '/shape.geojson',
            success: function(json) {
                for (var i in json.coordinates) {
                    for (var j in json.coordinates[i]) {
                        if (inside(locArray, json.coordinates[i][j])) {
                            done(state);
                            return;
                        }
                    }
                }

                checkState(sArray.shift());
            }
        });
    }

    checkState(sArray.shift());
}

/**
 * Acquires the district based on location
 * @param state String
 * @param loc {latitude, longitude}
 * @param done
 */
function getDistrictFromLocation(state, loc, done) {
    var locArray = [loc.longitude, loc.latitude];


    API.getJSON({
        path: '/api/static/us/states/' + state + '/districts.geojson',
        success: function(json) {
            for (var d in json) {
                if (!json[d])
                    continue;

                for (var i in json[d].geometry.coordinates) {
                    for (var j in json[d].geometry.coordinates[i]) {
                        if (inside(locArray, json[d].geometry.coordinates[i][j])) {
                            done(parseInt(d));
                            return;
                        }
                    }
                }
            }

            done(false);
        },
        error: function() {
            done(false);
        }

    });

}





$(document).on('click', '[href]', function(e) {
    var href = $(this).attr('href');
    if (href[0] === '#') {
        e.preventDefault();

        if (href !== '#null')
            location.href = href;
    }
});


Vue.component('x-nav',    {template: '#x-nav-template', props: {active: String}});
Vue.component('x-loader', {template: '#x-loader-template'});
Vue.component('x-alerts', {template: '#x-alerts-template', props: {alerts: Object}});