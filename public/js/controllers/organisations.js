simplecrmApp.controller('OrganisationsController', 
['$scope', '$rootScope', '$timeout', '$modal', 'OrganisationsServices', '$window', '$location',
function OrganisationsController ($scope, $rootScope, $timeout, $modal, OrganisationsServices, $window, $location) {
	
	var startCount = 0;
	var organisationTotalCount = null;
	var searchWasDirty = false;
	var infScrollCounter = 0;
	var stopScroll = false;
	$scope.BtnFavImg = 'empty-star.png';
	$scope.img='empty-star.png';
	$rootScope.saveFilters = true;
	$scope.statuses = {favorites: false};
	var obj = $location.search();

	$scope.loadMore = function loadMore () {
		if(stopScroll){
			return;
		}
	 	infScrollCounter += 5;
	 	if(startCount >= organisationTotalCount){
	 		return;
	 	}
		if(infScrollCounter % 30 == 0){	
			OrganisationsServices.loadOrganisations(startCount, $scope.statuses.favorites)
				.then(function  (res) {
					var push = true;
					for (var i = 0; i < res.organisations.length; i++) {
						for (var a = 0; a < $scope.organisations.length; a++) {
							if(res.organisations[i].id == $scope.organisations[a].id) {
								push = false;
								break;
							}
						}
						if(push == true) {
							$scope.organisations.push(res.organisations[i]);
						}
					}
			});
			startCount +=100;
		}
	};
	
	$scope.searchInOrganisations = function searchInOrganisations () {
		stopScroll = true;
		if($scope.searchPattern !== ''){
			var tempPattern = $scope.searchPattern;
			$timeout(function  () {
				if(tempPattern === $scope.searchPattern){
					OrganisationsServices.searchForOrgansiations(tempPattern, $scope.statuses.favorites)
						.then(function  (res) {
							$scope.organisations = res;
							searchWasDirty = true;
							startCount = 0;
						});
				}		
			},500);
		}
		if(searchWasDirty && $scope.searchPattern === ''){
			stopScroll = false;
			$timeout(function  () {
				loadOrganisations(startCount, $scope.statuses.favorites);
			},500);
		}
	};

	$scope.openAddOrganisation = function openAddOrganisation () {
		var modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/addOrganisationModal.html',
				controller: 'AddOrganisationController'
            });
	};

	$scope.openEditOrganisation = function openEditOrganisation (organisation) {
		var modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/editOrganisationModal.html',
                controller: 'EditOrganisationController',
                size: 'lg',
                resolve: {
                    organisation: function () {
                        return organisation;
                    }
                }
            });
	};
	$scope.changeFavoriteStatus = function changeFavoriteStatus (organisation) {
		OrganisationsServices.changeFavoriteStatus(organisation)
			.then(function  (res) {
				organisation.isFavorite = !organisation.isFavorite;
				if($scope.statuses.favorites){
					loadOrganisations(startCount, $scope.statuses.favorites);
				}
			});
	};

	$scope.getFavorites = function getFavorites () {
		if($scope.statuses.favorites === true){
			loadOrganisations(startCount, true);
			return;
		}else{
			loadOrganisations(0, false);
			return;
		}
	}

	function loadOrganisations (startCount, favorites) {
		OrganisationsServices.loadOrganisations(startCount, favorites)
		.then(function  (res) {
			organisationTotalCount = res.count;
			$scope.organisations = res.organisations;
			searchWasDirty = false;
			startCount += 100;
			if($scope.organisations.length < 20){
				$scope.tableWidth = 'width: 99%;';
			}else{
				$scope.tableWidth = 'width: 100%;';
			}
		});
		}
		if(Object.keys(obj).length > 0){
			$scope.searchPattern = obj.pattern;
			$scope.searchInOrganisations();
		}else{
			loadOrganisations(startCount, $scope.statuses.favorites);
	}
	$scope.lastSortedOrder = {};
	$scope.elementsStyle = {};
	$scope.elementsOrderType = {};

	if(localStorage.lastSortingInOrganisations){
		var objs = ['name', 'address', 'zip', 'city'];
		for (var i = 0; i < objs.length; i++) {
			if(objs[i] === localStorage.lastSortingInOrganisations){
				$scope.lastSortedOrder[objs[i]] = true;
				$scope.elementsStyle[objs[i]] = 'background-color:#D2E4EB';
				$scope.elementsOrderType[objs[i]] = localStorage.lastOrderInOrganisations;
				$scope.selectedCol = objs[i];
			}
			else{
				$scope.lastSortedOrder[objs[i]] = false;
				$scope.elementsStyle[objs[i]] = false;
				$scope.elementsOrderType[objs[i]] = null;
			}
		};
	}else{
		$scope.lastSortedOrder = {name: true, address: false, zip: false, city: false};
		$scope.elementsStyle = {name: 'background-color:#D2E4EB', address: null, zip: null, city: null};
		$scope.elementsOrderType = {name: 'ascending', address: null, zip: null, city: null};
		$scope.selectedCol = 'name';
	}
    $scope.setHeadersColSortingStyles = function setHeadersColSortingStyles (col, order) {
    	for(var i in $scope.elementsStyle){
    		if(i === col){
    			$scope.selectedCol = i;
    			$scope.elementsStyle[i] = 'background-color:#D2E4EB';
    			if($scope.elementsOrderType[i] === 'ascending'){
    				$scope.elementsOrderType[i] = 'descending';
    			}else{
    				$scope.elementsOrderType[i] = 'ascending';
    			}
    			localStorage.lastSortingInOrganisations = col;
				localStorage.lastOrderInOrganisations = $scope.elementsOrderType[i];
    		}else{
    			$scope.elementsStyle[i] = null;
    		}
    	}
    }

    $scope.clearSearchField = function clearSearchField () {
    	$scope.searchPattern = '';
    	loadOrganisations(0, false);
    	$location.search('pattern', null);
    }
    
}]);

simplecrmApp.controller('AddOrganisationController', 
['$scope', '$rootScope', 'OrganisationsServices', '$modalInstance',
function AddOrganisationController ($scope, $rootScope, OrganisationsServices, $modalInstance) {
		
	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	}

	$scope.ok = function ok () {
		var newOrganisation = {
			name: $scope.name,
			address: $scope.address,
			zip: $scope.zip,
			city: $scope.city
		};

		OrganisationsServices.addOrganisation(newOrganisation)
			.then(function  (res) {
				$modalInstance.dismiss();
			},function  (err) {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_CREATION_ERROR};
			});
	};

}]);

simplecrmApp.controller('EditOrganisationController' , 
['$scope', '$rootScope', '$window', 'organisation', 'OrganisationsServices', '$modalInstance',
function EditOrganisationController ($scope, $rootScope, $window, organisation, OrganisationsServices, $modalInstance) {
	$scope.organisation = organisation;

	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	};

	$scope.ok = function ok () {
		var updatedOrganisation = {
			name: $scope.organisation.name,
			address: $scope.organisation.address,
			zip: $scope.organisation.zip,
			city: $scope.organisation.city,
			id: organisation.id
		};
		OrganisationsServices.updateOrganisation(updatedOrganisation)
			.then(function  (res) {
				$modalInstance.dismiss();
			},function  (err) {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.REQUIRED_FIELDS_ERROR};
				$rootScope.messagesClose();
			});
	};

	$scope.confirmDeleteOrganisation = false;
	$scope.askForContactDelete = function askForContactDelete () {
		$scope.confirmDeleteOrganisation = true;

		$scope.deleteOrganisation = function deleteOrganisation (arg) {
			if(arg === true){
				OrganisationsServices.deleteOrganisation(organisation.id)
					.then(function  (res) {
						$modalInstance.dismiss();
						$scope.confirmDeleteOrganisation = false;
						$window.location.reload();
						$rootScope.alert = {type: 'success', msg: $rootScope.translation.ORGANISATION_DELETED_SUCCESS};
						$rootScope.messagesClose();
						
					},function  (err) {
						$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_DELETE_ERROR};
						$rootScope.messagesClose();
					})
			}else{
				$scope.confirmDeleteOrganisation = false;
			}	
		};
	};
	$scope.redirectToOrganisationDeals = function redirectToOrganisationDeals () {
		$modalInstance.dismiss();
	}
}]);