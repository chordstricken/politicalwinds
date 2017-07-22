module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt); // npm install --save-dev load-grunt-tasks


    grunt.initConfig({

        uglify: {
            options: {
                mangle: false
            },
            dist: {
                files: {
                    'cordova/www/js/index.min.js': 'cordova/www/js/index.js'
                }
            }
        },

        concat: {
            js: {
                options: {
                    separator: ";\n"
                },
                src: [
                    'bower_components/jquery/dist/jquery.min.js',
                    'bower_components/bootstrap/dist/js/bootstrap.js',
                    'bower_components/vue/dist/vue.js',
                    'cordova/www/js/index.min.js',
                ],
                dest: 'cordova/www/js/app.min.js'
            }
        },

        watch: {
            files: [
                'cordova/www/js/*.js',
            ],
            tasks: ['default']
        },

        compile: {}
    })

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.registerTask('default', ['uglify', 'concat']);

}