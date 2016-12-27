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
                    .when('/addcustomer', {
                    title: 'Add a customer',
                    templateUrl: 'partials/addcustomer.html',
                    controller: 'twsCtrl'
                })
                    .when('/addpayment', {
                    title: 'Add a payment',
                    templateUrl: 'partials/addpayment.html',
                    controller: 'twsCtrl'
                })
                    .when('/winelist', {
                    title: 'Winelist',
                    templateUrl: 'partials/winelist.html',
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
                console.log(JSON.stringify(results));
                $rootScope.authenticated = true;
                $rootScope.role = results.role;
                $rootScope.sessionUid = results.uid;
                $rootScope.associated_to = results.associated_to;
                $rootScope.payment_is_set = results.payment_is_set;
                //force data input 
                if(results.associated_to == 0) {
                    $location.path("/addcustomer");
                    $location.hash('page-wrapper');
                }else if (results.payment_is_set == 0) {
                    $location.path("/addpayment");
                    $location.hash('page-wrapper');
                } 
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
