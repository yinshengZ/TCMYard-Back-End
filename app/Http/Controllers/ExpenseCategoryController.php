<?php

namespace App\Http\Controllers;
use App\Models\ExpenseCategory;

use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{

    public function index(){
   
        $categories = ExpenseCategory::get();
        return response()->json([
            'data'=>$categories,
            'code'=>200
        ]);
    }

    public function store(Request $request){
        
        /* return $request; */

        $this->validate($request,array(
            'category'=>'required|unique:expense_categories|'
        ));

        $category = new ExpenseCategory;
        $category->category = $request->category;
        $category->save();

        return response()->json([
            'data'=>'Category has been added!',
            'code'=>200
        ]);

        
    }

    public function update(){

    }

    public function destory($id){
        
    }

}
