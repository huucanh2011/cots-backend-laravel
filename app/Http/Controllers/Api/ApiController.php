<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    //respond
    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    //respond success
    protected function respondSuccess()
    {
        return $this->respond(null);
    }

    //respond error
    protected function respondError($message, $statusCode)
    {
        return $this->respond([
            'errors' => [
                'message' => $message,
                'status_code' => $statusCode
            ]
            ], $statusCode);
    }

    //respond unauthorized
    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    //respond forbidden
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }
}
