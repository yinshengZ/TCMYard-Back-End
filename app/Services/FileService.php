<?php
namespace App\Services;

use DB;

use App\Models\File;



class FileService {

    public static function processFileList($patient_id){

        $files = File::select(DB::raw('group_concat(id) as file_id'),
        DB::raw('group_concat(file_name) as file_name'),    
        DB::raw('group_concat(file_path) as file_path'),
        DB::raw('group_concat(original_name) as original_name'),'description',
        DB::raw('group_concat(patient_id) as patient_id'),
        DB::raw('group_concat(user_id) as user_id'),
         'created_at')
        ->where('patient_id',$patient_id)
        ->groupBy('created_at')
        ->latest()     
        ->with('user')
        ->get();
/* 
        $file_info = [];
        $final_files_info = [];
        if(sizeof($files)>0){
            
            foreach ($files as $index=>$file){
                $files_id = explode(',',$file->file_id);
                $files_name = explode(',',$file->file_name);
                $files_path = explode(',',$file->file_path);
                $files_original_name= explode(',',$file->original_name);

                foreach ($files_id as $id_index=>$file_id){
                    $file_info['file_id'] = $file_id[$id_index];
                    $file_info['files_name']=$files_name[$id_index];
                } */

/* 
                $file_info['file_id'] = $files_id[$index];
                $file_info['file_name']=$files_name[$index];
                $file_info['file_path']=$files_path[$index];
                $file_info['original_name']=$files_original_name[$index];

                array_push($final_files_info,$file_info); */

                return $files;
                
            }
        }
        








?>