<?php

namespace App\Services;

use App\Models\Client;
use App\Jobs\ProcessFile;
use Illuminate\Support\Facades\Log;

class ClientService
{

    public static function create($data)
    {   
        Log::info($data);
        Client::create($data);
    }

}