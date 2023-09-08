<?php

namespace App\Http\Controllers;

use App\Models\Beers;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BeersController extends Controller
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
        $beers = Beers::with(['types' => function ($query) {
            $query->select(['id', 'type_name']);
        }])->cursorPaginate(10);

        return $this->showResponse($beers);
    }
    public function show(Request $request, $name)
    {
        // $request->segment(2) กรณีเป็นภาษาไทย มันอ่านค่าไม่ได้
        $beer = Beers::where('beer_name', 'like', '%' . $request->segment(2) . '%')->with(['types' => function ($query) {
            $query->select(['id', 'type_name']);
        }])->cursorPaginate(10);
        return $this->showResponse($beer);
    }
    public function store(Request $request)
    {
        $rules = [
            'type_beer_id' => 'required|max:10',
            'beer_name' => 'required|max:50',
            'beer_image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'beer_detail' => 'required|max:300',
        ];

        $this->validate($request, $rules);
        $input_beer = $request->all();
        if ($request->hasFile('beer_image')) {

            $beer_image = $request->file('beer_image')->getClientOriginalName();
            $fileName = 'beer_' . time() . '_' . $beer_image;
            $path = base_path('public/uploads/images/beer/');
            if ($request->beer_image->move($path, $fileName)) {
                $input_beer['beer_image'] = $fileName;
            }

        }
        $beer = Beers::create($input_beer);
        $beer->save();
        $this->addLog('', $input_beer);
        return $this->successResponse($beer, Response::HTTP_CREATED);
    }
    public function update(Request $request, $id)
    {
        $rules = [
            'type_beer_id' => 'required|max:10',
            'beer_name' => 'required|max:50',
            'beer_image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'beer_detail' => 'required|max:300',
        ];

        $this->validate($request, $rules);
        if (Beers::find($id)) {
            $beer = Beers::findOrFail($id);

            $image_one = $beer['beer_image'];

            $beer_old = [
                'type_beer_id' => $beer['type_beer_id'],
                'beer_name' => $beer['beer_name'],
                'beer_image' => $beer['beer_image'],
                'beer_detail' => $beer['beer_detail'],

            ];
            $beer->fill($request->all());

            if ($beer->isClean()) {
                return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($request->hasFile('beer_image')) {
                $file_path = base_path('public/uploads/images/beer/');
                $file_path = $file_path . $image_one;

                if (File::exists($file_path)) {
                    unlink($file_path); //delete from storage
                    // Storage::delete($file_path); //Or you can do it as well
                }

                $beer_image = $request->file('beer_image')->getClientOriginalName();

                $fileName = 'beer_' . time() . '_' . $beer_image;
                $path = base_path('public/uploads/images/beer/');
                if ($request->beer_image->move($path, $fileName)) {
                    $beer->beer_image = $fileName;
                }
                $beer->update();
                // หาข้อมูลที่แตกต่าง
                $beer_new = [
                    'type_beer_id' => $beer->type_beer_id,
                    'beer_name' => $beer->beer_name,
                    'beer_image' => $beer->beer_image,
                    'beer_detail' => $beer->beer_detail,
                ];
                $result_new = array_diff($beer_new, $beer_old); //ข้อมูลใหม่
                $result_old = array_diff($beer_old, $result_new); //ข้อมูลเก่า
                $this->addLog($result_old, $result_new);
            }
            return $this->successResponse($beer);
        }
        return $this->successResponse([
            'message' => 'not have id',
        ]
        );
    }
    public function destroy($id)
    {

        if (Beers::find($id)) {
            $beer = Beers::findOrFail($id);
            $beer_ = $beer;
            $beer_old = [
                'id' => $beer_['id'],
                'type_beer_id' => $beer_['type_beer_id'],
                'beer_name' => $beer_['beer_name'],
                'beer_image' => $beer_['beer_image'],
                'beer_detail' => $beer_['beer_detail'],
            ];

            $file_path = base_path('public/uploads/images/beer/');
            $file_path = $file_path . $beer['beer_image'];

            //You can also check existance of the file in storage.
            if (File::exists($file_path)) {
                unlink($file_path); //delete from storage
                // Storage::delete($file_path); //Or you can do it as well
            }

            $beer->delete();
            $this->addLog($beer_old, '');
            return $this->successResponse([
                'message' => 'Delete is Successfuly',
            ]);
        } else {
            return $this->successResponse([
                'message' => 'not have id',
            ]
            );
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
        $insertData = DB::connection('mongodb')->collection('data_log_beer')->insert($data);
    }

    //
}
