<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Stock;

use Validator;
use Storage;
use DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::all();
        $items = DB::table('item')->join('stock', 'item.item_id', '=', 'stock.item_id')->get();
        return view('item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->description);
        $rules = [
            'img_path' => 'mimes:jpg,bmp,png',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $item = new Item();
        $item->description = $request->description;
        $item->sell_price = $request->sell_price;
        $item->cost_price = $request->cost_price;

        $name = $request->file('img_path')->getClientOriginalName();
        $extension = $request->file('img_path')->getClientOriginalExtension();

        $path = Storage::putFileAs(
            'public/items/images',
            $request->file('img_path'),
            $name
        );
        $item->img_path = 'storage/items/images/' . $name;
        $item->save();

        $stock = new Stock();
        $stock->item_id = $item->item_id;
        $stock->quantity = $request->quantity;
        $stock->save();
        return redirect()->route('items.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = DB::table('item')->join('stock', 'item.item_id', '=', 'stock.item_id')->where('item.item_id', $id)->first();
        // dd($items);
        return view('item.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'img_path' => 'mimes:jpg,bmp,png',

        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $item = Item::find($id);
        $item->description = $request->description;
        $item->sell_price = $request->sell_price;
        $item->cost_price = $request->cost_price;

        $name = $request->file('img_path')->getClientOriginalName();


        $path = Storage::putFileAs(
            'public/items/images',
            $request->file('img_path'),
            $name
        );
        $item->img_path = 'storage/items/images/' . $name;
        $item->save();

        $stock = Stock::find($id);

        $stock->quantity = $request->quantity;
        $stock->save();
        return redirect()->route('items.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::destroy($id);
        Stock::destroy($id);
        return redirect()->route('items.index');
    }

    public function getItems()
    {
        $items = DB::table('item')->join('stock', 'item.item_id', '=', 'stock.item_id')->get();
        return view('shop.index', compact('items'));
    }

    public function addToCart($id)
    {
        $item = Item::find($id);
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        dd($oldCart);
        $cart = new Cart($oldCart);
        // dd($cart);
        $cart->add($item, $item->item_id);

        Session::put('cart', $cart);
        // dd(Session::get('cart'));
        // $request->session()->save();
        Session::save();
        // dd(Session::get('cart'));

        return redirect('/');
    }
}
