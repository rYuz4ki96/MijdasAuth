<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiLoginController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {               
       // implement your user role retrieval logic, for example retrieve from `roles` database table
        $role = $user->checkRole();

        // grant scopes based on the role that we get previously
        if ($role == 'admin') {
            $request->request->add([
                'scope' => 'coordinator', // grant manage order scope for user with admin role
		'username' => $request->email
            ]);
        } else {
            $request->request->add([
                'scope' => 'tutor', // read-only order scope for other user role
		'username' => $request->email
            ]);
        }

        // forward the request to the oauth token request endpoint
        $tokenRequest = Request::create(
            '/oauth/token',
            'post'
        );
        return Route::dispatch($tokenRequest);
    }
}
