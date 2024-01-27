<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    function paginatedSuccessResponse($data, $name)
    {
        return response()->json(
            [
                'status' => 'success',
                $name => $data->items(),
                'meta' => [
                    'pagination' => [
                        $name => [
                            'last_item' => $data->lastItem(),
                            'first_item' => $data->firstItem(),
                            'last_page' => $data->lastPage(),
                            'per_page' => $data->perPage(),
                            'current_page' => $data->currentPage(),
                            'total' => $data->total()
                        ]
                    ]
                ]
            ]
        );
    }
}
