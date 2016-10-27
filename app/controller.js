app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
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
                $scope.user = results;
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
    }

    $scope.closeMenu = function () {
        $("body").toggleClass("mini-navbar");
        SmoothlyMenu();
    }

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
