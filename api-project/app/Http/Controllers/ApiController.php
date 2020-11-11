<?php

namespace App\Http\Controllers;
use App\Models\Item;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getAllItems() {
      $items = Item::get()->toJson(JSON_PRETTY_PRINT);
      return response($items, 200);
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

    public function getItem($id) {
      if (Item::where('id', $id)->exists()) {
        $item = Item::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
        return response($item, 200);
      } else {
        return response()->json(["message" => "Item not found"], 404);
      }
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

    public function updateBid(Request $request, $id) {
      if(Item::where('id', $id)->exists()){
        $item = Item::find($id);
        $item->highestBid = $request->highestBid;
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
