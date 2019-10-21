<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Role;
use Validator;

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
    public function register(Request $request)
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

    public function registerUser(Request $request) {
        
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
