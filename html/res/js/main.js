var _vueHtml, _vueObj;
if (!location.hash) location.href = "/#/";


var routes = {
    '/jobs/v/:jobId': function(jobId) {
        loadModule('jobs/single', {jobId: jobId});
    },
    '/jobs': function() {
        loadModule('jobs/dashboard');
    },
    '/domains/new': function() {
        loadModule('domains/new');
    },
    '/domains/v/:domainId': function(domainName) {
        loadModule('domains/single', {domainName: domainName});
    },
    '/domains': function() {
        loadModule('domains/list');
    },
    '/': function() {
        location.hash = '#/domains';
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
        modules: {
            domains: {
                title: 'Domains',
                href: '#/domains',
            },
            jobs: {
                title: 'Jobs',
                href: '#/jobs',
            },
        }
    },
    methods: {
        isActive: function(module) {
            return location.hash.indexOf(module.href) === 0;
        },
    },
});

/**
 * Globally loaded Sidebar
 * @type {Vue}
 */
var SideBar = new Vue({
    el: '#vue-sidebar',
    data: {
        links: {
            domains: {
                '#/domains': 'List Domains',
                '#/domains/new': 'New Domain',
            },
            jobs: {
                '#/jobs': 'Jobs Dashboard',
                '#/jobs/schedule': 'Schedule Job',
            }
        },
    },
    methods: {
        activeLinks: function() {
            var module = location.hash.replace(/^\#\//, '').split('/')[0];
            return this.links[module] || {};
        },
        isActive: function(link) {
            return link === location.hash;
        }
    }
});


/**
 * Clears the page and puts in a 'not found' alert
 */
function pageNotFound() {
    $app.html("<div class='alert alert-danger'>404: Page not found.</div>");
}

/**
 * @param module
 * @param params
 */
function loadModule(module, params) {
    var waiting = 2;

    if (module[0] === '/')
        module = module.substr(1);

    function done() {
        if (waiting) return;

        // don't cache this selector. Creating a new Vue instance clones it and the ref becomes stale.
        $('#vue-app').html(_vueHtml);

        // console.log('loadModule', module, (params || {}));
        _vueObj.data.params = params || {};
        _vueObj.el = '#vue-app';

        if (window.App && window.App.$destroy)
            window.App.$destroy();

        window.App = new Vue(_vueObj);

        NavBar.$forceUpdate();
        SideBar.$forceUpdate();
    }

    $.get({
        url: '/modules/' + module + '.html',
        success: function(html) {
            waiting--;
            _vueHtml = html;
            done();
        },
        error: pageNotFound,
    });

    $.getScript('/modules/' + module + '.js', function(js) {
        waiting--;
        done();
    });
}
