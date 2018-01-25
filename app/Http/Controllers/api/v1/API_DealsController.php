<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Contacts;
use App\Accounts;
use App\Organisations;
use App\Deals;
use App\Users;
use App\DealFavorite;
use Request;
use Response;
use Input;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use DateTime;

class API_DealsController extends Controller {

    private function getAuthenticatedUser() {
        try {

            if (!$user = JWTAuth::parseToken()->toUser()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }
        if($user->last_login != null) {
            $last_login = Carbon::parse($user->last_login);
            $now = Carbon::now();
            if($now->diffInDays($last_login) != 0) {
                $user->last_login = new DateTime();
                $user->save();
            }
        } else {
            $user->last_login = new DateTime();
            $user->save();
        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() 
    {
      $user = $this->getAuthenticatedUser();

      if(Input::get('contactId'))
      {
        $deals = $this->getDealsForCotnact(Input::get('contactId'), $user->id);

        return array('count' => count($deals), 'deals' => $deals); 
      }
      
      if(Input::get('organisationId'))
      {
        $deals = $this->getDealsForOrganisation(Input::get('organisationId'), $user->id);

        return array('count' => count($deals), 'deals' => $deals); 
      }

      $accountID = Input::get('account');
      settype($accountID, "integer");

      $data = Input::all();
      $dealsCriterias = array();
      //set search criterias
      if(array_key_exists('pattern', $data))
      {
        $pattern = $data['pattern'];
      }
      if(array_key_exists('userid', $data))
      {
        $userID = $data['userid'];
        settype($userID, "integer");
      }
      if(array_key_exists('statuses', $data))
      {
        $statuses = $data['statuses'];
      }
      if(array_key_exists('favorites', $data))
      {
        $favorites = DealFavorite::where('userID', $user->id)->get();
        $favoritesIDs = array();
        foreach ($favorites as $key => $value) {
          array_push($favoritesIDs, $value->dealID);
        }
        $deals = Deals::whereIn('id', $favoritesIDs)
            ->with('organisation')
            ->with('user')
            ->with('stage')
            ->with('currency')
            ->get();
        $result = $this->filterByStatus($deals, $statuses);
        if(array_key_exists('pattern', $data))
        {
          $result = $this->filterByPattern($result, $pattern);
        }
        $result = $this->handleResult($result, $user->id);
        return array('count'=>count($result),'deals' => $result);
      }

      if(!array_key_exists('pattern', $data))
      {
        $counter = $data['start'];

        //return only owner deals
        if(array_key_exists('userid', $data))
        {
          $dealsCount = Deals::where('accountID',$accountID)->where('userID',$userID)->count();
          $deals = $this->getDealsWithGivenOwnerID($userID, $accountID, $counter);
          $result = $this->handleResult($deals, $user->id);

          return array('count' => $dealsCount, 'deals' => $result);
        }
        //return all deals
        $deals = $this->getAllDeals($accountID, $counter);
        $result = $this->handleResult($deals['deals'],$user->id);
        return array('count' => $deals['count'], 'deals' => $result);  
      }
      else
      {
        if(array_key_exists('userid', $data))
        {
          //return all deals from search engine with given owner
          $result = $this->getDealsForFilteringWithGivenOwner($userID, $accountID);  
          $result = $this->filterByPattern($result, $pattern);
          $result = $this->filterByStatus($result, $statuses);
          $result = $this->handleResult($result, $user->id);

          return array('count'=>count($result),'deals' => $result);
        }
        else 
        {
          //return all deals from search engiene from all data
          $result = $this->getDealsForFilteringFromAllData($accountID);  
          $result = $this->filterByPattern($result, $pattern);
          $result = $this->filterByStatus($result, $statuses);
          $result = $this->handleResult($result, $user->id);

          return array('count'=>count($result),'deals' => $result);
        }
      }
            
    } 
    
    //return tight view of deals
    //
    private function handleResult($deals, $userId)
    {
      $result = array();
      for ($i=0; $i <count($deals) ; $i++) { 
        $currDeal = $deals[$i];
        $deal = array();
        $dealUser = array();
        $dealUser = $currDeal['user'];
        $deal['id'] = $currDeal['id'];
        $deal['title'] = $currDeal['title'];
        $deal['userID'] = $currDeal['userID'];
        $deal['ownerName'] = $dealUser['name'];
        $deal['value'] = $currDeal['value'];
        $deal['status'] = $currDeal['status'];
        $deal['updated_at'] = $currDeal['updated_at'];
        $deal['accountID'] = $currDeal['accountID'];
        if(count($currDeal['organisation']) > 0)
        {
          $deal['organisation'] = array();
          $deal['organisation']['name'] = $currDeal['organisation']['name'];
        }
        if(count($currDeal['contactID']) !== null)
        {
          $deal['contact'] = array();
          $deal['contact']['name'] = $currDeal['contact']['name'];
        }
        $deal['stage'] = array();
        $deal['stage']['id'] = $currDeal['stage']['id'];
        $deal['stage']['title'] = $currDeal['stage']['title'];
        $deal['currency'] = $currDeal['currency'];
        $isFavorite = DealFavorite::where('dealID', $currDeal['id'])
        ->where('userID', $userId)->get();
        if(count($isFavorite) > 0)
        {
          $deal['isFavorite'] = true;
        }
        else
        {
          $deal['isFavorite'] = false;
        }
        array_push($result, $deal);
      }

      return $result;
    }

    //return deals with given owner from no search params
    //
    private function getDealsWithGivenOwnerID($userId, $accountId, $counter)
    {
      $deals = Deals::limit(100)->orderBy('updated_at', 'DESC')
            ->with('organisation')
            ->with('user')
            ->with('stage')
            ->with('currency')
            ->where(function($query){
              foreach(\Input::get('statuses') as $status) {
                $query->orWhere('status', '=', $status);
              }
            })
            ->where('userID', $userId)
            ->where('accountID', $accountId)
            ->skip($counter)
            ->get();
      return $deals;
    }

    //return all deals from no search params
    //
    private function getAllDeals($accountId, $counter)
    { 
      $count = Deals::where('accountID',$accountId)
          ->where(function($query){
              foreach(\Input::get('statuses') as $status) {
                $query->orWhere('status', '=', $status);
              }
            })
          ->count();

      $deals = Deals::limit(100)->orderBy('updated_at', 'DESC')
          ->with('organisation')
          ->with('user')
          ->with('stage')
          ->with('currency')
          ->where('accountID',$accountId)
          ->where(function($query){
              foreach(\Input::get('statuses') as $status) {
                $query->orWhere('status', '=', $status);
              }
            })
          ->skip($counter)
          ->get();

      return array('deals' => $deals, 'count' => $count);
    }
    //return deals with given owner from search engine no filtered 
    //
    private function getDealsForFilteringWithGivenOwner($userId, $accountId)
    {
      $deals = Deals::with('organisation')
            ->with('user')
            ->with('stage')
            ->with('currency')
            ->where('userID', $userId)
            ->where('accountID', $accountId)
            ->get();

      return $deals;
    }
    // return deals from all data from search engine
    // 
    
    private function getDealsForFilteringFromAllData($accountId)
    {
      $deals = Deals::with('organisation')
            ->with('user')
            ->with('stage')
            ->with('currency')
            ->where('accountID', $accountId)
            ->get();

      return $deals;
    }

    // return fitered deals by pattern from search engine
    // 
    private function filterByPattern($deals, $pattern)
    {
      $result = array();
      for ($i=0; $i < count($deals); $i++) { 
        $currDeal = $deals[$i];
        $pattern = strtolower($pattern);
        $dealTitle = strtolower($currDeal['title']);

        if (strpos($dealTitle, $pattern) !== false){
          array_push($result, $deals[$i]);
          continue;
        }
        
        $organisation = array();
        $organisation['name'] = $currDeal['organisation']['name'];
        $organisationName = strtolower($organisation['name']);
        if (strpos($organisationName, $pattern) !== false){
          array_push($result, $deals[$i]);
          continue;
        } 

        $contactName = strtolower($currDeal['contact']['name']);
        if (strpos($contactName, $pattern) !== false){
          array_push($result, $deals[$i]);
          continue;
        } 
        
      }

      return $result;
    }
    // return deals with given status from search engine
    //  
    private function filterByStatus($deals, $statuses)
    {
      $result = array();

      for ($i=0; $i < count($deals); $i++) { 
        $currDealStatus = $deals[$i]['status'];
        for ($j=0; $j < count($statuses) ; $j++) { 
          if($currDealStatus === $statuses[$j]){
            array_push($result, $deals[$i]);
            break;
          }
        }
      }
      return $result;
    }

    private function getDealOwnerNameShort($name)
    {
      if (strpos($name, " ") !== false) 
      {
        $name = explode(" ", $name);
        $result = strtoupper($name[0][0]);
        $result = $result . '.';
        $result = $result . ucfirst($name[1]);

        return $result;
      }
      return $name;
    }

    private function getDealsForCotnact($contactId, $userId)
    {
      $deals = Deals::orderBy('updated_at')->where('contactID', $contactId)
      ->with('organisation')
      ->with('user')
      ->with('stage')
      ->with('currency')
      ->get();
      if($deals)
      {
        $deals = $this->handleResult($deals, $userId);
      }
      return $deals;
    }
    private function getDealsFororganisation($organisationId, $userId)
    {
      $deals = Deals::orderBy('updated_at')->where('organisationID', $organisationId)
      ->with('organisation')
      ->with('user')
      ->with('stage')
      ->with('currency')
      ->get();
      if($deals)
      {
        $deals = $this->handleResult($deals, $userId);
      }
      return $deals;
    }
    
    public function create() {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {

        $user = $this->getAuthenticatedUser();
        $userID = $user['id'];
        $accountID = Input::get('account');

        $organisationID = null;
        $contactID = null;
        if(Input::get('organisationID')){
          $organisationID = Input::get('organisationID');
        }
        if(Input::get('organisationName')){
          $data = array();
          $data['name'] = Input::get('organisationName');
          $data['accountID'] = $accountID;

          $neworganisation = Organisations::firstOrCreate($data);
          $neworganisation->save();

          $organisationID = $neworganisation->id;
        }

        if(Input::get('contactID')){
          $contactID = Input::get('contactID');
        }
        if(Input::get('contactName')){
          $data = array();
          $data['name'] = Input::get('contactName');
          $data['accountID'] = $accountID;

          $newcontact = Contacts::firstOrCreate($data);
          $newcontact->save();

          $contactID = $newcontact->id;
        }

        $data = array();
        $data['title'] = Input::get('title');
        $data['value'] = Input::get('value');
        $data['stageID'] = Input::get('stageID');
        $data['currencyID'] = Input::get('currencyID');
        $data['status'] = Input::get('status');
        $data['contactID'] = $contactID;
        $data['organisationID'] = $organisationID;
        $data['accountID'] = $accountID;
        $data['userID'] = Input::get('userID');
        $newdeal = Deals::firstOrCreate($data);
        $newdeal->save();

        $dealID = $newdeal->id;

        $deal = Deals::with('organisation')->with('contact')->with('user')->with('stage')->with('currency')->find($dealID);

        return Response::json(array(
                    'error' => false,
                    'deal' => $deal), 200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $deal = Deals::with('organisation')->with('revisions')->with('stage')
        ->with('currency')->with('contact')->with('user')
        ->with(['comments' => function($query) {
                        $query->with('user');
                        $query->with('revisions');
                    }])
        ->find($id);

        $logs=array();
        // var_dump($deal);
        if($deal->revisions){

        foreach($deal->revisions as $revision){
            foreach($revision->getDiff() as $key => $diff){

                $logs[] = array('user' => $revision->user, 
                  'date' => $revision->created_at->format('Y-m-d H:i:s'), 
                  'label' => $revision->label($key), 'old' => $revision->old($key), 
                  'new' => $revision->new($key));
                }
            }
        }

        foreach ($deal->comments as $comment) {
            foreach ($comment['revisions'] as $revision) {
                $new = array();
                $new = $revision['new'];

                if(count($revision['old']) > 0)
                {
                    $label = 'comment_edited';
                    if($revision['old']['dealID'] != $deal->id) {
                        continue;
                    }
                }
                else
                {
                    $label = 'comment_posted';
                    if($revision['new']['dealID'] != $deal->id) {
                        continue;
                    }
                }
                $logs[] = array('user' => $revision['user'],
                    'comment' => true,
                    'date' => $revision['created_at']->format('Y-m-d H:i:s'),
                    'label' => $label
                );
            }
        }
        return Response::json(array(
                    'deal' => $deal,
                    'logs' => $logs), 200
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $user = $this->getAuthenticatedUser();
        $userID = $user['id'];
        $accountID = Input::get('account');

        $updatedeal = Deals::find($id);

        $organisationID = null;
        $contactID = null;
        if(Input::get('organisationID')){
          $organisationID = Input::get('organisationID');
        }
        if(Input::get('organisationName')){
          $data = array();
          $data['name'] = Input::get('organisationName');
          $data['accountID'] = $accountID;

          $neworganisation = Organisations::firstOrCreate($data);
          $neworganisation->save();

          $organisationID = $neworganisation->id;
        }

        if(Input::get('contactID')){
          $contactID = Input::get('contactID');
        }
        if(Input::get('contactName')){
          $data = array();
          $data['name'] = Input::get('contactName');
          $data['accountID'] = $accountID;

          $newcontact = Contacts::firstOrCreate($data);
          $newcontact->save();

          $contactID = $newcontact->id;
        }

        $updatedeal->title = Input::get('title');
        $updatedeal->value = Input::get('value');
        $updatedeal->stageID = Input::get('stageID');
        $updatedeal->currencyID = Input::get('currencyID');
        $updatedeal->status = Input::get('status');
        $updatedeal->contactID = $contactID;
        $updatedeal->organisationID = $organisationID;
        $updatedeal->accountID = $accountID;
        $updatedeal->userID = Input::get('userID');;

        $updatedeal->save();

        $deal = Deals::with('organisation')->with('contact')->with('user')->with('stage')->with('currency')->find($id);

        return Response::json(array(
                    'error' => false,
                    'deal' => $deal), 200
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {

        $user = $this->getAuthenticatedUser();
        $userID = $user['id'];
        $accountID = $user['accountID'];

        $deal = Deals::find($id);
        $deal->delete();
        return Response::json(array(
                    'error' => false,
                    'deal' => $deal), 200
        );
    }

}
