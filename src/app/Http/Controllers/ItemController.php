<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with('purchase');
        if (Auth::check()){
            $query->where('seller_id', '!=', Auth::id()); 
        }
        $items = $query->get();

        return view('index', compact('items'));
    }

    public function show($item_id)
    {
        $item = Item::with(['likes', 'comments', 'seller', 'purchase'])->findOrFail($item_id);

        return view('item_detail', compact('item'));
    }
}
