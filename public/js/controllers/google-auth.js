simplecrmApp.controller('GoogleAuthController', ['$scope', '$auth', 'AuthServices', '$location', '$modal',
function GoogleAuthController($scope, $auth, AuthServices, $location, $modal) {



    $scope.login = function login () {
        $auth.authenticate('google')
            .then(function (res) {
                if(res.data.user.accounts.length > 0) {
                    localStorage.accountid = res.data.user.accounts[0].id;
                    AuthServices.setCredentials(res.data.user, null);
                    if(localStorage.atemptUrl){
                        $location.path(localStorage.atemptUrl);
                    }else{
                        $location.path('/leads');
                    }
                } else {
                    var modalInstance = $modal.open({
                        templateUrl: '/angular/templates/modals/addAccountFromSocial.html',
                        controller: 'AccountFromSocial'
                    });
                    modalInstance.result.then(function(res){
                        localStorage.accountid = res.data.user.accounts[0].id;
                        AuthServices.setCredentials(res.user, null);
                        if(localStorage.atemptUrl){
                            $location.path(localStorage.atemptUrl);
                        }else{
                            $location.path('/leads');
                        }
                    });
                }
            })
            .catch(function (err) {
                console.log(err);
            });
    }

}]);

simplecrmApp.controller('AccountFromSocial', ['$scope', '$modalInstance', 'CompanyFromSocial', function AccountFromSocial($scope, $modalInstance, CompanyFromSocial) {

    $scope.company_name = '';
    $scope.company_name_error = false;
    $scope.ok = function ok() {
        if($scope.company_name == '' || $scope.company_name == undefined) {
            $scope.company_name_error = true;
            return false;
        }
        var data = {name: $scope.company_name};
        CompanyFromSocial.create(data)
            .then(function (res) {
                $modalInstance.close(res);
            }, function (err) {

            })
        
        
    };
}]);