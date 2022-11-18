<?php

namespace App\Services;

use App\Models\Upload;
use App\Jobs\ProcessFile;
use Illuminate\Support\Facades\Log;

class UploadService
{

    public function uploadFile($request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileName = $this->saveFile($request->file('file'));
            if(file_exists( public_path().'/uploads/'.$fileName )){
                $uploadId = $this->saveUpload($fileName, "Aguardando", $request->input('user_id'));
                $this->startJobProcess($uploadId);
                return $uploadId;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function startJobProcess($uploadId)
    {
        $upload = Upload::find($uploadId);
        $upload->status = "Processando";
        $upload->save();
        $user_id = $upload->user_id;
        $upload_id = $upload->id;
        $filepath = public_path().'/uploads/'.$upload->filename;
        if(file_exists( $filepath )){
            $csv_array = file( $filepath );
            $csv_array = array_slice($csv_array, 1);
            $csv_array = array_map("utf8_encode", $csv_array );
            //Log::info($csv_array);
            Log::info(count($csv_array));
            $parts = array_chunk($csv_array, 100);
            $i = 0;
            foreach($parts as $part){
                ProcessFile::dispatch(
                    Array(
                        "data" => $part,
                        "indice" => $i,
                        "upload_id" => $upload_id,
                        "user_id" => $user_id,
                        "count" => count($csv_array),
                        "count_parts" => count($parts)
                    )
                );
                $i++;
            }
        }
        //ProcessFile::dispatch($uploadId);
    }

    private function saveFile($file)
    {
        $fileName = time().'.'.$file->getClientOriginalExtension();
        $destinationPath = 'uploads';
        $file->move($destinationPath, $fileName);
        return $fileName;
    }

    private function saveUpload($fileName, $status, $userId)
    {
        $upload = new Upload();
        $upload->filename = $fileName;
        $upload->status = $status;
        $upload->user_id = $userId;
        $upload->save();
        $uploadId = $upload->id;
        return $uploadId;
    }

}