app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data, moment) {
    $scope.datePicker = {};
    $scope.datePicker.date = {
        startDate: null,
        endDate: null
    };
    $scope.opts = {
        ranges: {
            'Questa settimana': [moment().startOf('isoWeek').format("YYYY-MM-DD 12:00:00"), moment().endOf('isoWeek').add(1, 'day').format("YYYY-MM-DD 12:00:00")],
            'Questo mese': [moment().startOf('month').format("YYYY-MM-DD 12:00:00"), moment().endOf('month').add(1, 'day').format("YYYY-MM-DD 12:00:00")],
            'Quest anno': [moment().startOf('year').format("YYYY-MM-DD 12:00:00"), moment().endOf('year').add(1, 'day').format("YYYY-MM-DD 12:00:00")],
        },
        timePicker: true,
        timePickerIncrement: 30,
        showCustomRangeLabel: false,
        alwaysShowCalendars: false,
        autoApply: true,
        locale: {
            format: 'YYYY-MM-DD h:mm:ss'
        }
    };

    /* 
    *   Do the login inside the app.
    *   @params {array}: customer data
    *   @return success 
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
    $scope.getInfo = function() {
        Data.get('getProducerList').then(function (results) {
            $scope.producers = results['producers'];
            $scope.regions = results['regions'];
            $scope.wines = results['wines'];
        });
    };

    $scope.getUsers = function(role) {
        Data.post('getUserList', {
            role: role
        }).then(function (results) {
            $scope.users = results
        });
    }

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
    *   Get this week of sales from the logged user or from a specific user
    */

    $scope.getWineSold = function (uid, range) {
        $scope.uid = uid;
        $scope.rangeStart = "2016-01-01 12:00:00";
        $scope.rangeEnd = "2016-11-12 12:00:00";
        Data.get('getWineSoldList', {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){
            //Setting scope values
            console.log(JSON.stringify(results));
           /* $scope.period = _.keys(_.omit(results['bottles'][0], 'total'));
            $scope.bottles = _.values(_.omit(results['bottles'][0], 'total'));
            $scope.revenue = _.values(_.omit(results['revenue'][0], 'total'));
            $scope.revenueTws = _.values(_.omit(results['revenueTws'][0], 'total'));*/
            $scope.totalBottle = results['totals'][0].sold;
            $scope.totalRevenue = results['totals'][0].total_restaurants;
            $scope.totalRevenueTws = results['totals'][0].total_revenues;
            $scope.wines = results['wines'];
            $scope.data = results['data'];
            $scope.period = _.pluck(results['data'], 'days');
            $scope.bottles = _.pluck(results['data'], 'total_sales');
            $scope.revenueTws = _.pluck(results['data'], 'total_revenues');
            $scope.revenue = _.pluck(results['data'], 'total_restaurants');
            $scope.typeValue = _.pluck(results['wineType'], 'sold');
            $scope.typeNames = _.pluck(results['wineType'], 'type');
            //drawing charts
            if($scope.myChart){
                $scope.myChart.destroy();
            };
            if($rootScope.role == "admin") {
                $scope.drawChartMix("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenueTws);
            }else{
                $scope.drawChartMix("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            }
            $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
        });
    };

    /* 
    *   Get this week of sales from the logged user or from a specific user
    *
    $scope.getWineSold = function (uid, range) {
        $scope.uid = uid;
        var rangeStart = $scope.datePicker.date["startDate"];
        var rangeEnd = $scope.datePicker.date["endDate"];
        var range = rangeEnd.diff(rangeStart, 'day');
        if(range == 7){
            $apiToCall = 'getWineSoldWeekly';
            $scope.rangeStart = moment().startOf('isoWeek').format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().endOf('isoWeek').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        }else if(range == -7) { 
            $apiToCall = 'getWineSoldWeekly';
            $scope.rangeStart = moment().startOf('isoWeek').subtract(7, 'day').format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().endOf('isoWeek').subtract(6, 'day').format("YYYY-MM-DD 12:00:00");
        }else if(range == 30) {
            $apiToCall = 'getWineSoldMonthly';
            $scope.rangeStart = moment().startOf('month').format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().endOf('month').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        }else if(range == 365) {
            $apiToCall = 'getWineSoldYearly';
            $scope.rangeStart = moment().startOf('year').format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().endOf('year').add(1, 'day').format("YYYY-MM-DD 12:00:00");
        }
        Data.post($apiToCall, {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){
            //alert(results);
            //Setting scope values
            $scope.period = _.keys(_.omit(results['bottles'][0], 'total'));
            $scope.bottles = _.values(_.omit(results['bottles'][0], 'total'));
            $scope.revenue = _.values(_.omit(results['revenue'][0], 'total'));
            $scope.revenueTws = _.values(_.omit(results['revenueTws'][0], 'total'));
            $scope.totalBottle = results['bottles'][0].total;
            $scope.totalRevenue = results['revenue'][0].total;
            $scope.totalRevenueTws = results['revenueTws'][0].total;
            $scope.wines = results['wines'];
            $scope.typeValue = _.pluck(results['type'], 'sold');
            $scope.typeNames = _.pluck(results['type'], 'type');
            //drawing charts
            if($scope.myChart){
                $scope.myChart.destroy();
            };
            if($rootScope.role == "admin") {
                $scope.drawChartMix("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenueTws);
            }else{
                $scope.drawChartMix("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            }
            $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
        });
    };*/ 


    $scope.drawChartMix = function (id, type1, type2, labels, data1, data2) {
        var ctx = $(id);
        $scope.myChart = new Chart(ctx, {
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
        $scope.myChart = new Chart(ctx, {
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
