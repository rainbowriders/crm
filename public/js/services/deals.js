simplecrmApp.factory('DealsServices', ['$http', '$q', '$rootScope', '$location',
function DealsServices ($http, $q, $rootScope, $location) {

	var baseUrl = 'api/v1/deals';
	var userId = localStorage.id;

	function addDeal (dealData) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: baseUrl + '?account=' + localStorage.accountid,
			data:dealData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function loadDeals (statuses, owner, startIndexForLoadDeals, pattern) {
		var defer = $q.defer();
		var pattern = pattern || '';

		$rootScope.statuses = statuses;
		$rootScope.owner = owner;
		$rootScope.startIndexForLoadDeals = 0;
		$rootScope.pattern = pattern;
		$http({
			method: 'GET',
			url: generateUrl(statuses, owner, startIndexForLoadDeals, pattern)
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function showDeal (id) {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl + '/' + id
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	};

	function updateDeal (dealData) {
		var defer = $q.defer();

		$http({
			method: 'PUT',
			url: baseUrl + '/' + dealData.id + '?account=' + localStorage.accountid,
			data: dealData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	};

	function deleteDeal (dealId) {
		var defer = $q.defer();

		$http({
			method: 'DELETE',
			url: baseUrl + '/' + dealId
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function generateUrl (statuses, owner, startIndexForLoadDeals, pattern) {

		var favorites = (statuses.favorites ? '&favorites=true' : '');
		var lost = (statuses.lost ? '&statuses[]=lost': '');
		var won = (statuses.won ? '&statuses[]=won': '');
		var open = (statuses.open ? '&statuses[]=open': '');
		var cancelled = (statuses.cancelled ? '&statuses[]=cancelled': '');
		var isOwner ='';
		var start = '&start=' + startIndexForLoadDeals;
		var pattern = pattern;
		if(pattern !== ''){pattern = '&pattern=' + pattern};


		if(owner.me === true && owner.other === true){isOwner = ''};
		if(owner.me === true && owner.other === false){isOwner = '&userid=' + userId};
		if(owner.me === false && owner.other === true){isOwner = ''};
		if(owner.me === false && owner.other === false){return};


		var fakeUrlComponets = {
			lost: statuses.lost,
			won: statuses.won,
			open:statuses.open,
			cancelled: statuses.cancelled
		};
		var fakeUrl = '';
		for(var i in fakeUrlComponets){
			if(fakeUrl === ''){
				fakeUrl += i + '=' + fakeUrlComponets[i];
			}
			else{
				fakeUrl += '&' + i + '=' + fakeUrlComponets[i];
			}
		};
		$location.search(fakeUrl + pattern);
		var url = baseUrl + '?account=' + localStorage.accountid  + lost + won + open + cancelled + isOwner + start + pattern + favorites;
		return url;
	};

	function changeFavoriteStatus (deal) {
		var method = (deal.isFavorite ? 'DELETE' : 'POST');
		var statusUrl = 'api/v1/deal-favorite/' + deal.id;
		var defer = $q.defer();
		$http({
			method: method,
			url: statusUrl
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function getDealsForContact (contactId) {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl + '?contactId=' + contactId 
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	function getDealsForOrganisation (organisationId) {
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl + '?organisationId=' + organisationId 
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	}

	return {
		addDeal: addDeal,
		loadDeals: loadDeals,
		showDeal: showDeal,
		updateDeal: updateDeal,
		deleteDeal: deleteDeal,
		changeFavoriteStatus: changeFavoriteStatus,
		getDealsForContact: getDealsForContact,
		getDealsForOrganisation: getDealsForOrganisation
	};
}]);