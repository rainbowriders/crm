simplecrmApp.controller('DealsController', 
	['$scope', '$rootScope', '$modal', '$timeout', '$state', '$location', '$filter', 'DealsServices',
function DealsController ($scope, $rootScope, $modal, $timeout, $state, $location, $filter, DealsServices) {

	//set filters for deals
	//
	var dealsLoaded = false;
	var obj = $location.search();
	$scope.orderCriteriaItems = '-updated_at';
	$scope.loadingDeals = true;
	$scope.width = screen.width;
	if($scope.width > 1800){
		$scope.searchClass = 'col-md-2';
		$scope.btnsContainerClass = 'col-md-6';
		$scope.marginBtnsGroup = 'margin-left:5px;';
		$scope.limitTitle = 1000;
		$scope.limitOrgName = 1000;
	}else{
		$scope.searchClass = 'col-md-2';
		$scope.btnsContainerClass = 'col-md-9';
		$scope.btnContainerPadding = 'padding-top:10px';
		$scope.alignRight = 'text-align:left';
		$scope.marginBtnsGroup = 'margin-right:5px;';
		$scope.limitOrgName = 20;
		$scope.limitTitle = 45;
	}
	$scope.stopLoadingDeals = false;
	$scope.statuses = {open: true, lost: false, won: false, favorites: false, cancelled: false};

	$scope.owner = {};
	$scope.owner = {me: true, other: false};
	$scope.img = 'empty-star.png';
	$scope.BtnFavImg = 'empty-star.png';
	$scope.stopScroll = false;
	$scope.startIndexForLoadDeals = 0;
	$scope.dealsCount = 0;
	$scope.searchWasDirty = false;
	$scope.infScrollCounter = 0;

	if(Object.keys(obj).length > 0){
		if(obj.contactId){
			DealsServices.getDealsForContact(obj.contactId)
				.then(function  (res) {
					$scope.deals = res.deals;
					$scope.loadDeals = false;
				});
			dealsLoaded = true;
			$scope.loadDeals = true;

		}
	}

	if(Object.keys(obj).length > 0){
		if(obj.organisationId){
			DealsServices.getDealsForOrganisation(obj.organisationId)
				.then(function  (res) {
					$scope.deals = res.deals;
					$scope.loadDeals = false;
				});
			dealsLoaded = true;
			$scope.loadDeals = true;

		}
	}

	if($rootScope.saveFilters === true && dealsLoaded === false){
		$scope.statuses = $rootScope.statuses;
		$scope.owner = $rootScope.owner;
		$scope.startIndexForLoadDeals = $rootScope.startIndexForLoadDeals;
		$scope.searchPattern = $rootScope.pattern;
		$rootScope.saveFilters = false;
		loadDeals($scope.searchPattern);
		dealsLoaded = true;
	}
	if(Object.keys(obj).length > 0 && dealsLoaded === false){
		$scope.statuses.open = (obj.open === 'true' ? true : false);
		$scope.statuses.lost = (obj.lost === 'true' ? true : false);
		$scope.statuses.won = (obj.won === 'true' ? true : false);
		$scope.statuses.cancelled = (obj.cancelled === 'true' ? true : false);
		if(obj.pattern){
			$scope.searchPattern = obj.pattern;
			loadDeals(obj.pattern);
			dealsLoaded = true;
		}else{
			loadDeals();
			dealsLoaded = true;
		}
	}
	if(localStorage.haveFilters && (Object.keys(obj).length === 0 || !$rootScope.saveFilters) && dealsLoaded === false){
			applyfiltersFromLocalStorage();
			if(localStorage.dealSearchPattern !== ""){
				loadDeals($scope.searchPattern);
				dealsLoaded = true;
			}
			else{
				loadDeals();
				dealsLoaded = true;
			}
		}
	if(dealsLoaded === false){
		loadDeals();
		dealsLoaded = true;
	};
	$scope.reloadDealsWithFilters = function reloadDealsWithFilters () {
		$scope.startIndexForLoadDeals = 0;
		$scope.searchPattern = '';
		loadDeals($scope.searchPattern);
		setFiltersInLocalStorage();
	};

	$scope.loadMore = function loadMore () {
		if($scope.stopScroll){
	 		return;
	 	}
		$scope.infScrollCounter += 5;
		// if($scope.startIndexForLoadDeals >= $scope.dealsCount){
	 // 		return;
	 // 	}
		// if($scope.startIndexForLoadDeals +100 >= $scope.dealsCount){
	 // 		return;
	 // 	}
	 	if($scope.infScrollCounter % 30 == 0){	
			DealsServices.loadDeals($scope.statuses, $scope.owner, $scope.startIndexForLoadDeals, 
				$scope.searchPattern)
				.then(function  (res) {
					var push = true;
				 	for (var i = 0; i < res.deals.length; i++) {
				 		for (var a = 0; a < $scope.deals.length; a++) {
				 			if($scope.deals[a].id === res.deals[i].id){
				 				push =false;
				 				break;
				 			}
				 		};
				 		if(push === true){
			 				$scope.deals.push(res.deals[i]);				 		
				 		}
				 	};	
			});
		 	$scope.startIndexForLoadDeals +=100;
		}
	};

	$scope.searchInDeals = function searchInDeals () {
		$scope.stopScroll = true;
		if($scope.searchPattern !== ''){
			var tempPattern = $scope.searchPattern;

			$timeout(function  () {
				if(tempPattern === $scope.searchPattern){
					loadDeals($scope.searchPattern);
					$scope.searchWasDirty = true;
					$scope.startIndexForLoadDeals = 0;
				}
			},500);
		}
		if($scope.searchWasDirty && $scope.searchPattern === ''){
			$scope.stopScroll = false;
			$timeout(function  () {
				loadDeals();
				$scope.searchWasDirty = false;
			},500);
		}
		localStorage.dealSearchPattern = $scope.searchPattern;
	};
	$scope.changeFavoriteStatus = function changeFavoriteStatus (e, deal) {
		e.stopPropagation();
		DealsServices.changeFavoriteStatus(deal)
			.then(function  (res) {
				$scope.reloadDealsWithFilters();
			}, function  (err) {
				deal.isFavorite = !deal.isFavorite;
			});
		deal.isFavorite = !deal.isFavorite;
	};

	function loadDeals(pattern) {
		var owner = {me:true, other: false};
		var statuses = {open: true, lost: false, won: false, favorites: false, cancelled: false};
		$scope.statuses = $scope.statuses || statuses;
		$scope.owner = $scope.owner || owner;
		if($scope.owner.me === false && $scope.owner.other === false){
			$scope.deals = [];
		}else if($scope.statuses.open === false && $scope.statuses.lost === false && $scope.statuses.won === false && $scope.statuses.cancelled === false){
			$scope.deals = [];
		}else{

			var pattern = pattern || '';
			DealsServices.loadDeals($scope.statuses, $scope.owner, $scope.startIndexForLoadDeals, pattern)
			.then(function  (res) {
				$scope.deals = [];
				$scope.dealsCount = res.count;
				$scope.deals = res.deals;
				if($scope.dealsCount < $scope.startIndexForLoadDeals + 100){
					$scope.startIndexForLoadDeals += ($scope.startIndexForLoadDeals + 100) - $scope.dealsCount;
					$scope.stopLoadingDeals = true;
				}else {
					$scope.startIndexForLoadDeals += 100;
				}
				$scope.loadDeals = false;
				if($scope.deals.length < 20){
					$scope.tableWidth = 'width: 99%;';
				}else{
					$scope.tableWidth = 'width: 99.55%;';
				}
			});
		}
	};
	//helpers for html in table's status col
	$scope.dealStatusClass = function dealStatusClass (arg) {
		switch(arg){
			case 'open': return '';break;
			case 'won': return '#1AB667';break;
			case 'lost': return '#F05050';break;
			case 'cancelled': return '#F05050';break;
		}
	};

	$scope.dealStatusTranslation = function dealStatusTranslation (arg) {
		switch(arg){
			case 'open': return 'OPEN';break;
			case 'won': return 'WON';break;
			case 'lost': return 'LOST';break;
			case 'cancelled': return 'CANCELLED'; break;
		}
	};

	$scope.openAddDeal = function openAddDeal () {
		var modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/addDealModal.html',
				controller: 'AddDealController'
            });
	};
	$scope.openDeal = function(item){
		$state.go('dealsdetails',{"dealID":item.id});
    };

    function setFiltersInLocalStorage () {
    	localStorage.haveFilters = true;
    	localStorage.dealSearchPattern = $scope.searchPattern;
		localStorage.dealStatusOpen = $scope.statuses.open;
		localStorage.dealStatusLost = $scope.statuses.lost;
		localStorage.dealStatusWon = $scope.statuses.won;
		localStorage.dealStatusCancelled = $scope.statuses.cancelled;
		localStorage.dealStatusFavorites = $scope.statuses.favorites;
		localStorage.dealOwnerMe = $scope.owner.me;
		localStorage.dealOwnerother = $scope.owner.other;
		// localStorage.startIndexForLoadDeals = $scope.startIndexForLoadDeals;
    }
    function applyfiltersFromLocalStorage () {
    	$scope.searchPattern = localStorage.dealSearchPattern;
    	$scope.statuses.open = (localStorage.dealStatusOpen == 'true'? true: false);
    	$scope.statuses.lost = (localStorage.dealStatusLost == 'true'? true: false);
    	$scope.statuses.won = (localStorage.dealStatusWon == 'true'? true: false);
		$scope.statuses.cancelled = (localStorage.dealStatusCancelled == 'true'? true: false);
    	$scope.statuses.favorites = (localStorage.dealStatusFavorites == 'true'? true: false);
    	$scope.owner.me = (localStorage.dealOwnerMe == 'true'? true: false);
    	$scope.owner.other = (localStorage.dealOwnerother == 'true'? true: false);
    	// $scope.startIndexForLoadDeals = localStorage.startIndexForLoadDeals;
    }

    $scope.clearSearchField = function clearSearchField () {
    	$scope.searchPattern = null;
    	$location.search('pattern', null);
    	$rootScope.pattern = null;
    	localStorage.removeItem('dealSearchPattern');
    	$scope.startIndexForLoadDeals = 0;
    	loadDeals();
    };
    
	$scope.lastSortedOrder = {};
	$scope.elementsStyle = {};
	$scope.elementsOrderType = {};
    if(localStorage.lastSortingInDeals){
		var objs = ['title', 'organisation', 'contact', 'value', 'stage', 'status', 'updated', 'owner', 'favorotes'];
		for (var i = 0; i < objs.length; i++) {
			if(objs[i] === localStorage.lastSortingInDeals){
				$scope.lastSortedOrder[objs[i]] = true;
				$scope.elementsStyle[objs[i]] = 'background-color:#D2E4EB';
				$scope.elementsOrderType[objs[i]] = localStorage.lastOrderInDeals;
				$scope.selectedCol = objs[i];
			}
			else{
				$scope.lastSortedOrder[objs[i]] = false;
				$scope.elementsStyle[objs[i]] = false;
				$scope.elementsOrderType[objs[i]] = null;
			}
		};
	}
	else{
	    $scope.lastSortedOrder = 
	    {title: false, organisation: false, contact: false, value: false, stage: false, status: false, updated: true, owner: false, favorotes: false};
	    $scope.elementsStyle = 
	    {title: null, organisation: null, contact: null, value: null, stage: null, status: null, updated: 'background-color:#D2E4EB', owner: null, favorotes: null};
		$scope.elementsOrderType = {title: null, organisation: null, contact: null, value: null, stage: null, status: null, updated: 'descending', owner: null, favorotes: null};
		$scope.selectedCol = 'updated';
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
    			localStorage.lastSortingInDeals = col;
				localStorage.lastOrderInDeals = $scope.elementsOrderType[i];
    		}else{
    			$scope.elementsStyle[i] = null;
    		}
    	}
    }

    $rootScope.$watch('loadDeals', function  () {
    	if($rootScope.loadDeals === true){
    		$scope.searchPattern = '';
    		loadDeals();  
    		$rootScope.loadDeals = false;
    		dealsLoaded = true;
    	}
    });

}]);


simplecrmApp.controller('AddDealController', 
	['$scope', '$q',  '$location', '$rootScope', '$modal' ,'CurrencyServices', 'UserServices', 'StagesServices', 'OrganisationsServices',
	'ContactsServices', 'UserServices', 'DealsServices', '$modalInstance',
function AddDealController ($scope, $q, $location, $rootScope, $modal, CurrencyServices, UserServices, StagesServices, 
	OrganisationsServices, ContactsServices, UserServices, DealsServices, $modalInstance) {

	//get chosen currency data
	$scope.selectedCurrency = {id:parseInt(localStorage.selectedCurrencyID)};
	CurrencyServices.getTypesOfCurrencies()
		.then(function  (res) {
			$scope.currencies = res;
		});

	//get stage from DB
	$scope.selectedStage = {id:1};
	StagesServices.getStages()
		.then(function  (res) {
			$scope.stages = res;
		});

	//set default status
	$scope.selectedStatus = {id:0};

	//search in organisations
	$scope.getOrganisations = function getOrganisations (){
		var pattern = $scope.selectedOrganisation.name;
		$scope.selectedOrganisation.id = null;
		$scope.organisations = null;
		if(pattern){
			OrganisationsServices.searchForOrgansiations(pattern)
			.then(function (res) {
				$scope.showOrganisationsContainer = res.length;
				$scope.organisations = res;
			});
		}
	};

	//search in contacts
	$scope.getContacts = function getContacts () {
		var pattern = $scope.selectedContact.name;
		$scope.selectedContact.id = null;
		if(pattern){
			ContactsServices.searchForContacts(pattern)
				.then(function  (res) {
					$scope.showContactsContainer = res.contacts.length;
					$scope.contacts = res.contacts;
				});
		}
		if(pattern.length == 0){
			$scope.showContactsContainer = 0;
		}
	};
	//search for owner
	UserServices.getAllUsers()
		.then(function  (res) {
			$scope.users = res;
			for (var i = 0; i < $scope.users.length; i++) {
				if($scope.users[i].id == localStorage.id){
					$scope.selectedOwner = $scope.users[i];
					break;
				}
			};
		});
	$scope.selectedOrganisation = {};
	$scope.setSelectedOrganisation = function  setSelectedOrganisation(organisation) {
		$scope.selectedOrganisation = organisation;
		$scope.showOrganisationsContainer = 0;
	};

	$scope.selectedContact = {};
	$scope.setSelectedContact = function setSelectedContact (contact) {
		$scope.selectedContact = contact;
		$scope.showContactsContainer = 0;
	};

	$scope.validateValue = function validateValue(val) {
		var regex = /^(\d*([.,](?=\d{2}))?\d+)+((?!\2)[.,]\d\d)?$/;
		if(regex.test(val)) {
			return true;
		} else {
			return false;
		}
	};
	$scope.ok = function  () {
		var newDeal = {};

		newDeal.title = $scope.title;
		if(typeof($scope.selectedOrganisation.name) == 'object'){
			newDeal.organisationID = $scope.selectedOrganisation.name.id;
		}else{
			newDeal.organisationName = $scope.selectedOrganisation.name;
		}
		if(typeof($scope.selectedContact.name) == 'object'){
			newDeal.contactID = $scope.selectedContact.name.id;
		}else{
			newDeal.contactName = $scope.selectedContact.name;
		}

        newDeal.userID = $scope.selectedOwner.id;
        newDeal.stageID = $scope.selectedStage.id;
        newDeal.currencyID = $scope.selectedCurrency.id;
        newDeal.value = $scope.value;
		if(!$scope.validateValue($scope.value) && $scope.value) {
			return;
		}
        if($scope.selectedStatus.id == 0){
           newDeal.status = 'open';
        }
        else if($scope.selectedStatus.id == 1){
           newDeal.status = 'won';
        }
        else if($scope.selectedStatus.id == 2){
           newDeal.status = 'lost';
        }else if($scope.selectedStatus.id == 3){
			newDeal.status = 'cancelled';
		}
        DealsServices.addDeal(newDeal)
        	.then(function  (res) {
        		$location.path('/lead:' + res.deal.id);
				$rootScope.alert = {type: 'success', msg: $rootScope.translation.DEAL_CREATION_SUCCESS};
				$rootScope.messagesClose();
				$modalInstance.dismiss();
        	},function  () {
				$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_CREATION_ERROR};
        	});
	};
	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	};

}]);
simplecrmApp.controller('DeleteCommentController', ['$scope', '$modalInstance', 'comment',function ($scope, $modalInstance, comment) {
    $scope.confirmDeleteComment = function (arg) {
        if(arg == 'no') {
            $modalInstance.dismiss();
        } else {
            $modalInstance.close(comment);
        }
    }
}]);
simplecrmApp.controller('ShowDealController',
	['$scope' , '$rootScope', '$location', '$modal', '$sce', 'DealsServices',
	'CommentServices', 'StagesServices',
function ShowDealController ($scope , $rootScope, $location, $modal, $sce, DealsServices,
 CommentServices, StagesServices) {
	//get deal id from url
	var dealId = $location.path().substring(6, $location.path().length);
	//get deal data
	showDeal(dealId);	
	getStages();
	$scope.newcomment = $scope.newcomment || '';
	$rootScope.saveFilters = true;
	//watch for deal update and modal close
	//
	$rootScope.dealUpdated = false;
	$scope.$watch('dealUpdated', function  () {
		if($rootScope.dealUpdated === true){
			showDeal(dealId);
			$rootScope.dealUpdated = false;
		}
	});

	$scope.currUserID = localStorage.id;
	$scope.openAddDeal = function openAddDeal () {
		var modalInstance = $modal.open({
			templateUrl: '/angular/templates/modals/addDealModal.html',
			controller: 'AddDealController'
		});
	};

	$scope.postComment = function postComment () {
		var text = $scope.newcomment;
		if(text.length > 0 ){
			var commentData = {
				text: text,
				dealID: dealId,
				accountID: localStorage.accountid,
				userID: localStorage.id
			};
			CommentServices.createComment(commentData)
				.then(function  (res) {
					$scope.newcomment = '';
					showDeal(dealId);
				},function  (err) {
					$rootScope.alert = {type: 'danger', msg: $rootScope.translation.REQUIRED_FIELDS_ERROR};
					$rootScope.messagesClose();
				});
		}
	};

	$scope.editComment = function  (comment) {
              $scope.commentInEditMode = true;
              $scope.commentInEditModeId = comment.id;
              $scope.currCommentText = comment.text;
              $scope.currCommentNewText = comment.text;
              
        $scope.cancelEditComment = function cancelEditComment () {
			$scope.commentInEditMode = false;
		};
		$scope.doneEditComment = function doneEditComment (text) {
			var commentData = {id: comment.id, text: text};
			CommentServices.editComment(commentData)
				.then(function  (res) {
					$scope.commentInEditMode = false;
					showDeal(dealId);
				},function  (res) {
					$rootScope.alert = {type: 'danger', msg: $rootScope.translation.REQUIRED_FIELDS_ERROR};
					$rootScope.messagesClose();
				});
		};      
	};
	$scope.deleteComment = function deleteComment(comment) {

		var modalInstance = $modal.open({
			templateUrl: '/angular/templates/modals/deleteCommentPromt.html',
			controller: 'DeleteCommentController',
            resolve: {
                comment: function () {
                    return comment;
                }
            }
		});
        modalInstance.result.then(function (comment) {
            CommentServices.deleteComment(comment)
                .then(function (res) {
                    showDeal(dealId);
                });
        })
	};
	//change stages with fast btns
	$scope.viewStageBtns = false;
    $scope.showStageBtns = function showStageBtns () {
        $scope.viewStageBtns = !$scope.viewStageBtns;
    };

    $scope.changeDealStage = function changeDealStage (counter) {
		$scope.deal.stageID = counter + 1;
		$scope.deal.stage = $scope.stages[counter];
		DealsServices.updateDeal($scope.deal)
			.then(function  (res) {
				showDeal(dealId);	
				$scope.showStageBtns();
			});
    };
    //change status with fast btns
    $scope.showChangeStatusBtns = false;
    $scope.changeStatusBtns = function changeStatusBtns () {
        $scope.showChangeStatusBtns = !$scope.showChangeStatusBtns;
    };

    $scope.changeDealStatus = function changeDealStatus (newStatus) {
    	$scope.deal.status = newStatus;
		DealsServices.updateDeal($scope.deal)
			.then(function  (res) {
				showDeal(dealId);	
				$scope.changeStatusBtns();
			});
    };

    $scope.openEditDeal = function openEditDeal (deal) {
		$modalInstance = $modal.open({
                templateUrl: '/angular/templates/modals/editDealModal.html',
                controller: 'EditDealController'
                
            });
    };

	$scope.addSmilieToCommentArea = function addSmilieToCommentArea (smilie) {
		$scope.newcomment += ':' + smilie + ':';
		return;
	};

	function showDeal (dealId) {
		DealsServices.showDeal(dealId)
		.then(function  (res) {
			$scope.deal = res.deal;
			$scope.logs = res.logs;
			for (var i = 0; i < $scope.deal.comments.length; i++) {
				var commentText = $scope.deal.comments[i].text.replace('<', '&lt;');
				$scope.deal.comments[i].text = commentText;
			};
		});
	};

	function getStages () {
		StagesServices.getStages()
			.then(function  (res) {
				$scope.stages = res;
			});
	};
}]);

simplecrmApp.controller('EditDealController', 
	['$scope', '$rootScope', '$location', 'DealsServices', 'CurrencyServices' , 'OrganisationsServices',
	'ContactsServices', 'UserServices', 'StagesServices', '$modalInstance',
function EditDealController ($scope, $rootScope, $location, DealsServices, CurrencyServices, OrganisationsServices,
	ContactsServices, UserServices, StagesServices, $modalInstance) {
	var dealId = $location.path().substring(6, $location.path().length);

	showDeal(dealId);
	getStages();
	getCurrencies();
	
	//search in organisations
	$scope.getOrganisations = function getOrganisations (){
		$scope.selectedOrganisation.id = null;
		$scope.organisations = null;
		var pattern = $scope.selectedOrganisation.name;
		if(pattern && pattern.length > 0 && pattern != 'undefined'){
			OrganisationsServices.searchForOrgansiations(pattern)
			.then(function (res) {
				$scope.showOrganisationsContainer = res.length;
				$scope.organisations = res;
			});
		}
	};

	//search in contacts
	$scope.getContacts = function getContacts () {
		$scope.selectedContact.id = null;
		var pattern = $scope.selectedContact.name;
		if(pattern){
			ContactsServices.searchForContacts(pattern)
				.then(function  (res) {
					$scope.showContactsContainer = res.contacts.length;
					$scope.contacts = res.contacts;
				});
		}
	};

	//set new organsiation from DB
	$scope.setSelectedOrganisation = function  setSelectedOrganisation(organisation) {
		$scope.selectedOrganisation = organisation;
		$scope.showOrganisationsContainer = 0;
	};
	//set new contact person from DB
	$scope.setSelectedContact = function setSelectedContact (contact) {
		$scope.selectedContact = contact;
		$scope.showContactsContainer = 0;
	}

	//close modal instance without changes
	$scope.cancel = function cancel () {
		$modalInstance.dismiss();
	}

	//delete deal btn actions
	$scope.confirmDeleteDeal = false;
    $scope.askForDeleteDeal = function askForDeleteDeal () {
        $scope.confirmDeleteDeal = true;

        $scope.deleteDeal = function deleteDeal (arg) {
			if(arg === true){
				DealsServices.deleteDeal(dealId)
					.then(function  (res) {
						$location.path('/leads');
						$scope.cancel();
					},function  (err) {
						$rootScope.alert = {type: 'danger', msg: $rootScope.translation.ITEM_DELETE_ERROR};
						$rootScope.messagesClose();
					});
			}else if(arg === false){
				$scope.confirmDeleteDeal = false;
			}
        };
    };

	$scope.validateValue = function validateValue(val) {
		var regex = /^(\d*([.,](?=\d{2}))?\d+)+((?!\2)[.,]\d\d)?$/;
		if(regex.test(val)) {
			return true;
		} else {
			return false;
		}
	};
    //save deal changes
    //
	$scope.ok = function oks () {
		var updateDeal = {};
		if(typeof($scope.selectedOrganisation.name) == 'object'){
			updateDeal.organisationID = $scope.selectedOrganisation.name.id;
		}else{
			updateDeal.organisationName = $scope.selectedOrganisation.name;
		}
		if(typeof($scope.selectedContact.name) == 'object'){
			updateDeal.contactID = $scope.selectedContact.name.id;
		}else{
			updateDeal.contactName = $scope.selectedContact.name;
		}
		updateDeal.id = dealId;
		updateDeal.title = $scope.title;
		updateDeal.userID = $scope.selectedOwner.id;
		updateDeal.stageID = $scope.selectedStage.id;
		updateDeal.currencyID = $scope.selectedCurrency.id;
		updateDeal.value = $scope.value;
		updateDeal.status = getSelectedStatus($scope.selectedStatus);
		if(!$scope.validateValue($scope.value) && $scope.value) {
			return;
		}
		DealsServices.updateDeal(updateDeal)
			.then(function  (res) {
				$rootScope.dealUpdated = true;
				$modalInstance.dismiss();
			},function  (err) {
				// $rootScope.alert = {type: 'danger', msg: $rootScope.translation.REQUIRED_FIELDS_ERROR};
				$rootScope.messagesClose();
			});
	};

	function showDeal (dealId) {
		DealsServices.showDeal(dealId)
			.then(function  (res) {
				$scope.title = res.deal.title;
				$scope.value = res.deal.value;
				$scope.selectedCurrency = {id: parseInt(res.deal.currencyID)};
				if(res.deal.organisationID !== null){
					$scope.selectedOrganisation = {name: res.deal.organisation.name};
				}else{
					$scope.selectedOrganisation = {};
				}
				if(res.deal.contactID !== null){
					$scope.selectedContact = {name: res.deal.contact.name};
				}else{
					$scope.selectedContact = {};
				}
				$scope.selectedStage = {id: parseInt(res.deal.stageID)};
				getSelectedStatus(res.deal);
				getOwner(res.deal.userID);
			});
	};

	function getStages () {
		StagesServices.getStages()
			.then(function  (res) {
				$scope.stages = res;
			});
	};

	function getCurrencies () {
		CurrencyServices.getTypesOfCurrencies()
		.then(function  (res) {
			$scope.currencies = res;
		});
	};

	function getSelectedStatus (data) {
		if(data.status){
			if(data.status == 'open'){
	    		$scope.selectedStatus = {id:0};
		    }
		    else if(data.status == 'won'){
		    	$scope.selectedStatus = {id:1};
		    }
		    else if(data.status == 'lost'){
		    	$scope.selectedStatus = {id:2};
		    } else if(data.status == 'cancelled'){
				$scope.selectedStatus = {id:3};
			}
		}else{
			if(data.id == 0){
				return 'open';
        	}else if(data.id == 1){
           		return 'won';
			}else if(data.id == 2){
           		return 'lost';
        	}else if(data.id == 3){
				return 'cancelled';
			}
		}
	};
	function getOwner (userId) {
		//get users to chose owner
		UserServices.getAllUsers()
			.then(function  (res) {
				$scope.users = res;
				for (var i = 0; i < res.length; i++) {
					if(userId === res[i].id){
						$scope.selectedOwner = res[i];
						break;
					}
				};
			});
    }
}]);	