// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.controllers' is found in controllers.js
angular.module('starter', ['ionic', 'starter.controllers'])

    .run(function($ionicPlatform) {
        $ionicPlatform.ready(function() {
            // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
            // for form inputs)
            if (window.cordova && window.cordova.plugins.Keyboard) {
                cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
            }
            if (window.StatusBar) {
                // org.apache.cordova.statusbar required
                StatusBar.styleDefault();
            }
        });
    })

    .config(function($stateProvider, $urlRouterProvider) {
        $stateProvider

            .state('app', {
                url: "/app",
                abstract: true,
                templateUrl: "templates/menu.html",
                controller: 'AppCtrl'
            })

            .state('app.search', {
                url: "/search",
                views: {
                    'menuContent': {
                        templateUrl: "templates/search.html"
                    }
                }
            })

            .state('app.browse', {
                url: "/browse",
                views: {
                    'menuContent': {
                        templateUrl: "templates/browse.html"
                    }
                }
            })

            .state('app.pages', {
                url: "/pages",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages.html",
                        controller: 'PagesController'
                    }
                }
            })

            .state('app.single', {
                url: "/pages/plenario",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/plenario.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.deputados', {
                url: "/pages/deputados",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/deputados.html",
                        controller: 'PartiesController'
                    }
                }
            })

            .state('app.deputado', {
                url: "/pages/congressman",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/deputados.html",
                        controller: 'PartiesController'
                    }
                }
            })

            .state('app.noticias', {
                url: "/pages/noticias",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/noticias.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.alo', {
                url: "/pages/alo",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/alo.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.comissoes', {
                url: "/pages/comissoes",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/comissoes.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.comissoesDefesaDoConsumidor', {
                url: "/pages/comissoes/defesaDoConsumidor",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/comissoes/defesaDoConsumidor.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.noticias1', {
                url: "/pages/noticias/1",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/noticias/1.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.diario', {
                url: "/pages/diario",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/diario.html",
                        controller: 'PageController'
                    }
                }
            })

            .state('app.aloAlerjChat', {
                url: "/pages/aloAlerjChat",
                views: {
                    'menuContent': {
                        templateUrl: "templates/pages/aloAlerjChat.html",
                        controller: 'PageController'
                    }
                }
            })

            ;

        // if none of the above states are matched, use this as the fallback
        $urlRouterProvider.otherwise('/app/pages');
    });
