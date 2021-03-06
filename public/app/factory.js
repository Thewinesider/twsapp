app.factory("Data", ['$http', 'toaster',
    function ($http, toaster) { // This service connects to our REST API

        var serviceBase = 'server/v1/';

        var obj = {};
        
        obj.toast = function (data) {
            toaster.pop(data.status, "", data.message, 3000, 'trustedHtml');
        }
        
        obj.onlyMsg = function (message) {
            toaster.pop("success", "", message, 3000, 'trustedHtml');
        }
        
        obj.get = function (q, params) {
            return $http.get(serviceBase + q, {params: params}).then(function (results) {
                return results.data;
            });
        };
        
        obj.post = function (q, object) {
            return $http.post(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        
        obj.put = function (q, object) {
            return $http.put(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        
        obj.delete = function (q) {
            return $http.delete(serviceBase + q).then(function (results) {
                return results.data;
            });
        };

        return obj;
}]);
