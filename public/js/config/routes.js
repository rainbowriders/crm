simplecrmApp.config(['$stateProvider', '$locationProvider', '$httpProvider', '$compileProvider', '$routeProvider', '$urlRouterProvider',
function  ($stateProvider, $locationProvider, $httpProvider, $compileProvider, $routeProvider, $urlRouterProvider) {
  $compileProvider.debugInfoEnabled(true);
  $locationProvider.html5Mode({
        enabled: true,
        requireBase: false
    });
    $urlRouterProvider.otherwise('/');

    $stateProvider
    	.state('home', {
    		url: '/',
    		templateUrl: 'angular/templates/auth/login.html',
        controller: 'AuthController'
    	})
    	.state('login', {
    		url: '/login',
    		templateUrl: 'angular/templates/auth/login.html'
    	})
    	.state('signup', {
    		url: '/signup',
            templateUrl: 'angular/templates/auth/signup.html',
            controller: 'AuthController'
    	})
    	.state('deals', {
    		url: "/leads",
    		templateUrl: 'angular/templates/deals/index.html',
            controller: 'DealsController'
    	})
      .state('dealsdetails', {
          url: '/lead:' + ':dealID',
          templateUrl: 'angular/templates/deals/show.html',
          controller: 'ShowDealController'
      })
      .state('contacts', {
            url: '/contacts',
            templateUrl: 'angular/templates/contacts/index.html'
      })
      .state('organisations', {
        url: '/organisations',
        templateUrl: 'angular/templates/organisations/index.html'
      })
      .state('settings', {
        url: '/settings',
        templateUrl: 'angular/templates/settings/index.html'
      })
      .state('invitesignup', {
            url: '/invitesignup',
            templateUrl: 'angular/templates/auth/invitesignup.html',
            controller: 'InvationController'
      })
      .state('confirm', {
        url: "/confirm",
        templateUrl: 'angular/templates/auth/confirm.html',
        controller: 'ConfirmController'
      })
      .state('sendresetpasswordlink', {
        url: '/send-reset-password-link',
        templateUrl: 'angular/templates/auth/send-forgot-password-link.html',
        controller: 'sendResetPasswordLinkController'
      })
      .state('resetpassword', {
        url: '/reset-password',
        templateUrl: 'angular/templates/auth/reset-password.html',
        controller: 'ResetPasswordController'
      });

	$httpProvider.interceptors.push(['$q', '$location',  function ($q, $location) {
	return {
   		'request': function (config) {
       		config.headers = config.headers || {};
			if (localStorage.satellizer_token) {
           		config.headers.Authorization = 'Bearer ' + localStorage.satellizer_token;
       		}
       		return config;
   		}
	};
  
}]);

}]);