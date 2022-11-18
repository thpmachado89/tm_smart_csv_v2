<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Jobs\ProcessFile;
use App\Models\Client;
use App\Models\Error;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ClientController;

class ProcessFileTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_process_data_valid()
    {

        $dataCSV = 'Nome Completo;email@email.com;694.546.400-89;Cidade;UF;05/08/2012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date
        ];

        ProcessFile::dispatch(
            Array(
                "data" => [$dataCSV],
                "indice" => 0,
                "upload_id" => 1,
                "user_id" => 1,
                "count" => 1,
                "count_parts" => 1
            )
        );

        $client = Client::find(1)->first(['name', 'email', 'document', 'city', 'state', 'start_date']);

        Log::info($dataInput);

        Log::info($client);

        $this->assertEquals(json_encode($dataInput), json_encode($client));

    }

    public function test_process_data_cpf_invalid()
    {
        

        $dataCSV = 'Nome Completo;email@email.com;88888888888;Cidade;UF;05/08/2012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date,
            "user_id" => 1
        ];

        $error = ["error"=>true,"errors"=>["O campo document não é um CPF válido."]];

        $clientController = new ClientController();
        $result = $clientController->create($dataInput);

        $this->assertEquals($result, json_encode($error));

    }

    public function test_process_data_cpf_already_save()
    {
        

        $dataCSV = 'Nome Completo;email@email.com;694.546.400-89;Cidade;UF;05/08/2012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date,
            "user_id" => 1
        ];

        $error = ["error"=>true,"errors"=>["The document has already been taken."]];

        $clientController = new ClientController();
        $result = $clientController->create($dataInput);

        $this->assertEquals($result, json_encode($error));

    }

    public function test_process_data_cpf_missing()
    {
        

        $dataCSV = 'Nome Completo;email@email.com;;Cidade;UF;05/08/2012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date,
            "user_id" => 1
        ];

        $error = ["error"=>true,"errors"=>["The document field is required."]];

        $clientController = new ClientController();
        $result = $clientController->create($dataInput);

        $this->assertEquals($result, json_encode($error));

    }

    public function test_process_data_name_missing()
    {
        

        $dataCSV = ';email@email.com;520.175.840-10;Cidade;UF;05/08/2012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date,
            "user_id" => 1
        ];

        $error = ["error"=>true,"errors"=>["The name field is required."]];

        $clientController = new ClientController();
        $result = $clientController->create($dataInput);

        $this->assertEquals($result, json_encode($error));

    }

    public function test_process_data_start_date_invalid()
    {
        

        $dataCSV = 'Nome Completo;email@email.com;520.175.840-10;Cidade;UF;0/8/32012';

        $dataArray = explode(";", $dataCSV);

        $start_date_arr = explode("/", $dataArray[5]);
        $start_date = $start_date_arr[2]."-".$start_date_arr[1]."-".$start_date_arr[0];

        $dataInput = [
            "name" => $dataArray[0],
            "email" => $dataArray[1],
            "document" => preg_replace('/[^0-9]/', '', $dataArray[2]),
            "city" => $dataArray[3],
            "state" => $dataArray[4],
            "start_date" => $start_date,
            "user_id" => 1
        ];

        $error = ["error"=>true,"errors"=>["The start date does not match the format Y-m-d."]];

        $clientController = new ClientController();
        $result = $clientController->create($dataInput);

        $this->assertEquals($result, json_encode($error));

    }
}
