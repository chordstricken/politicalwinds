$(document).on('click', '[href]', function(e) {
    var href = $(this).attr('href');
    if (href[0] === '#') {
        e.preventDefault();
        location.href = href;
    }
});

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

var states = {
    'AL': 'Alabama',
    'AK': 'Alaska',
    'AZ': 'Arizona',
    'AR': 'Arkansas',
    'CA': 'California',
    'CO': 'Colorado',
    'CT': 'Connecticut',
    'DC': 'District of Columbia',
    'DE': 'Delaware',
    'FL': 'Florida',
    'GA': 'Georgia',
    'HI': 'Hawaii',
    'IA': 'Iowa',
    'ID': 'Idaho',
    'IL': 'Illinois',
    'IN': 'Indiana',
    'KS': 'Kansas',
    'KY': 'Kentucky',
    'LA': 'Louisiana',
    'ME': 'Maine',
    'MD': 'Maryland',
    'MA': 'Massachusetts',
    'MI': 'Michigan',
    'MN': 'Minnesota',
    'MS': 'Mississippi',
    'MO': 'Missouri',
    'MT': 'Montana',
    'NE': 'Nebraska',
    'NV': 'Nevada',
    'NH': 'New Hampshire',
    'NJ': 'New Jersey',
    'NM': 'New Mexico',
    'NY': 'New York',
    'NC': 'North Carolina',
    'ND': 'North Dakota',
    'OH': 'Ohio',
    'OK': 'Oklahoma',
    'OR': 'Oregon',
    'PA': 'Pennsylvania',
    'RI': 'Rhode Island',
    'SC': 'South Carolina',
    'SD': 'South Dakota',
    'TN': 'Tennessee',
    'TX': 'Texas',
    'UT': 'Utah',
    'VT': 'Vermont',
    'VA': 'Virginia',
    'WA': 'Washington',
    'WV': 'West Virginia',
    'WI': 'Wisconsin',
    'WY': 'Wyoming',
};
function stateFull(abbrev) {
    return states[abbrev.toUpperCase()] || 'N/A';
}

/** Member Index Object **/
function getMemberHeadshot(member) {
    return '/api/static/members/photos/' + member.id[0] + '/' + member.id + '.jpg';
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

Vue.component('Loader', {
    props: ['isbusy'],
    template: '<div class="text-center h1" v-show="isbusy"><i class="fa fa-spinner fa-spin"></i> Loading...</div>'
});