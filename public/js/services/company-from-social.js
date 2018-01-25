simplecrmApp.factory('CompanyFromSocial', ['$http', '$q', '$location',
    function CompanyFromSocial ($http, $q, $location) {

        var baseUrl = 'api/v1/company-from-social';

        function create (data) {

            var favorites = (favorites ? '&favorites=true' : '');
            var defer = $q.defer();
            $http({
                method: 'POST',
                url: baseUrl,
                data: data
            }).success(function  (res) {
                defer.resolve(res);
            }).error(function  (err) {
                defer.reject(err);
            });
            
            return defer.promise;
        }

        return {
            create: create
        }
    }]);