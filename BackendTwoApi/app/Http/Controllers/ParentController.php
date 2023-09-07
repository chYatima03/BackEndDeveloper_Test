<?php

namespace App\Http\Controllers;

use App\Models\Parents;
use App\Resources\ParentsResource;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;

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
        return $this->successResponse(ParentsResource::collection(Parents::all()),Response::HTTP_OK);
    }

    //
}
