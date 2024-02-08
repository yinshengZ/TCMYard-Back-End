<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Exception;

class FileController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required'
        ]);

        $file = new File;

        $original_name = str_replace(" ", "_", $request->file('file')->getClientOriginalName());
        $file_name = time() . '_' . $original_name;
        try {
            $file_path = $request->file('file')->storeAs('patient_files/' . $request->patient_id, $file_name, 'public');
        } catch (Exception $err) {
            return $err;
        }

        $file->file_name = $file_name;
        $file->file_path = '/storage/' . $file_path;
        $file->file_type = $request->file('file')->getMimeType();
        $file->original_name = $request->file('file')->getClientOriginalName();
        $file->patient_id = $request->patient_id;
        $file->user_id = $request->user_id;
        $file->description = $request->description;
        $file->save();

        return response()->json([
            'data' => "File has been uploaded successfully!",
            'code' => 200
        ]);
    }

    /**
     * Update patient files
     * @param Request $request
     * @return json response of updating result
     */
    public function update_patient_file(Request $request)
    {
        $file = File::findOrFail($request->id);

        if ($file) {
            $file->description = $request->description;
            $file->save();

            return response()->json([
                'data' => "File has been updated!",
                'code' => 200
            ]);
        };
    }


    /**
     * Display the specific patient's files
     * @param int $id
     * @return json object of file info 
     */
    public function get_patient_files($id)
    {
        $file_details = [];
        $files = File::where('patient_id', $id)->latest()->with('user')->get();
        foreach ($files as $index => $file) {
            $file_detail = [];
            $file_detail['id'] = $file->id;
            $file_detail['file_name'] =  $file->original_name;
            $file_detail['url'] = url($file->file_path);
            $file_detail['description'] = $file->description;
            $file_detail['file_type'] = $file->file_type;
            $file_detail['created_at'] = $file->created_at;
            $file_detail['uploaded_by'] = $file->user['username'];
            array_push($file_details, $file_detail);
        }
        return response()->json([
            'data' => $file_details,
            'code' => 200
        ]);
    }

    public function get_file($id)
    {
        $file_info = File::where('id', $id)->with('user')->get();

        return response()->json([
            'data' => $file_info,
            'code' => 200
        ]);
    }








    /**
     * Delete the file requested by user
     *
     * @param  int  $id
     * @return json response of deletion result
     */
    public function destroy($id)
    {
        $file = File::findOrFail($id);
        $file_path = '/patient_files' . '/' . $file->patient_id . '/' . $file->file_name;
        if (Storage::disk('public')->exists($file_path)) {
            Storage::disk('public')->delete($file_path);
        } else {


            return "There's no such file on the system!";
        }
        $file->delete();

        return response()->json([
            'data' => 'File Has Been Deleted Successfully!',
            'code' => 200
        ]);
    }
}
