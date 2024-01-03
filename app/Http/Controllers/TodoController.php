<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TODO;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    public function user_todo_list($user_id)
    {
        $user_todo = TODO::where('user_id', $user_id)->where('status', '!=', 1)->orderBy('finish_date')->paginate(3);
        return response()->json([
            'data' => $user_todo,
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
        $todo = new TODO;
        $todo->content = $request->task;
        $todo->finish_date = $request->deadline;
        $todo->status = 0;
        $todo->user_id = $request->user_id;
        $todo->save();
        return response()->json([
            'data' => 'Task has been aded!',
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
        //
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
        $todo = TODO::find($id);
        $todo->status = $request->todo_status;
        $todo->save();
        return response()->json([
            'data' => 'Task has been updated!',
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
        $todo = TODO::find($id);
        $todo->delete();
        return response()->json([
            'data' => 'Task has been deleted!',
            'code' => 200
        ]);
    }
}
