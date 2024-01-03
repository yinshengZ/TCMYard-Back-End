<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $service = Service::all();
        return response()->json([
            'data'=> $service,
            'code'=>200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'service_title' => 'required',
            'unit_price' => 'required'
        ));

        $service = new Service;
        $service->service_title = $request->service_title;
        $service->description = $request->description;
        $service->unit_price = $request->unit_price;
        $service->save();

        return response()->json([
            'data'=> 'Service has been added!',
            'code'=>200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::find($id);
        return response()->json([
            'data'=> $service,
            'code'=>200
        ]);
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
        $this->validate($request, array(
            'service_title' => 'required',
            'unit_price' => 'required|max:999999|numeric',
            'description' => 'required|string'
        ));

        $service = Service::find($id);
        $service->service_title = $request->service_title;
        $service->unit_price = $request->unit_price;
        $service->description = $request->description;
        $service->save();
        return response()->json([
            'data'=> 'Service has been updated!',
            'code'=>200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
