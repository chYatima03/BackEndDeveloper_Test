<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponser
{
     /**
     * Display success responses
     * @param string|array $data
     * @param int $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function showResponse($data, $code = Response::HTTP_OK)
    {
        return response()->json(['data' => $data],$code);
    }
    /**
     * Build success responses
     * @param string|array $data
     * @param int $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data, $code)
    {
        return response()->json(['status'=> true,'code'=> $code,'data' => $data], $code);
    }

    /**
     * Build error responses
     * @param string|array $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }
}
