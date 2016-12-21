app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $filter, $location, $http, $window, Data, moment, DTOptionsBuilder, DTColumnBuilder) {
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
            console.log(JSON.stringify(results));
            $location.path('winespy');
        });
    };

    $scope.getUsers = function(role) {
        Data.post('getUserList', {
            role: role
        }).then(function (results) {
            $scope.users = results
        });
    }


    $scope.getUser = function(role) {
        Data.post('user', {
            role: role
        }).then(function (results) {
            $scope.users = results
        });
    }


    /* 
    *   Get the wine list of the logged user
    */
    $scope.getCatalog = function () {
        Data.get('catalog').then(function (results) {
            console.log(JSON.stringify(results));
            $scope.regions = results["regions"]
            $scope.producers = results["producers"]
            $scope.wines = results["wines"];
        });
    };

    /* 
    *   Get the wine list of the logged user
    */
    $scope.getWineList = function () {
        Data.get('wineList').then(function (results){
            $scope.wines = results;
            if($rootScope.payment_is_set == 1) {
                $scope.payment_type = "Carta di Credito";
            } else {
                $scope.payment_type = "Bonifico Bancario";  
            }            
        });
    };

    /* 
    *   Add a new user to the DB
    */
    $scope.signUp = function (customer) {
        Data.post('signUp', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            $location.path('winespy');
        });
    };

    /* 
    *   Add a new user with Credit Card 
    */
    $scope.addCustomer = function (customer) {
        customer.associated_to = $rootScope.sessionUid;
        Data.post('customer', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if(results.status == "success") {
                $location.path('addpayment');
                $location.hash('page-wrapper');
            }
        });
    };

    /*
    *   Create a new Wallet on Lemonway
    */
    $scope.addLemonwayWallet = function(lemonway) {
        if(lemonway.payment_type == 1) {
            $api = "registerCC";
        } else {
            $api = "registerSDD";
        }
        console.log("API: " + $api);
        Data.post($api, {
            lemonway: lemonway
        }).then(function (results){
            console.log(results);
            if (results["status"] == "success" && lemonway.payment_type == 1){
                $window.location.href = results["url"];
            } else if (results["status"] == "success" && lemonway.payment_type == 2) {
                Data.toast(results);
                $location.path("winespy");
            } else {
                Data.toast(results);
            }
        });
    }

    /* 
    *   Get this week of sales from the logged user or from a specific user
    */
    $scope.getStatistics = function (uid) {
        $scope.uid = uid;
        console.log("UID: " + this.uid);
        $scope.rangeStart = moment($scope.date["startDate"]).format("YYYY-MM-DD hh:mm:ss");
        $scope.rangeEnd = moment($scope.date["endDate"]).format("YYYY-MM-DD hh:mm:ss");
        Data.get('statistics', {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){
            //Setting scope values
            console.log(JSON.stringify(results));
            if(results['totals'][0].sold == null){
                $scope.totalBottle = 0;   
            }else{
                $scope.totalBottle = results['totals'][0].sold;
            }    
            if(results['totals'][0].total_restaurants == null){
                $scope.totalRevenue = 0;
            }else{
                $scope.totalRevenue = results['totals'][0].total_restaurants;
            }
            if(results['totals'][0].total_revenues == null){
                $scope.totalRevenueTws = 0;
            }else{
                $scope.totalRevenueTws = results['totals'][0].total_revenues;
            }     
            $scope.bestWine = _.max(results['wines'], function(wine){ return wine.sold; }).name;
            if($scope.bestWine == null) $scope.bestWine = "Nessun dato";
            $scope.bestWineType = _.max(results['wineType'], function(wine){ return wine.sold; }).type;
            if($scope.bestWineType == null) $scope.bestWineType = "Nessun dato";
            $scope.averageSold = $scope.totalRevenue / $scope.totalBottle ;
            if(isNaN($scope.averageSold)) $scope.averageSold = 0;
            $scope.averageMark = $scope.totalRevenue / $scope.totalRevenueTws;
            if(isNaN($scope.averageMark)) $scope.averageMark = 0;
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
            if($rootScope.role == "admin" && angular.element('#wineGraph').length) {
                $scope.drawChart("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenueTws);
            }else if(angular.element('#wineGraph').length){
                $scope.drawChart("#wineGraph", "bar", "line", $scope.period, $scope.bottles, $scope.revenue);
            }
            if(angular.element('#wineGraph').length){
                $scope.drawChartSingle("#wineType", "pie", $scope.typeNames, $scope.typeValue);
            }
        });
        //define buttons
        if($rootScope.role == 'admin'){
            var btn =[
                'colvis',
                'copy',
                'print',
                'excel'
            ];
        }else{
            var btn =[
                'copy',
                'print',
                'excel'
            ];
        }
        //initialize the datatable
        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            var defer = $q.defer();
            defer.resolve($scope.wines);
            return defer.promise;
        })
            .withPaginationType('full_numbers')
        // Active Buttons extension
            .withButtons(btn);
        $scope.dtColumns = [
            DTColumnBuilder.newColumn('sku').withTitle('SKU'),
            DTColumnBuilder.newColumn('name').withTitle('Nome vino'),
            DTColumnBuilder.newColumn('type').withTitle('Tipo'),
            DTColumnBuilder.newColumn('sold').withTitle('Venduto'),
            DTColumnBuilder.newColumn('revenue').withTitle('Fatturato'),
            DTColumnBuilder.newColumn('revenueTWS').withTitle('Fatturato TWS')
        ];
    };

    /* 
    *   Get this week of sales from the logged user or from a specific user
    */
    $scope.getWineSold = function (uid, range) {
        //setting query data
        $scope.uid = uid;
        if(range == 'yesterday'){
            $scope.rangeStart = moment().subtract(1, 'day').format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().format("YYYY-MM-DD 12:00:00");
        }else if(range == 'today'){
            $scope.rangeStart = moment().format("YYYY-MM-DD 12:00:00");
            $scope.rangeEnd = moment().add(1, 'day').format("YYYY-MM-DD 12:00:00");
        }else{
            $scope.rangeStart = moment($scope.date["startDate"]).format("YYYY-MM-DD hh:mm:ss");
            $scope.rangeEnd = moment($scope.date["endDate"]).format("YYYY-MM-DD hh:mm:ss");
        }
        //getting data
        Data.get('wineSold', {
            periodStart: $scope.rangeStart,
            periodEnd: $scope.rangeEnd,
            uid: $scope.uid,
        }).then(function (results){   
            console.log(JSON.stringify(results));
            if(range == 'yesterday'){
                $scope.winesRange = results['wines'];
            }else{
                $scope.wines = results['wines'];
            } 
        });
        //define buttons
        if($rootScope.role == 'admin'){
            var btn =[
                'colvis',
                'copy',
                'print',
                'excel'
            ];
        }else{
            var btn =[
                'copy',
                'print',
                'excel'
            ];
        }
        //initialize the datatable
        $scope.dtOptions = DTOptionsBuilder.fromFnPromise(function() {
            var defer = $q.defer();
            defer.resolve($scope.wines);
            return defer.promise;
        })
            .withPaginationType('full_numbers')
        // Active Buttons extension
            .withButtons(btn);
        $scope.dtColumns = [
            DTColumnBuilder.newColumn('sku').withTitle('SKU'),
            DTColumnBuilder.newColumn('name').withTitle('Nome vino'),
            DTColumnBuilder.newColumn('type').withTitle('Tipo'),
            DTColumnBuilder.newColumn('sold').withTitle('Venduto'),
            DTColumnBuilder.newColumn('revenue').withTitle('Fatturato'),
            DTColumnBuilder.newColumn('revenueTWS').withTitle('Fatturato TWS')
        ];
    };


    $scope.drawChart = function (id, type1, type2, labels, data1, data2) {
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
