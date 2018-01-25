<!DOCTYPE html>
<html lang="en" ng-app="simplecrmApp" class="app bg-white">
    <head>
        <!--<base href="https://crm.rainbowriders.dk/">-->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        {!! HTML::style('//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css') !!}
        {!! HTML::style('packages/bower_components/bootstrap/dist/css/bootstrap.min.css') !!}
        {!! HTML::style('musik/css/app.v1.css') !!}
        {!! HTML::style('musik/js/chosen/chosen.css') !!}
        {!! HTML::style('packages/bower_components/angular-tablesort/tablesort.css') !!}
        {!! HTML::style('css/main.css') !!}        
        {!! HTML::style('packages/bower_components/angular-smilies/dist/angular-smilies.min.css') !!}
        {!! HTML::style('packages/bower_components/angular-smilies/dist/angular-smilies-embed.min.css') !!}
        {!! HTML::style('packages/bower_components/angular/angular-csp.css') !!}
        {!! HTML::style('css/animate.css') !!}
        {!! HTML::style('css/forms.css') !!}
        
        <script src="packages/bower_components/angular/angular.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.0-beta.0/angular-route.min.js"></script>
        <script src="packages/bower_components/angular-extend-promises/angular-extend-promises.js"></script>
        <script src="https://code.angularjs.org/1.2.0/angular-animate.min.js" ></script>
        <script src="packages/bower_components/satellizer/satellizer.js"></script>
        <script src="packages/bower_components/angular-sanitize/angular-sanitize.min.js"></script>
        <script src="packages/bower_components/tg-angular-validator/dist/angular-validator.min.js"></script>
        <script src="packages/bower_components/angular-ui-router/release/angular-ui-router.min.js"></script>
        <script src="packages/bower_components/angular-tablesort/js/angular-tablesort.js"></script>
        <script src="packages/bower_components/ngInfiniteScroll/build/ng-infinite-scroll.js"></script>
        <script src="packages/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js"></script>
        <script src="packages/bower_components/angular-smilies/dist/angular-smilies.min.js"></script>

        <title>Rainbow CRM</title>
        <script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-71153431-1', 'auto');
		ga('send', 'pageview');

	</script>
	<script type="text/javascript">
		var _urq = _urq || [];
		_urq.push(['initSite', 'abfa16cd-54ab-44e8-8b2b-d3942dee3ec2']);
		(function() {
			var ur = document.createElement('script'); ur.type = 'text/javascript'; ur.async = true;
			ur.src = ('https:' == document.location.protocol ? 'https://cdn.userreport.com/userreport.js' : 'http://cdn.userreport.com/userreport.js');
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ur, s);
		})();
	</script>
    </head>
    <body   class="{{ !isUserLoged ? 'body-with-scroll' : '' }}">
        <section class="{{isUserLoged ? 'vbox' : ''}}" ng-cloak>
            <header class="bg-white-only header header-md navbar navbar-fixed-top-xs" ng-controller="UserController" ng-show="isUserLoged">
                <div class="aside navbar-header text-center hidden-xs" id="logo"><a href="/" style="color:#777;"><img ng-src="/images/rr-circle-logo.png" alt="..." style="height:50px;margin-right:10px;" ng-click="resetDealFilters()"> Rainbow CRM</a></div>
                <section ng-if="isUserLoged">
                    <a href="/leads" class="h4 pull-left m" ng-cloak>{{translation.LEADS}}</a>
                    <a href="/contacts" class="h4 pull-left m" ng-cloak>{{translation.CONTACTS}}</a>
                    <a href="/organisations" class="h4 pull-left m" ng-cloak>{{translation.ORGANISATIONS}}</a>

                    <div class="navbar-right ">
                        <div class="nav navbar-nav m-n hidden-xs nav-user user">
                            <div class="nav navbar-nav m-n hidden-xs nav-user user" style="height:60px;" ng-show="accounts.length > 1">
                                <button data-toggle="dropdown" class="btn btn-sm btn-default dropdown-toggle"  style="height:60px;border:0;box-shadow:none;margin-right:10px;">
                                <span class="dropdown-label" ng-cloak>{{selectedAccount.accountName}}</span>&nbsp;&nbsp;<span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-select">
                                    <li ng-repeat="account in accounts" ng-click="switchAccount(account)" class="{{account.id == selectedAccount.accountID ? 'active' : ''}}"
                                        style="color:darkcyan;">
                                        <input type="radio" name="d-s-r" ng-checked="account.id == selectedAccount"><a href="#" ng-cloak>{{::account.owner}}</a>
                                    </li>
                                </ul>
                            </div>
                            <ul class="nav navbar-nav m-n hidden-xs nav-user user b-l">
                                <li class="dropdown"> <a href="#" class="dropdown-toggle bg clear" data-toggle="dropdown"> <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm"> <img ng-src="/images/person.png" alt="..."> </span><b class="caret"></b> </a>
                                <ul class="dropdown-menu animated fadeInRight">
                                    <!--<li> <a href="#">Profile</a> </li>-->
                                    <li> <span class="arrow top"></span> <a href="/settings" ng-cloak>{{translation.SETTINGS}}</a> </li>
                                    <!--<li> <a href="#"> <span class="badge bg-danger pull-right">3</span> Notifications </a> </li>-->
                                    <li class="divider"></li>
                                    <li> <span class="arrow top"></span> <a href="mailto:info@rainbowriders.dk?subject=Rainbow CRM support">Support</a> </li>
                                    <li class="divider"></li>
                                    <li> <a href="#" ng-click="logOut()" ng-cloak >{{translation.LOGOUT}}</a> </li>
                                </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="/api/v1/download/guide" target="_blank" class="h4 pull-right m" ng-cloak>Download Guide</a>
                </section>
            </header>
            <alert ng-if="$root.alert.msg" type="{{$root.alert.type}}" close="closeAlert($index)"
                   style="position:absolute;top:40px;width:50%;left: 25%;z-index: 100001" ng-cloak>{{$root.alert.msg}}</alert>
            <section ui-view style="width:100%;">
        </section>
    </section>
        {!! HTML::script('js/app.js') !!}
        <!--App config scripts-->
        {!! HTML::script('js/config/routes.js') !!}
        {!! HTML::script('js/config/auth.js') !!}
        <!--App controllers scripts-->
        {!! HTML::script('js/controllers/auth.js') !!}
        {!! HTML::script('js/controllers/user.js') !!}
        {!! HTML::script('js/controllers/user-settings.js') !!}
        {!! HTML::script('js/controllers/deals.js') !!}
        {!! HTML::script('js/controllers/organisations.js') !!}
        {!! HTML::script('js/controllers/contacts.js') !!}
        {!! HTML::script('js/controllers/invation.js') !!}
        {!! HTML::script('js/controllers/confirm.js') !!}
        {!! HTML::script('js/controllers/send-reset-password-link.js') !!}
        {!! HTML::script('js/controllers/reset-password.js') !!}
        {!! HTML::script('js/controllers/google-auth.js') !!}
        {!! HTML::script('js/controllers/facebook-auth.js') !!}
        {!! HTML::script('js/controllers/linked-auth.js') !!}

                <!--App services scripts-->
        {!! HTML::script('js/services/translations.js')!!}
        {!! HTML::script('js/services/auth.js') !!}
        {!! HTML::script('js/services/user.js') !!} 
        {!! HTML::script('js/services/user-settings.js') !!}
        {!! HTML::script('js/services/currency.js') !!}
        {!! HTML::script('js/services/language.js') !!}
        {!! HTML::script('js/services/user-accounts.js') !!}
        {!! HTML::script('js/services/deals.js') !!}
        {!! HTML::script('js/services/stages.js') !!}
        {!! HTML::script('js/services/organisations.js') !!}
        {!! HTML::script('js/services/contacts.js') !!}
        {!! HTML::script('js/services/comments.js') !!}
        {!! HTML::script('js/services/company-from-social.js') !!}

        <!--App filters-->
        {!! HTML::script('js/filters/date-time.js') !!}
        {!! HTML::script('js/filters/translation.js') !!}
        {!! HTML::script('js/filters/parse-url.js') !!}
        {!! HTML::script('js/filters/nl2br.js') !!}
       <!-- Bootstrap -->

        <!-- App -->
        {!! HTML::script('musik/js/app.v1.js') !!}
        {!! HTML::script('musik/js/app.plugin.js') !!}
        {!! HTML::script('musik/js/chosen/chosen.jquery.min.js') !!}
</body>
</html>