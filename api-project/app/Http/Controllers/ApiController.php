<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\Bids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use App\Mail\AuctionWon;

class ApiController extends Controller
{

    public function auctionWonNotification(Request $request){
      Mail::to($request->email)->send(new AuctionWon());
    }

    public function bidMadeNotification(Request $request){
      $data = json_decode($request->emails);
      foreach ($data as $recipient) {
        Mail::to($recipient)->send(new WelcomeMail());
      }
    }

    public function getUser($id) {
      if (User::where('id', $id)->exists()) {
        $user = User::find($id);
        return response($user, 200);
      }
      else{
        return response(['error' => 'Requested user does not exist']);
      }
    }

    public function getAllUsers() {
      $users = User::all()->toJson(JSON_PRETTY_PRINT);
      return response($users, 200);
    }

    public function login(Request $request){
      if(Auth::attempt(['name' => $request->username, 'password' => $request->password], true)){
        return response()->json(Auth::user(), 200);
      }
      else{
        return response()->json(['error' => 'Could not log you in'],401);
      }
    }

    public function logout(){
      Auth::logout();
    }

    public function register(Request $request){
      
      $user = User::where('name', $request->username)->first();

      if(isset($user->id)){
        return response()->json(['error' => 'Username already exists.'],401);
      }

      $user = new User();

      $user->name = $request->username;
      $user->email = $request->email;
      $user->password = bcrypt($request->password);
      $user->userType = $request->userType ? $request->userType : 2;
      $user->save();

      Auth::login($user);

      return response()->json($user,200);

    }

    public function getAllItems() {
      $items = Item::get()->toJson(JSON_PRETTY_PRINT);
      return response($items, 200);
    }

    public function getAllBids() {
      $bids = Bids::get()->toJson(JSON_PRETTY_PRINT);
      return response($bids, 200);
    }

    public function getBidsByUser($userId) {
      if (Bids::where('userId',$userId)->exists()){
        $bids = Bids::where('userId', $userId)->get()->toJson(JSON_PRETTY_PRINT);
        return response($bids, 200);
      } else {
        return response()->json(["message" => "No bids made by that user"], 404);
      }
    }

    public function getBidsByItem($itemId) {
      if (Bids::where('itemId',$itemId)->exists()){
        $bids = Bids::where('itemId', $itemId)->get()->toJson(JSON_PRETTY_PRINT);
        return response($bids, 200);
      } else {
        return response()->json(["message" => "No bids made for this item"], 404);
      }
    }

    public function getItem($id) {
      if (Item::where('id', $id)->exists()) {
        $item = Item::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
        return response($item, 200);
      } else {
        return response()->json(["message" => "Item not found"], 404);
      }
    }

    public function createBid(Request $request) {
      
      $bid = new Bids;
      $bid->bid = $request->bid;
      $bid->userId = $request->userId;
      $bid->itemId = $request->itemId;
      $bid->save();

      return response()->json(["message" => "Bid created successfully!"], 201);
    }

    public function createItem(Request $request) {

      $validatedData = $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'auctionDeadline' => 'required',
      ]);
      
      $item = new Item;
      $item->name = $request->name;
      $item->description = $request->description;
      $item->price = $request->price;
      $item->auctionDeadline = $request->auctionDeadline;
      $item->highestBid = 0;
      $item->biddingHistory = '';
      $item->save();

      return response()->json(["message" => "Item created successfully!"], 201);
    }

    public function updateBidsHistory(Request $request, $id) {
      if (Item::where('id', $id)->exists()) {
        $item = Item::find($id);
        if(empty($item->biddingHistory)){
          $item->biddingHistory = $request->biddingHistory;
        }
        else{
          $item->biddingHistory = $item->biddingHistory.', '.$request->biddingHistory;
        }
        $item->save();

        return response()->json(["message" => "Bid history updated"], 200);
      } else {
        return response()->json(["message" => "Item not found"], 404);
      }
    }

    public function updateItem(Request $request, $id) {
      if (Item::where('id', $id)->exists()) {

        $item = Item::find($id);
        $item->name = is_null($request->name) ? $item->name : $request->name;
        $item->description = is_null($request->description) ? $item->description : $request->description;
        $item->price = is_null($request->price) ? $item->price : $request->price;
        $item->auctionDeadline = is_null($request->auctionDeadline) ? $item->auctionDeadline : $request->auctionDeadline;
        $item->save();

        return response()->json(["message" => "Item updated successfully!"], 200);
      }
      else {
        return response()->json(["message" => "Item was not found!"], 404);
      }
    }

    public function makeBid(Request $request, $id) {
      if(Item::where('id', $id)->exists()){
        $bid = new Bids;
        $bid->bid = $request->bid;
        $bid->userId = $request->userId;
        $bid->itemId = $id;
        $bid->save();

        $data = collect(['userId' => $request->userId, 'bid' => $request->bid, 'bidId' => $bid->id ]);
        
        $item = Item::find($id);
        $item->highestBid = $data;
        $item->save();
        return response()->json(["message" => "Item highest bid updated successfully!"], 200);
      }
      else{
        return response()->json(["message" => "Item was not found!"], 404);
      }
    }

    public function deleteItem($id) {
      if(Item::where('id', $id)->exists()) {
        $item = Item::find($id);
        $item->delete();

        return response()->json(["message" => "Item deleted successfully!"], 202);
      } else {
        return response()->json(["message" => "Item not found"], 404);
      }
    }
}
