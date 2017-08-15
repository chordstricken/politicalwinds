var _vueHtml, _vueObj;
if (!location.hash) location.href = '#/';

var routes = {
    '/member/v/:memberId': function(memberId) {
        loadModule('member/single', {memberId: memberId});
    },
    '/member/search': function() {
        loadModule('member/search');
    },
    '/': function() {
        loadModule('home');
    }
};

/**
 * Route the request
 */
var router = new Router(routes);
router.init();


/**
 * Globally loaded NavBar
 * @type {Vue}
 */
var NavBar = new Vue({
    el: '#vue-nav-bar',
    data: {
        // modules: {
        //     domains: {
        //         title: 'Domains',
        //         href: '#/domains',
        //     },
        //     jobs: {
        //         title: 'Jobs',
        //         href: '#/jobs',
        //     },
        // }
    },
    methods: {
        isActive: function(module) {
            return location.hash.indexOf(module.href) === 0;
        },
    },
});

/**
 * @param module
 * @param params
 */
function loadModule(module, params) {
    var waiting = 2;

    if (module[0] === '/')
        module = module.substr(1);

    function doneLoadingView() {
        if (--waiting) return;

        if (window.App && window.App.$destroy) {
            window.App.$destroy();
            window.App = null;
        }

        // don't cache this selector. Creating a new Vue instance clones it and the ref becomes stale.
        $('#vue-app').empty();

        if (_vueObj) {
            _vueObj.data.params = params || {};
            _vueObj.el          = '#vue-app';
            _vueObj.template    = _vueHtml;
            window.App = new Vue(_vueObj);
        }
    }

    API.abortPending();

    $.get({
        url: 'modules/' + module + '.html',
        success: function(html) {
            _vueHtml = '<div id="vue-app">' + html + '</div>';
        },
    }).done(doneLoadingView);

    $.getScript('modules/' + module + '.js').done(doneLoadingView);
}
