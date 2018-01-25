simplecrmApp.controller('LinkedAuthController', ['$scope', '$auth', 'AuthServices', '$location', '$modal',
    function LinkedAuthController($scope, $auth, AuthServices, $location, $modal) {
        $scope.login = function login () {
            $auth.authenticate('linkedin')
                .then(function (res) {
                    localStorage.setItem('satellizer_token', res.data.token);
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
                        modalInstance.result.then(function (res) {
                            localStorage.accountid = res.data.user.accounts[0].id;
                            AuthServices.setCredentials(res.user, null);
                            if (localStorage.atemptUrl) {
                                $location.path(localStorage.atemptUrl);
                            } else {
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
