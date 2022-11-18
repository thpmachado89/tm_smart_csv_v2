<?php

namespace App\Services;

use App\Models\Error;
use Illuminate\Support\Facades\Log;

class ErrorService
{

    public static function create($data)
    {   
        Log::info($data);
        Error::create($data);
    }

    public static function getErrorsByUploadId($id)
    {
        return Error::where('upload_id', $id)->get();
    }

}