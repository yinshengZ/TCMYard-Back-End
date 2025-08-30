<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Record;
use App\Models\Patient;
use App\Models\User;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Record::with('patient:id,first_name,last_name')->with('user:id,nickname')->get();

        return response()->json([
            'data' => $records,
            'code' => 200
        ]);
    }

    public function patient_records($patient_id)
    {
        $records = Record::where('patient_id', $patient_id)->with('user')->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'data' => $records,
            'code' => 200
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
            'record_body' => 'required',
            'patient_id' => 'required',
            'user_id' => 'required'
        ));

        $record = new Record;
        $record->record_body = $request->record_body;
        $record->patient_id = $request->patient_id;
        $record->user_id = $request->user_id;
        $record->save();

        return response()->json([
            'data' => 'Record has been added!',
            'code' => 200
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
        $records = Record::where('id', $id)->first();
        return response()->json([
            'data' => $records,
            'code' => 200
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
            'record_body' => 'required',
            //'patient_id'=>'required',
            'user_id' => 'required'

        ));

        $record = Record::find($id);
        $record->record_body = $request->record_body;
        $record->user_id = $request->user_id;
        $record->save();

        return response()->json([
            'data' => 'Record has been updated!',
            'code' => 200
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
        $record = Record::find($id);
        $record->delete();
        return response()->json([
            'data' => 'Record has been deleted!',
            'code' => 200
        ]);
    }
}
