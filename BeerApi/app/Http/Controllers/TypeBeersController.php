<?php

namespace App\Http\Controllers;

use App\Models\Typebeers;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TypeBeersController extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public function index()
    {
        $type_beer = Typebeers::orderBy('id')->cursorPaginate(10);
        // $type_beer = Typebeers::all();
        // $this->addLog($type_beer);
        return $this->showResponse(($type_beer));
    }
    public function show($type)
    {
        $type_beer = Typebeers::find($type);
        return $this->showResponse($type_beer);

    }
    public function store(Request $request)
    {
        $rules = [
            'type_name' => 'required|max:50',
        ];

        $this->validate($request, $rules);

        $type_beer = Typebeers::create($request->all());
        return $this->successResponse($type_beer, Response::HTTP_CREATED);
    }
    public function update(Request $request, $type)
    {
        $rules = [
            'type_name' => 'required|max:50',
        ];
        $this->validate($request, $rules);
        $type_beer = Typebeers::findOrFail($type);
        $type_beer_old = [
            'type_name' => $type_beer['type_name'],
        ];
        $type_beer->fill($request->all());

        if ($type_beer->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $type_beer->save();
        $type_beer_new = [
            'type_name' => $type_beer->type_name,
        ];
        $result_new = array_diff($type_beer_new, $type_beer_old);
        $result_old = array_diff($type_beer_old, $result_new);
        $this->addLog($result_old, $result_new);
        // $this->addLog($request->all());

        return $this->successResponse($type_beer);
    }
    public function destroy($type)
    {
        $type_beer = Typebeers::findOrFail($type);

        $type_beer->delete();
        $this->addLog('','');
        $this->successResponse($type_beer);
    }
    private function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
    private function addLog($data_before,$data_log)
    {
        $database = DB::connection()->getDatabaseName();
        // $tableprefix = DB::connection()->getSchemaBuilder(col);
        // $data = array('log_action' => 'get');
        $method = $_SERVER['REQUEST_METHOD'];
        $actual_link = "$_SERVER[REQUEST_URI]";
        $ip_client = $this->get_client_ip();
        // $d=strtotime("today");
        $datetime = date('Y/m/d H:i:s');
        // $d = array('dadad','ss');
        $data = [
            // "log_user" => '',
            "log_action" => $method,
            "log_url" => $actual_link,
            "log_db" => $database,
            "log_data_before" => $data_before,
            "log_data_new" => $data_log,
            "log_ip" => $ip_client,
            "log_date" => $datetime,

        ];
        $insertData = DB::connection('mongodb')->collection('data_log_type')->insert($data);
    }

    //
}
