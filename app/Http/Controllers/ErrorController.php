<?php

namespace App\Http\Controllers;

use App\Services\ErrorService;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($data)
    {
        try {
            
            return json_encode(
                Array(
                        "success" => true,
                        "client" => ErrorService::create($data)
                )
            );

        } catch(Exception $e) {
            
            return Array(
                "error" => true,
                "message" => $e->getMessage(),
            );
            
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreErrorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreErrorRequest $request)
    {
        //
    }
    
    public function show($id)
    {
        try {
            
            return json_encode(
                Array(
                        "success" => true,
                        "errors" => ErrorService::getErrorsByUploadId($id)
                )
            );

        } catch(Exception $e) {
            
            return Array(
                "error" => true,
                "message" => $e->getMessage(),
            );
            
        }   
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Error  $error
     * @return \Illuminate\Http\Response
     */
    public function edit(Error $error)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateErrorRequest  $request
     * @param  \App\Models\Error  $error
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateErrorRequest $request, Error $error)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Error  $error
     * @return \Illuminate\Http\Response
     */
    public function destroy(Error $error)
    {
        //
    }
}
