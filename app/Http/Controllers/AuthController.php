<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Role;
use Validator;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class AuthController extends Controller
{
    /**
     * login API
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
            //'email' => 'required|email',
            'password' => 'required',
            'username' => 'required',
//            'scopes' => 'required',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 417);
        }

        $credentials = $request->only(['username', 'password']);

        if(Auth::attempt($credentials)) {
            $user = Auth::user();
//            $scopes = $request->only(['scopes'])['scopes'];
//            $scopes = explode(" ", $scopes);
$scopes = ['tutor'];
            $role_checker = new Role\RoleChecker();
            $scopes_valid = true;
            for($i = 0; $i < count($scopes); $i++) {
                if(!$role_checker->check($user, $scopes[$i])) $scopes_valid = false;
            }
//            if($role_checker->check($user, $scopes)) {
            if($scopes_valid) {
                $success['token'] = $user->createToken('MyApp', $scopes)->accessToken;
                return response()->json(['success' => $success], 200);
            } else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    /**
     * register API
     *
     * @return \Illuminate\Http\Response
     */
    public function registerOld(Request $request)
    {
    	$input = $request->all();
    	$validator = Validator::make($input, [
    		'name' => 'required',
		'email' => 'required|email',
		'username' => 'required',
		'password' => 'required',
		'c_password' => 'required|same:password',
		'scopes' => 'required',
    	]);
    	if ($validator->fails()) {
    		
    		return response()->json($validator->errors(), 417);
    	}
    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
		'username' => $request->username,
    		'password' => bcrypt($request->password),
		'roles' => json_encode(explode(" ", $request->scopes)),
    	]);
    	$success['name'] = $user->name;
    	$success['token'] = $user->createToken('MyApp', [$request->scopes])->accessToken;
    	return response()->json(['success' => $success], 200);
    }

    public function register(Request $request) {
    	$input = $request->all();
    	$validator = Validator::make($input, [
    		'name' => 'required',
		'email' => 'required|email',
		'username' => 'required',
		'password' => 'required',
		'c_password' => 'required|same:password',
		'scopes' => 'required',
    	]);
    	if ($validator->fails()) {
    		
    		return response()->json($validator->errors(), 417);
        }
        
        $username = $request->only(['username'])['username'];
        if(User::where('username', '=', $username)->exists()) {
            return response()->json(['error' => 'Username is taken'], 417);
        }

        $client = new Client();
        $apiResponse = $client->post('https://markit.mijdas.com/api/user', [
            /*RequestOptions::JSON*/'json' => [
                'request' => 'SIGN_UP',
                'username' => $request->get('username'),
                'password' => $request->get('password'),
                'email' => $request->get('email'),
                'firstName' => $request->get('name'),
                'lastName' => $request->get('name'),
                'permissionType' => $request->get('scopes')
            ]
        ]);

        if($apiResponse->getStatusCode() != 200) {
            return response()->json(['error' => 'Username is taken, or another error occurred'], 417);
        }

    	$user = User::create([
    		'name' => $request->name,
    		'email' => $request->email,
		'username' => $request->username,
    		'password' => bcrypt($request->password),
		'roles' => json_encode(explode(" ", $request->scopes)),
    	]);
    	$success['name'] = $user->name;
    	$success['token'] = $user->createToken('MyApp', [$request->scopes])->accessToken;
    	return response()->json(['success' => $success], 200);
    }

    public function checkToken(Request $request) {
        $input = $request->all();
        $validator = Validator::make($input, [
//            'token' => 'required',
            'scopes' => 'required',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(), 417);
        }

        $scopes = $request->only('scopes')['scopes'];
        $scopes = explode(" ", $scopes);
        for($i = 0; $i < count($scopes); $i++) {
            if(!$request->user('api')->tokenCan($scopes[$i])) {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        }

        return response()->json(['success' => 'Authorised', 'scopes' => $scopes], 200);
    }

    /**
	 * admin login API
	 * @return \Illuminate\Http\Response
	 */
	public function adminLogin(Request $request)
	{
		$input = $request->all();
		$validator = Validator::make($input, [
			//'email' => 'required|email',
			'password' => 'required',
                        'username' => 'required',
		]);
		if ($validator->fails()) {
			
			return response()->json($validator->errors(), 417);
		}
		$credentials = $request->only(['username', 'password']);
		if (Auth::attempt($credentials)) {
			
			$user = Auth::user();
			$success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
			return response()->json(['success' => $success], 200);
		}
		else {
			return response()->json(['error' => 'Unauthorized'], 401);
		}
	}
	/**
	 * admin register API
	 * @return \Illuminate\Http\Response
	 */
	public function adminRegister(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'email' => 'required|email',
			'password' => 'required',
			'c_password' => 'required|same:password',
                        'username' => 'required',
		]);
		if ($validator->fails()) {
			
			return response()->json($validator->errors(), 417);
		}
		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
                        'username' => $request->username,
                        'roles' => json_encode(['*']),
		]);
		$success['name'] = $user->name;
		$success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
		return response()->json(['success' => $success], 200);
	}

        public function coordinatorRegister(Request $request)
        {
                $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'email' => 'required|email',
                        'password' => 'required',
                        'c_password' => 'required|same:password',
                        'username' => 'required',
                ]);
                if ($validator->fails()) {

                        return response()->json($validator->errors(), 417);
                }
                $user = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'username' => $request->username,
                        'roles' => json_encode(['ROLE_COORDINATOR']),
                ]);
                $success['name'] = $user->name;
                //$success['token'] = $user->createToken('MyApp', ['*'])->accessToken;
                return response()->json(['success' => $success], 200);
        }
}
