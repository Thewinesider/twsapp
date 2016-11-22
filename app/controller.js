app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $filter, $location, $http, Data, moment, DTOptionsBuilder, DTColumnBuilder) {


    $scope.date = {
        startDate: moment().startOf('isoWeek').format("YYYY-MM-DD 12:00:00"),
        endDate: moment().endOf('isoWeek').format("YYYY-MM-DD 00:00:00")
    };

    $scope.opts = {
        "autoApply": true,
        "timePicker": true,
        "timePickerIncrement": 30,
        "showCustomRangeLabel": false,
        "alwaysShowCalendars": true,
        "opens": "left",
        "locale": {
            format: 'DD MMM YY hh:mm'
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
    *   Get this week of sales from the logged user or from a specific user
    */
    $scope.getStatistics = function (uid) {
        $scope.uid = uid;
        $scope.rangeStart = moment($scope.date["startDate"]).format("YYYY-MM-DD hh:mm:ss");
        $scope.rangeEnd = moment($scope.date["endDate"]).format("YYYY-MM-DD hh:mm:ss");
        Data.get('statistics', {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){
            //Setting scope values
            console.log(results);
            if(results['totals'][0].sold == null){
                $scope.totalBottle = 0;   
            }else{
                $scope.totalBottle = results['totals'][0].sold;
            }    
            if(results['totals'][0].sold == null){
                $scope.totalRevenue = 0;
            }else{
                $scope.totalRevenue = results['totals'][0].total_restaurants;
            }
            if(results['totals'][0].sold == null){
                $scope.totalRevenueTws = 0;
            }else{
                $scope.totalRevenueTws = results['totals'][0].total_revenues;
            }     
            $scope.wines = results['wines'];
            $scope.data = results['data'];
            $scope.period = _.pluck(results['data'], 'days');
            $scope.bottles = _.pluck(results['data'], 'total_sales');
            $scope.revenueTws = _.pluck(results['data'], 'total_revenues');
            $scope.revenue = _.pluck(results['data'], 'total_restaurants');
            $scope.typeValue = _.pluck(results['wineType'], 'sold');
            $scope.typeNames = _.pluck(results['wineType'], 'type');
            console.log(JSON.stringify($scope.wines));
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
    */
    $scope.getWineSold = function (uid) {
        $scope.uid = uid;
        $scope.rangeStart = moment($scope.date["startDate"]).format("YYYY-MM-DD hh:mm:ss");
        $scope.rangeEnd = moment($scope.date["endDate"]).format("YYYY-MM-DD hh:mm:ss");
        Data.get('wineSold', {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){    
            $scope.wines = results['wines'];
        });
        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            var defer = $q.defer();
            defer.resolve($scope.wines);
            return defer.promise;
        })
            .withPaginationType('full_numbers')
        // Active Buttons extension
            .withButtons([
            'colvis',
            'copy',
            'print',
            'excel',
        ]);
        $scope.dtColumns = [
            DTColumnBuilder.newColumn('sku').withTitle('SKU'),
            DTColumnBuilder.newColumn('name').withTitle('Nome vino'),
            DTColumnBuilder.newColumn('sold').withTitle('Venduto')
        ];
    };


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
                },
                tension: 0
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
