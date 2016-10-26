app.controller('twsCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Data) {
    $scope.login = {};
    $scope.signup = {};

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

    /* PERIODO FINE */

    $scope.getFullWineList = function() {
        Data.get('getProducerList').then(function (results) {
            $scope.producers = results;
        });
        Data.get('getRegionList').then(function (results) {
            $scope.regions = results;
        });
        Data.get('getFullWineList').then(function (results) {
            $scope.wines = results;
        });
    };

    $scope.signup = {email:'',password:'',name:'',phone:'',address:''};

    $scope.signUp = function (customer) {
        Data.post('signUp', {
            customer: customer
        }).then(function (results) {
            Data.toast(results);
            if (results.status == "success") {
                $location.path('dashboard');
            }
        });
    };

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
    $scope.addWine = function () {
        $scope.value += 1;
    }
    $scope.removeWine = function() {
        if($scope.value - 1 > 0) {
            $scope.value -= 1;
        }
    }
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

    $scope.dismissModal = function() {
        $scope.modalInstance.close();
    }
});
