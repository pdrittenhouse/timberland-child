/**
 * Default Grunt control
 *
 * CRITICAL NOTE:
 * - `grunt watch` is expected to be used during development. It will not minify the output.
 * - `grunt` is expected to be used prior to deployment to production.
 *
 * For best results, include ""prefix": "../"," in package.json to make sure
 * the existing node_module folder is used without accidental nesting.
 *
 *
 */
const sass = require('sass');
module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        copy: {
            main: {
                files: [
                    {
                        expand: true,
                        flatten: false, // flattens results to a single level
                        cwd: 'node_modules/bootstrap/scss/', // makes all src relative to cwd
                        src: ['vendor/**/*', 'mixins/**/*', 'helpers/**/*', 'utilities/**/*', '_functions.scss', '_variables*.scss', '_maps.scss', '_mixins.scss', '_utilities.scss', '_root.scss', '_reboot.scss'], // includes files within path
                        dest: 'src/libs/bootstrap/', //copy file to location
                        filter: 'isFile',
                    },
                    // {
                    //     expand: true,
                    //     flatten: false, // flattens results to a single level
                    //     cwd: 'node_modules/waypoints/lib/', // makes all src relative to cwd
                    //     src: ['**/*'], // includes files within path
                    //     dest: 'src/libs/waypoints/', //copy file to location
                    //     filter: 'isFile',
                    // },
                    // {
                    //     expand: true,
                    //     flatten: false, // flattens results to a single level
                    //     cwd: 'node_modules/isotope-layout/dist/', // makes all src relative to cwd
                    //     src: ['**/*'], // includes files within path
                    //     dest: 'src/libs/isotope/', //copy file to location
                    //     filter: 'isFile',
                    // }
                ],
            },
        },
        svg_sprite: {
            dist: {
                expand: false,
                cwd: 'src/icons/svg',
                src: ['*.svg'],
                dest: 'dist',
                options: {
                    shape: {
                       transform: [{
                           svgo: {
                               plugins: [
                                   { name: 'preset-default' },
                                   { name: 'removeAttrs', params: { attrs: '(fill|stroke)' } },
                                   { name: 'removeStyleElement' }
                               ]
                           }
                       }]
                   },
                   mode: {
                        view: {
                            dest: '',
                            sprite: './svg/sprites/sprite',
                            bust: false,
                            render: {
                                scss: {
                                    template: 'src/icons/svg_sprite.template.scss.handlebars',
                                    dest: '../src/sass/global/_sprites'
                                }
                            }
                        },
                        symbol: {
                            dest: './svg',
                            inline: true
                        }
                    }
                }
            }
        },
        uglify: {
            options: {
                sourceMap : true
            },
            public: {
                files : {
                    "dist/scripts.min.js" :
                        [
                            "src/js/core.js",
                            "src/js/parts/**/*.part.js"
                        ],
                    "dist/admin.min.js" :
                        [
                            "src/js/admin.js"
                        ]
                }
            }
        },
        sass : {
            options : {
                implementation: sass,
                sourceMap : true,
                outputStyle : "expanded"
            },
            public : {
                files : {
                    "dist/styles.css" : "src/sass/styles.scss",
                    "dist/admin.css" : "src/sass/wordpress/admin/admin.scss",
                    "dist/editor.css" : "src/sass/wordpress/editor/editor.scss"
                }
            }
        },
        watch: {
            primary: {
                options: {
                    reload: false,
                    spawn: false,
                    interrupt: false,
                    livereload: true
                },
                files: [
                    "src/js/parts/**/*.js",
                    "src/sass/**/*.scss"
                ],
                tasks: ["uglify","sass"]
            },
            fonts: {
                options: {
                    reload: false,
                    spawn: false,
                    interrupt: false,
                    livereload: true
                },
                files: [
                    'source/fonts/fontello'
                ],
                tasks: ['fontello']
            }
        },
        postcss: {
            options: {
                processors: [
                    require('pixrem')(), // add fallbacks for rem units
                    require('autoprefixer')(), // add vendor prefixes
                    require('cssnano')({
                        preset: ['default', {
                            discardComments: { removeAll: true }, // Remove all comments
                            discardDuplicates: true // Remove duplicate CSS rules
                        }]
                    }) // minify the result
                ]
            },
            public: {
                map: {
                    inline: false, // Save all sourcemaps as separate files...
                    annotation: "dist/" // ...to the specified directory
                },
                src: "dist/*.css"
            }
        },
        compress: {
            main: {
                options: {
                    mode: 'gzip'
                },
                expand: true,
                cwd: 'dist/',
                src: ['**/*.js', '**/*.css'],
                dest: 'dist/',
                ext: '.gz'
            }
        }
    });

    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-sass");
    grunt.loadNpmTasks("grunt-postcss");
    grunt.loadNpmTasks("grunt-contrib-uglify");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-svg-sprite");
    grunt.loadNpmTasks('grunt-contrib-compress');

    // Default task(s).
    // grunt.registerTask("default", ["svg_sprite","uglify","sass"]);
    grunt.registerTask("default", ["uglify","sass"]);
    grunt.registerTask("sprites", ["svg_sprite","sass"]);
    grunt.registerTask("init", ["copy", "svg_sprite", "uglify", "sass"]);
    // grunt.registerTask("init", ["copy", "uglify", "sass"]);
    grunt.registerTask("prod", ["svg_sprite", "uglify","sass","postcss", "compress"]);
};
