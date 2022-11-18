<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
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

            $validator = Validator::make($data, [
                'name' => 'required',
                'email' => 'required|email',
                'document' => 'required|unique:clients|max:11|cpf',
                'city' => 'required',
                'state' => 'required|max:2',
                'start_date' => 'required|max:10|date_equals:date|date_format:Y-m-d',
                'user_id' => 'required'
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                return json_encode(
                    Array(
                        "error" => true,
                        "errors" => $errors->all()
                        )
                );
            }

            return json_encode(
                Array(
                        "success" => true,
                        "client" => ClientService::create($data)
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
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreClientRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClientRequest  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }
}
