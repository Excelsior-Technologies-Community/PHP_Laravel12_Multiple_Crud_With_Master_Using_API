<?php
// app/Traits/ApiResponseTrait.php

namespace App\Traits;

trait ApiResponseTrait
{
    /**
     * Success Response
     */
    protected function successResponse($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ], $code);
    }
    
    /**
     * Error Response
     */
    protected function errorResponse($message = 'Error', $errors = null, $code = 400)
    {
        $response = [
            'status' => false,
            'message' => $message,
            'timestamp' => now()->toISOString()
        ];
        
        if ($errors) {
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $code);
    }
    
    /**
     * Paginated Response
     */
    protected function paginatedResponse($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
            'timestamp' => now()->toISOString()
        ], $code);
    }
}