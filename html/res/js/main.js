var _vueHtml, _vueObj;


var routes = {
    '/jobs/:jobId': function(jobId) {
        loadModule('jobs/single', {jobId: jobId});
    },
    '/jobs': function() {
        loadModule('jobs/list');
    },
    '/domains/:domainId': function(domainName) {
        loadModule('domains/single', {domainName: domainName});
    },
    '/domains': function() {
        loadModule('domains/list');
    },
    '/': function() {
        loadModule('domains/list');
    }
};

/**
 * Route the request
 */
var router = new Router(routes);
router.init();

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

        console.log('loadModule', module, (params || {}));
        _vueObj.data.params = params || {};
        _vueObj.el = '#vue-app';

        if (window.App && window.App.$destroy)
            window.App.$destroy();

        window.App = new Vue(_vueObj);

        var moduleParts = module.split('/');
        window.SideBar.setActive(moduleParts[0]);
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

/**
 * Globally loaded NavBar
 * @type {Vue}
 */
var NavBar = new Vue({
    el: '#vue-nav-bar'
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
                title: 'Domains',
                href: '#/',
                active: false,
            },
            jobs: {
                title: 'Jobs',
                href: '#/jobs',
                active: false,
            }
        },
    },
    methods: {
        setActive: function(module) {
            for (var i in this.links)
                this.links[i].active = false;

            if (this.links[module])
                this.links[module].active = true;
        }
    }
});