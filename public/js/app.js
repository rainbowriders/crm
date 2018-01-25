var simplecrmApp = angular.module('simplecrmApp',
    [
    'ngRoute',
    'ui.router',
    'angularValidator',
    'satellizer',
    'ui.bootstrap',
    'tableSort',
    'ngSanitize',
    'infinite-scroll',
    'angular-smilies'
    ]);

simplecrmApp.run(function  ($rootScope, $q, $state, $rootScope, $location, $window, $timeout, $auth, TranslationsService, AuthServices) {
    TranslationsService.getTranslations()
        .then(function  (data) {
            $rootScope.translation = data;   
        });
    $rootScope.messagesClose = function messagesClose () {
        return $timeout(function () {
            $rootScope.alert = {};
        },4000);
    };
    $rootScope.$on('$locationChangeStart', function () {
        if($location.path() === '/invitesignup'){
            AuthServices.clearCredentials();
        }
    });

    $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams, fromState) {
        $rootScope.isUserLoged = AuthServices.getIsUserLoged();
    	if($rootScope.isUserLoged && $location.path() === '/login'){
            $location.path('/leads');
        }
        if($rootScope.isUserLoged && $location.path() === '/signup'){
            $location.path('/leads');
        }
        if($rootScope.isUserLoged && $location.path() === '/'){
            $location.path('/leads');
        }
        if($location.path() !== '/signup' && $location.path() !== '/login' && $location.path() !== '/' && $location.path() !== '/invitesignup'
                && $location.path() !== '/confirm' && $location.path() !== '/send-reset-password-link' && $location.path() !== '/reset-password'){
            if(!$rootScope.isUserLoged){
                localStorage.setItem('atemptUrl', $location.path());
                $location.path('/');
            }
        } 
        // if($location.path() === '/'){
        //     if($rootScope.isUserLoged && localStorage.rememberMe == 1){
        //         $location.path('/leads');
        //     }
        // }
        if(!$rootScope.isUserLoged && localStorage.getItem('remember_token')){
            var data = {remember_token: localStorage.getItem('remember_token')};
            $auth.login(data)
            .then(function  (res) {
                localStorage.accountid = res.data.user.accounts[0].id;
                AuthServices.setCredentials(res.data.user, data.rememberMe);
                    if(localStorage.atemptUrl){
                        $location.path(localStorage.atemptUrl);
                    }else{
                        $location.path('/leads');
                    }
                    $window.location.reload();
            },function  (err) {
                $rootScope.alert = {type: 'danger', msg: $rootScope.translation.LOGIN_ERROR};
                $rootScope.messagesClose();
            });
        }
	});    
});



