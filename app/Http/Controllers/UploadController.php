<?php

namespace App\Http\Controllers;

use App\Services\UploadService;
use App\Http\Requests\StoreUploadRequest;

class UploadController extends Controller
{

    protected $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function create(StoreUploadRequest $request)
    {
        try {

            return json_encode(
                Array(
                        "success" => true,
                        "upload_id" => $this->uploadService->uploadFile($request)
                )
            );

        } catch(Exception $e) {

            return Array(
                "error" => true,
                "message" => $e->getMessage(),
            );

        }
        
    }
}
