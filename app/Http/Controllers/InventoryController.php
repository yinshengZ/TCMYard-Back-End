<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventory = Inventory::with('category')->get();
        return response()->json([
            'data'=> $inventory,
            'code'=>200
        ]);
    }

    /*    public function get_all_retail_products(){
        $inventory = Inventory::where('categories_id','2')->with('category')->get();
        return $inventory;
    }
 */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, array(
            'name' => 'required|unique:App\Models\Inventory|max:255',
            'eng_name' => 'required|unique:App\Models\Inventory|max:255',
            'stock' => 'required|max:999999999|numeric',
            'unit_price' => 'required|max:999999|numeric',
            'description' => 'string',
            'categories_id' => 'integer',
            'expiry_date' => 'nullable|date'
        ));

        $inventory = new Inventory;
        $inventory->name = $request->name;
        $inventory->eng_name = $request->eng_name;
        $inventory->stock = $request->stock;
        $inventory->unit_price = $request->unit_price;
        $inventory->description = $request->description;
        $inventory->categories_id = $request->categories_id;
        $inventory->expiry_date = $request->expiry_date;

        $inventory->save();
        return response()->json([
            'data'=> 'Inventory has been added!',
            'code'=>200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $inventory = Inventory::with('category')->find($id);
        return response()->json([
            'data'=> $inventory,
            'code'=>200
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, array(
            'name' => 'required|max:255',
            'eng_name' => 'required|max:255',
            'stock' => 'required|max:9999999999999|numeric',
            'unit_price' => 'required|max:99999999|numeric',
            'description' => 'string',
            'categories_id' => 'integer'
        ));

        $inventory = Inventory::find($id);
        $inventory->name = $request->name;
        $inventory->eng_name = $request->eng_name;
        $inventory->stock = $request->stock;
        $inventory->unit_price = $request->unit_price;
        $inventory->description = $request->description;
        $inventory->categories_id = $request->categories_id;
        $inventory->expiry_date = $request->expiry_date;
        $inventory->save();

        return response()->json([
            'data'=> 'Inventory has been updated!',
            'code'=>200
        ]);
    }

    public function get_inventory_by_category($categories)
    {
        $categories_id = Category::where('categories', $categories)->get()->pluck('id');
        $inventory = Inventory::whereIn('categories_id', $categories_id)->get();
        return response()->json([
            'data'=> $inventory,
            'code'=>200
        ]);
    }

    public function get_herbs()
    {
        $herbs = Inventory::where('categories_id', 1)->get();
        return response()->json([
            'data'=> $herbs,
            'code'=>200
        ]);
    }

    public function get_services()
    {
        $services = Inventory::where('categories_id', 2)->get();
        return response()->json([
            'data'=> $services,
            'code'=>200
        ]);
    }

    public function get_retails()
    {
        $retails = Inventory::where('categories_id', 3)->get();
        return response()->json([
            'data'=> $retails,
            'code'=>200
        ]);
    }

    public function get_others()
    {
        $others = Inventory::where('categories_id', '>=', '4')->get();
        return response()->json([
            'data'=> $others,
            'code'=>200
        ]);
    }

    public function get_expirying()
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $inventory = Inventory::where('id', $id)->delete();
        return response()->json([
            'data'=>"Inventory has been deleted!",
            'code'=>200
        ]);
    }
}
