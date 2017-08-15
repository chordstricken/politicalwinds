module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt); // npm install --save-dev load-grunt-tasks

    grunt.initConfig({

        concat: {
            js: {
                options: {
                    separator: ";\n"
                },
                src: [
                    'bower_components/director/build/director.min.js',
                    'bower_components/jquery/dist/jquery.min.js',
                    'bower_components/bootstrap/dist/js/bootstrap.min.js',
                    'bower_components/vue/dist/vue.min.js',
                    'bower_components/haversine/haversine.js',
                    'bower_components/point-inside-polygon/index.js',
                ],
                dest: 'cordova/www/res/js/plugins.min.js'
            }
        },

        sass: {
            dist: {
                files: {
                    'cordova/www/res/css/index.css': 'cordova/www/res/css/index.scss'
                }
            }
        },

        watch: {
            styles: {
                files: [
                    'cordova/www/res/css/*.scss',
                ],
                tasks: ['sass']
            }
        }

    })

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.registerTask('default', ['concat', 'sass']);

}