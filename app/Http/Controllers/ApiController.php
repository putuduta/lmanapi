<?php

namespace App\Http\Controllers;

use App\Models\Designer;
use App\Models\DetailSkill;
use App\Models\RegistrationPayment;
use App\Models\School;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request)
    {
        $messages = [
            'required'  => 'Harap bagian :attribute di isi.',
            'unique'    => ':attribute sudah digunakan',
        ];

        //Validate data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'address' => 'required|string',
            'password' => 'required|string|min:6|max:50',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        //Request is valid, create new user
        $school = School::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'password' => bcrypt($request->password),
        ]);

        //Request is validated
        //Crean token
        try {
            $token = JWTAuth::attempt($request->only('email', 'password'), ['exp' => Carbon::now()->addDays(7)->timestamp]);
            //User created, return success response
            return response()->json([
                'success' => true,
                'message' => 'School created successfully',
                'data' => $school
            ], Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error Register School',
            ], 500);
        }
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials, ['exp' => Carbon::now()->addDays(7)->timestamp])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function registerPayment(Request $request)
    {
        $messages = [
            'required'  => 'Harap bagian :attribute di isi.',
            'unique'    => ':attribute sudah digunakan',
        ];

        //Validate data
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|string',
            'payment_term' => 'required|string',
            'due_date' => 'required|string',
            'bank_name' => 'required|string',
            'bank_number' => 'required|string',
            'confirmed_time' => 'required|string',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        
        $registerPayment = RegistrationPayment::create([
            'school_id' => $request->id,
            'payment_term' => $request->payment_term,
            'bank_name' => $request->bank_name,
            'bank_number' => $request->bank_number,
            'resume' => $request->resume,
            'status_confirmed' => 1,
            'confirmed_time' => $request->confirmed_time,
        ]);

        //Request is valid, create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'dob' => $request->dob,
            'address' => $request->address,
            'id_card' => $request->id_card,
            'is_designer' => $request->is_designer == '1' ? true : false,
            'is_customer' => $request->is_customer == '1' ? true : false,
            'password' => bcrypt($request->password)
        ]);


        //Request is validated
        //Crean token
        try {
            $token = JWTAuth::attempt($request->only('email', 'password'), ['exp' => Carbon::now()->addDays(7)->timestamp]);
            //User created, return success response
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
                'token' => $token
            ], Response::HTTP_OK);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error Register user',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 200);
        }

        //Request is validated, do logout        
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        if ($user->is_designer) {
            $designer = DB::table('designers')
                    ->join('users', 'users.id', '=', 'designers.user_id')
                    ->select(
                        'users.name',
                        'users.email',
                        'users.phone_number',
                        'users.dob',
                        'users.address',
                        'users.id_card',
                        'users.is_designer',
                        'users.is_customer',
                        'designers.*'   
                    )->where(
                        'users.id', '=', $user->id
                    )->first();
            return response()->json(['user' => $designer]);
        }

        return response()->json(['user' => $user]);
    }
}
