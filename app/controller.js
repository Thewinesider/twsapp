app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, moment) {
    $scope.login = {};
    $scope.signup = {};

    /* 
    *   Do the login inside the app.
    *   @params {array}
    */
    $scope.doLogin = function (customer) {
        Data.post('login', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('winespy');
            }
        });
    };

    /* 
    *   Get the wine list of the logged user
    *   @params {none}
    *   @return {json} the wine list
    */
    $scope.getWineList = function () {
        Data.get('getProducerList').then(function (results) {
            $scope.producers = results;
        });
        Data.get('getRegionList').then(function (results) {
            $scope.regions = results;
        });
        Data.get('getWineList').then(function (results){
            $scope.wines = results;
        });
    };

    /* 
    *   Get the TWS full catalog
    *   @params {none}
    *   @return {json} the wine list
    */
    $scope.getFullCatalog = function() {
        Data.get('getProducerList').then(function (results) {
            $scope.producers = results;
        });
        Data.get('getRegionList').then(function (results) {
            $scope.regions = results;
        });
        Data.get('getFullCatalog').then(function (results) {
            $scope.wines = results;
        });
    };

    /* 
    *   Add a new user to the DB
    *   @params {array}
    *   @return true
    */
    $scope.signUp = function (customer) {
        Data.post('signUp', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('winespy');
            }
        });
    };

    /* 
    *   Logout the user
    *   @params {none}
    */
    $scope.logout = function () {
        Data.get('logout').then(function (results) {
            Data.toast(results);
            $location.path('login');
        });
    };

    /* 
    *   Get the daily wine sold from the logged user 
    */ 
    $scope.getWineSoldDaily = function () {
        /* MOMENTJS PERIOD TO BE DEFINED */
        var data;
        Data.post('getWineSoldDaily', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function (results){
            $scope.period = _.keys(_.omit(results[0], 'total'));
            $scope.value = _.values(_.omit(results[0], 'total'));
            $scope.totalBottle = results[0].total;
            $scope.drawChart($scope.period, $scope.value);
        });
        Data.post('getWineSoldList', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function(results){
            $scope.wines = results;
        });
    };

    /* 
    *   Get the last week of sale from the logged user 
    */ 
    $scope.getWineSoldLastWeek = function () {
        $scope.periodStart = new moment().subtract(1, 'weeks').startOf('isoWeek').format("YYYY-MM-DD 12:00:00");
        $scope.periodEnd = new moment().subtract(1, 'weeks').endOf('isoWeek').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        Data.post('getWineSoldWeekly', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function (results){
            $scope.period = _.keys(_.omit(results[0], 'total'));
            $scope.value = _.values(_.omit(results[0], 'total'));
            $scope.totalBottle = results[0].total;
            $scope.drawChart($scope.period, $scope.value);
        });
        Data.post('getWineSoldList', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function(results){
            $scope.wines = results;
        });
    };

    /* 
    *   Get this week of sales from the logged user 
    */ 
    $scope.getWineSoldThisWeek = function () {
        $scope.periodStart = new moment().startOf('isoWeek').format("YYYY-MM-DD 12:00:00");
        $scope.periodEnd = new moment().endOf('isoWeek').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        Data.post('getWineSoldWeekly', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function (results){
            $scope.period = _.keys(_.omit(results['bottles'][0], 'total'));
            $scope.bottles = _.values(_.omit(results['bottles'][0], 'total'));
            $scope.revenue = _.values(_.omit(results['revenue'][0], 'total'));
            $scope.totalBottle = results['bottles'][0].total;
            $scope.totalRevenue = results['revenue'][0].total;
            $scope.wines = results['wines'];
            $scope.typeValue = _.pluck(results['type'], 'sold');
            $scope.typeNames = _.pluck(results['type'], 'type');
            $scope.drawChartMix("#main", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
        });
    };

    /* 
    *   Get the daily wine sold from the logged user 
    */ 
    $scope.getWineSoldMonthly = function () {
        $scope.periodStart = new moment().startOf('month').format("YYYY-MM-DD 12:00:00");
        $scope.periodEnd = new moment().endOf('month').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        Data.post('getWineSoldMonthly', {
            periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function (results){
            $scope.period = _.keys(_.omit(results['bottles'][0], 'total'));
            $scope.bottles = _.values(_.omit(results['bottles'][0], 'total'));
            $scope.revenue = _.values(_.omit(results['revenue'][0], 'total'));
            $scope.totalBottle = results['bottles'][0].total;
            $scope.totalRevenue = results['revenue'][0].total;
            $scope.wines = results['wines'];
            $scope.typeValue = _.pluck(results['type'], 'sold');
            $scope.typeNames = _.pluck(results['type'], 'type');
            $scope.drawChartMix("#main", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
        });
    };

    /* 
    *   Get the daily wine sold from the logged user 
    */ 
    $scope.getWineSoldYearly = function () {
        $scope.periodStart = new moment().startOf('year').format("YYYY-MM-DD 12:00:00");
        $scope.periodEnd = new moment().endOf('year').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        Data.post('getWineSoldYearly', {
             periodStart: $scope.periodStart,
            periodEnd: $scope.periodEnd,
        }).then(function (results){
            $scope.period = _.keys(_.omit(results['bottles'][0], 'total'));
            $scope.bottles = _.values(_.omit(results['bottles'][0], 'total'));
            $scope.revenue = _.values(_.omit(results['revenue'][0], 'total'));
            $scope.totalBottle = results['bottles'][0].total;
            $scope.totalRevenue = results['revenue'][0].total;
            $scope.wines = results['wines'];
            $scope.typeValue = _.pluck(results['type'], 'sold');
            $scope.typeNames = _.pluck(results['type'], 'type');
            $scope.drawChartMix("#main", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
        });
    };

    $scope.drawChartMix = function (id, type1, type2, labels, data1, data2) {
        var ctx = $(id);
        var myChart = new Chart(ctx, {
            type: type1,
            data: {
                labels: labels,
                datasets: [
                    {
                        type: type1,
                        label: 'Bottiglie vendute',
                        data: data1,
                        backgroundColor: 'rgba(137,84,106,0.5)',
                        borderColor: 'rgba(137,84,106,1)',
                        borderWidth: 1
                    },
                    {
                        type: type2,
                        label: 'Fatturato',
                        backgroundColor: 'transparent',
                        borderColor: '#1ab394',
                        data: data2,
                    }
                ]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
    };

    $scope.drawChartSingle = function (id, type, labels, data) {
        var ctx = $(id);
        var myChart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Tipologia vino',
                        data: data,
                        backgroundColor: [
                            'rgba(44, 32, 40, 0.8)',
                            'rgba(86, 81, 98, 0.8)',
                            'rgba(79, 45, 72, 0.8)',
                            'rgba(198, 188, 196, 0.8)',
                            'rgba(128, 98, 141, 0.8)'
                        ],
                        borderColor: 'white',
                        borderWidth: 1
                    }
                ]
            }
        });
    };

    
    /* 
    *   Get all the wines downloaded from yesterday to today
    *   @params {none}
    */
    $scope.getWineDownloaded = function (when) {
        if(when=='yesterday') {
            Data.get('getWineSoldYesterday').then(function (results) {
                $scope.ywines = results;
            });
        }else{
            Data.get('getWineSoldToday').then(function (results) {
                $scope.twines = results;
            });
        }
    };

    $scope.closeMenu = function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    };

    function SmoothlyMenu() {
        if (!$('body').hasClass('mini-navbar') || $('body').hasClass('body-small')) {
            // Hide menu in order to smoothly turn on when maximize menu
            $('#side-menu').hide();
            $('.logo-pittogramma.white').hide();
            // For smoothly turn on menu
            setTimeout(
                function () {
                    $('#side-menu').fadeIn(400);
                    $('.logo-pittogramma.white').fadeIn(400);
                }, 200);
        } else if ($('body').hasClass('fixed-sidebar')) {
            $('#side-menu').hide();
            $('.profile-element').hide();
            setTimeout(
                function () {
                    $('#side-menu').fadeIn(400);
                    $('.logo-pittogramma.white').fadeIn(400);
                }, 100);
        } else {
            // Remove all inline style from jquery fadeIn function to reset menu state
            $('#side-menu').removeAttr('style');
            $('.logo-pittogramma.white').hide();
        }
    }
});

app.controller('modalCtrl', function($scope, $http, $uibModal, Data, $location) { 
    $scope.value = 1;

    /* 
    *   Open a new modal for downloading a wine
    *   @params {string} the modale size
    *   @params {string} the wine name
    *   @params {string} the SKU
    */
    $scope.open = function (size, name, sku) {
        var $modalScope = this;
        $modalScope.wineName = name;
        $modalScope.wineSKU = sku;
        $scope.modalInstance = $uibModal.open({
            templateUrl: "./partials/modal/wineDownload.html",
            size: size,
            scope: $modalScope
        });
    };

    /* dismissing a modal */
    $scope.dismissModal = function() {
        $scope.modalInstance.close();
    }

    $scope.addWine = function () {
        $scope.value += 1;
    }

    $scope.removeWine = function() {
        if($scope.value - 1 > 0) {
            $scope.value -= 1;
        }
    }

    /* 
    *   Download operation of a specific wine
    *   @params {string} the SKU
    *   @params {value} the number of bottle/s to download
    */
    $scope.addWineSpy = function (sku, value) {
        Data.post('downloadWine', {
            sku: sku,
            value: value
        }).then(function (results) {
            if(results) {
                Data.onlyMsg("Succesfully downloaded");
            }
            $scope.modalInstance.close();
            $location.path('winespy');
        });
    };
});
