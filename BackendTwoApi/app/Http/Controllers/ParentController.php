<?php

namespace App\Http\Controllers;

use App\Models\Childrens;
use App\Models\Parents;
// use App\Models\Childrens;
use App\Resources\ParentsResource;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
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
        return $this->successResponse(ParentsResource::collection(Parents::all()), Response::HTTP_OK);
    }

    public function show(Request $request, $parent)
    {

        $parent_ = Parents::query()
            ->where('name', 'like', '%' . $request->segment(2) . '%')
            ->orWhere('id', '=', $parent)->get();

        return $this->successResponse(ParentsResource::collection($parent_), Response::HTTP_OK);

    }
    public function store(Request $request)
    {
        $rules = [
            'icon' => 'required|max:100',
            'name' => 'required|max:50',
            'route' => 'required|max:50',
            'is_children' => 'required',
        ];
        $this->validate($request, $rules);
        $input_parent = $request->all();
        $parent_ = Parents::create($input_parent);
        // $parent_->save();
        $this->addLog('', $input_parent);
        return $this->successResponse($parent_, Response::HTTP_CREATED);
    }
    public function update(Request $request, $parent)
    {
        $rules = [
            'icon' => 'required|max:100',
            'name' => 'required|max:50',
            'route' => 'required|max:50',
            'is_children' => 'required|boolean',
        ];
        $this->validate($request, $rules);
        if (Parents::find($parent)) {
            $parent_ = Parents::findOrFail($parent);
            $parent_old = [
                'icon' => $parent_['icon'],
                'name' => $parent_['name'],
                'route' => $parent_['route'],
                'is_children' => $parent_['is_children'],
            ];
            $parent_->fill($request->all());

            if ($parent_->isClean()) {
                return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $parent_->update();
            // หาข้อมูลที่แตกต่าง
            $parent_new = $request->all();
            $result_new = array_diff($parent_new, $parent_old); //ข้อมูลใหม่
            $result_old = array_diff($parent_old, $result_new); //ข้อมูลเก่า
            $this->addLog($result_old, $result_new);
            return $this->showResponse($parent_);
        }
        return $this->successResponse([
            'message' => 'not have id',
        ]
            , Response::HTTP_NOT_FOUND);
    }
    public function destroy($parent)
    {

        if (Parents::find($parent)) {
            $parent_id = $parent;
            $parent = Parents::findOrFail($parent);
            $parent_ = $parent;

            $parent_old = [
                'id' => $parent_['id'],
                'icon' => $parent_['icon'],
                'name' => $parent_['name'],
                'route' => $parent_['route'],
                'is_children' => $parent_['is_children'],
            ];

            $this->addLog($parent_old, '');

            $parent_ch = Childrens::where('parent_id', '=', $parent_id)->get();
            for ($i = 1; count($parent_ch) > $i; $i++) {
                $children_old = [
                    //     'd'=>$parent_ch
                    'id' => $parent_ch[$i]['id'],
                    'parent_id' => $parent_ch[$i]['parent_id'],
                    'name' => $parent_ch[$i]['name'],
                    'route' => $parent_ch[$i]['route'],
                ];
                $this->addLog_ch($children_old, '');
            }

            $parent->delete();

            return $this->successResponse([
                'message' => 'Delete is Successfully',
            ], Response::HTTP_OK);

        } else {
            return $this->successResponse([
                'message' => 'not have id',
            ]
                , Response::HTTP_NOT_FOUND);
        }

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
    private function addLog($data_before, $data_log)
    {
        $database = DB::connection()->getDatabaseName();
        $method = $_SERVER['REQUEST_METHOD'];
        $actual_link = "$_SERVER[REQUEST_URI]";
        $ip_client = $this->get_client_ip();
        $datetime = date('Y/m/d H:i:s');
        $data = [
            "log_action" => $method,
            "log_url" => $actual_link,
            "log_db" => $database,
            "log_data_before" => $data_before,
            "log_data_new" => $data_log,
            "log_ip" => $ip_client,
            "log_date" => $datetime,
        ];
        $insertData = DB::connection('mongodb')->collection('data_log_parent')->insert($data);
    }
    private function addLog_ch($data_before, $data_log)
    {
        $database = DB::connection()->getDatabaseName();
        $method = $_SERVER['REQUEST_METHOD'];
        $actual_link = "$_SERVER[REQUEST_URI]";
        $ip_client = $this->get_client_ip();
        $datetime = date('Y/m/d H:i:s');
        $data = [
            "log_action" => $method,
            "log_url" => $actual_link,
            "log_db" => $database,
            "log_data_before" => $data_before,
            "log_data_new" => $data_log,
            "log_ip" => $ip_client,
            "log_date" => $datetime,
        ];
        $insertData = DB::connection('mongodb')->collection('data_log_children')->insert($data);
    }
}
