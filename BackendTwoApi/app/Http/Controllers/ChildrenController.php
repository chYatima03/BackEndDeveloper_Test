<?php

namespace App\Http\Controllers;

use App\Models\Childrens;
use App\Models\Parents;
use App\Resources\ChildrensResource;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ChildrenController extends Controller
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
        return $this->successResponse(ChildrensResource::collection(Childrens::all()), Response::HTTP_OK);
    }
    public function show(Request $request, $children)
    {

        $children_ = Childrens::query()
            ->where('name', 'like', '%' . $request->segment(2) . '%')
            ->orWhere('id', '=', $children)->get();

        return $this->successResponse(ChildrensResource::collection($children_), Response::HTTP_OK);

    }
    public function store(Request $request)
    {
        $rules = [
            'parent_id' => 'required|max:10',
            'name' => 'required|max:50',
            'route' => 'required|max:150',
        ];
        $this->validate($request, $rules);
        $parent = Parents::find($request->parent_id);
        // $parent = Parents::where('id','=',$request->parent_id)->get();
        if($parent)
        {
            $input_children = $request->all();
            $children_ = Childrens::create($input_children);
            $this->addLog('', $input_children);
            return $this->successResponse($children_, Response::HTTP_CREATED);

        }else{
            return $this->errorResponse('ID Parent not found ', Response::HTTP_NOT_FOUND);
        }



    }
    public function update(Request $request, $children)
    {
        $rules = [
            'parent_id' => 'required|max:10',
            'name' => 'required|max:50',
            'route' => 'required|max:150',
        ];
        $this->validate($request, $rules);
        if (Childrens::find($children)) {
            $children_ = Childrens::findOrFail($children);
            $children_old = [
                'parent_id' => $children_['parent_id'],
                'name' => $children_['name'],
                'route' => $children_['route'],
            ];
            $children_->fill($request->all());

            if ($children_->isClean()) {
                return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $children_->update();
            // หาข้อมูลที่แตกต่าง
            $children_new = $request->all();
            $result_new = array_diff($children_new, $children_old); //ข้อมูลใหม่
            $result_old = array_diff($children_old, $result_new); //ข้อมูลเก่า
            $this->addLog($result_old, $result_new);
            return $this->showResponse($children_);
        }
        return $this->successResponse([
            'message' => 'not have id',
        ]
        );
    }
    public function destroy($children)
    {

        if (Childrens::find($children)) {
            $children = Childrens::findOrFail($children);
            $children_del = $children;
            $children_old = [
                'parent_id' => $children_del['parent_id'],
                'name' => $children_del['name'],
                'route' => $children_del['route'],
            ];

            $children->delete();
            $this->addLog($children_old, '');
            return $this->successResponse([
                'message' => 'Delete is Successfully',
            ], Response::HTTP_OK);

        } else {
            return $this->successResponse([
                'message' => 'not have id',
            ], Response::HTTP_NOT_FOUND);
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
        $insertData = DB::connection('mongodb')->collection('data_log_children')->insert($data);
    }
    //
}
