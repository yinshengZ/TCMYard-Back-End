<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * @return all payment methods
     */
    public function index(){
        $payment_methods = PaymentMethod::select('id','payment_type')->get();
        return response()->json([
            'data'=> $payment_methods,
            'code'=>200
        ]);
    }

    public function store(Request $request){
        $payment_method = new PaymentMethod;
        $payment_method->payment_type = $request->method;
        $payment_method->save();

       return  response()->json([
        'data'=>'Payment Method Has Been Added!',
        'code'=>200
       ]);
    }
}
