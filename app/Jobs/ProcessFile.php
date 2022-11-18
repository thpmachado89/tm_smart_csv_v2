<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Upload;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ErrorController;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\FileProcessed;
use App\Models\Error;

class ProcessFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $data = $this->params["data"];
        $i = 0;
        foreach($data as $line => $value){
            $i++;
            $this->processData($value, $this->params["indice"], $i, $this->params["upload_id"], $this->params["user_id"]);
        }

        $indice_verify = $this->params["indice"] + 1;

        if($this->params["count_parts"] == $indice_verify){
            $errors = Error::where('upload_id', $this->params["upload_id"])->count();
            event(
                new FileProcessed(
                    Array(
                        "upload_id" => $this->params["upload_id"],
                        "finished" => true,
                        "errors" => $errors,
                        "count" => $this->params["count"],
                    )
                )
            );
        }
    }

    private function processData($data, $indice, $i, $upload_id, $user_id)
    {
        $data_array = explode(";", $data);
        $data_array[] = $user_id;
        $array_to_save = $this->sanitizeArray($data_array);
        Log::info($data_array);
        $resultJson = $this->saveClient($array_to_save);
        if($resultJson){
            $result = json_decode($resultJson, true);
            if(@$result['errors']){
                $errors = $result['errors'];
                if(is_array($errors)){
                    if(count($errors) > 0){
                        $i2 = 0;
                        foreach($errors as $error){
                            $this->saveError($error, $i+($indice*100), $upload_id);
                        }
                    }
                }
            }
        }
    }

    private function saveError($error, $i, $uploadId)
    {
        $errorController = new ErrorController();
        $errorController->create(
            Array(
                "message" => $error,
                "line" => $i,
                "upload_id" => $uploadId
            )
        );
    }

    private function saveClient($array_to_save)
    {
        $clientController = new ClientController();
        return $clientController->create($array_to_save);
    }

    private function sanitizeArray($row)
    {   
        
        Log::info($row);
        $name = $row[0];
        $email = $row[1];
        $document = $row[2];
        $document = preg_replace('/[^0-9]/', '', $document);
        $city = $row[3];
        $state = $row[4];
        $start_date = substr($row[5], 0, 10);
        if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/", $start_date)) {
            $start_date = Carbon::createFromFormat('d/m/Y', substr($start_date, 0, 10))->format('Y-m-d');
        } else {
            $start_date = "";
        }
       
        $user_id = $row[6];

        return Array(
            "name" => $name,
            "email" => $email,
            "document" => $document,
            "city" => $city,
            "state" => $state,
            "start_date" => $start_date,
            "user_id" => $user_id,
        );

    }
}
