simplecrmApp.controller('FacebookAuthController', ['$scope', '$auth', 'AuthServices', '$location', '$modal',
function GoogleAuthController($scope, $auth, AuthServices, $location, $modal) {
    $scope.login = function login () {
        $auth.authenticate('facebook')
            .then(function (res) {
                var data = {token: res.access_token};
                AuthServices.facebookAuth(data)
                    .then(function (res) {
                        localStorage.setItem('satellizer_token', res.token);
                        if(res.user.accounts.length > 0) {
                            localStorage.accountid = res.user.accounts[0].id;
                            AuthServices.setCredentials(res.user, null);
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
                                localStorage.accountid = res.user.accounts[0].id;
                                AuthServices.setCredentials(res.user, null);
                                if(localStorage.atemptUrl){
                                    $location.path(localStorage.atemptUrl);
                                }else{
                                    $location.path('/leads');
                                }
                            });
                        }

                    }, function (err) {
                        console.log(err);
                    })
            })
            .catch(function (err) {
                console.log(err);
            });
    }

}]);
