var app = angular.module('twsApp', ['ngRoute', 'ngAnimate', 'toaster', 'ngTouch', 'ui.bootstrap', 'angularMoment', 'daterangepicker', 'datatables', 'datatables.buttons']);

app.config(['$routeProvider',
            function ($routeProvider) {
                $routeProvider.
                when('/login', {
                    title: 'Login',
                    templateUrl: 'partials/login.html',
                    controller: 'twsCtrl'
                })
                    .when('/logout', {
                    title: 'Logout',
                    templateUrl: 'partials/login.html',
                    controller: 'twsCtrl'
                })
                    .when('/signup', {
                    title: 'Signup',
                    templateUrl: 'partials/signup.html',
                    controller: 'twsCtrl'
                })
                    .when('/dashboard', {
                    title: 'Dashboard',
                    templateUrl: 'partials/dashboard.html',
                    controller: 'twsCtrl'
                })
                    .when('/admin', {
                    title: 'Admin wine list',
                    templateUrl: 'partials/admin.html',
                    controller: 'twsCtrl'
                })
                    .when('/catalog-list', {
                    title: 'Catalog List',
                    templateUrl: 'partials/catalog-list.html',
                    controller: 'twsCtrl'
                })
                    .when('/catalog-grid', {
                    title: 'Catalog Grid',
                    templateUrl: 'partials/catalog-grid.html',
                    controller: 'twsCtrl'
                })
                    .when('/winespy', {
                    title: 'Winespy',
                    templateUrl: 'partials/winespy.html',
                    controller: 'twsCtrl'
                })
                    .when('/lastwine', {
                    title: 'Lastwine downloaded',
                    templateUrl: 'partials/lastwine.html',
                    controller: 'twsCtrl'
                })
                    .when('/', {
                    title: 'WineSpy',
                    templateUrl: 'partials/winespy.html',
                    controller: 'twsCtrl',
                    role: '0'
                })
                    .otherwise({
                    redirectTo: '/login'
                });
            }])
    .run(function ($rootScope, $location, Data) {
    $rootScope.$on("$routeChangeStart", function (event, next, current) {
        $rootScope.authenticated = false;
        Data.get('session').then(function (results) {
            if (results.uid) {
                $rootScope.authenticated = true;
                $rootScope.role = results.role;
                $rootScope.uid = results.uid;
            } else {
                var nextUrl = next.$$route.originalPath;
                if (nextUrl == '/signup') {
                    $location.path("/signup");
                } else {
                    $location.path("/login");
                }
            }
        });
        
        /* 
        *   Logout the user
        *   @params {none}
        */
        $rootScope.logout = function () {
            Data.get('logout').then(function (results) {
                Data.toast(results);
                $location.path('login');
            });
        };
    });
});
