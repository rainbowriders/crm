simplecrmApp.factory('OrganisationsServices', ['$http', '$q', '$location',
function OrganisationsServices ($http, $q, $location) {
	var baseUrl = 'api/v1/organisations';

	function searchForOrgansiations (pattern, favorites) {
		var favorites = (favorites ? '&favorites=true' : '');
		var defer = $q.defer();
		$http({
			method: 'GET',
			url: baseUrl + '?account=' + localStorage.accountid + '&pattern=' + pattern +favorites
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		$location.search('&pattern=' + pattern);
		return defer.promise;
	};

	function loadOrganisations (start, favorites) {
		var favorites = (favorites ? '&favorites=true' : '');
		var defer = $q.defer();
		var start = '&start=' + start + favorites;
		$http({
			method: 'GET',
			url: baseUrl + '?account=' + localStorage.accountid + start
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function addOrganisation (organisationData) {
		var defer = $q.defer();
		$http({
			method: 'POST',
			url: baseUrl + '?account=' + localStorage.accountid,
			data: organisationData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function updateOrganisation (organisationData) {
		var defer = $q.defer();
		$http({
			method: 'PUT',
			url: baseUrl  + '/' + organisationData.id + '?account=' + localStorage.accountid,
			data: organisationData
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});
		return defer.promise;
	};

	function deleteOrganisation (id) {
		var defer = $q.defer();

		$http({
			method: 'DELETE',
			url: baseUrl + '/' + id
		}).success(function  (res) {
			defer.resolve(res);
		}).error(function  (err) {
			defer.reject(err);
		});

		return defer.promise;
	};

	function changeFavoriteStatus (organisation) {
		var method = (organisation.isFavorite ? 'DELETE' : 'POST');
		var statusUrl = 'api/v1/organisation-favorite/' + organisation.id;
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

	return {
		searchForOrgansiations: searchForOrgansiations,
		loadOrganisations: loadOrganisations,
		addOrganisation: addOrganisation,
		updateOrganisation: updateOrganisation,
		deleteOrganisation: deleteOrganisation,
		changeFavoriteStatus: changeFavoriteStatus
	}
}]);