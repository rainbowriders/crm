simplecrmApp.config(['$authProvider', function  ($authProvider) {
	$authProvider.loginUrl = 'auth/login';
	$authProvider.signupUrl = 'auth/signup';
	$authProvider.loginRedirect = '/deals';
	$authProvider.logoutRedirect = '/';
	$authProvider.loginOnSignup = false;

	$authProvider.google({
		clientId: '486529571126-3ur8c5566rvjl6gv469qunr0acfmsh07.apps.googleusercontent.com'
	});

	$authProvider.facebook({
		clientId: '1914346988851897',
		responseType: 'token'
	});

	$authProvider.twitter({
		responseType: 'token',
		clientId: 'FQpVAdavfVxsRkx2dNwpnT4EZ'
	});

	$authProvider.linkedin({
		clientId: '86rzwrin11k8tx',
		scope: ['r_emailaddress', 'r_fullprofile']
	});
}]);