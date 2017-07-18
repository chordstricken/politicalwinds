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