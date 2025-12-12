<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;

$exception->render(function (AuthenticationException $e, $request) {
    if($request->is('api/*')){
        return response()->json([
            'success' => false,
            'message' => 'Kamu tidak terautenikasi'
        ]);
    }
});