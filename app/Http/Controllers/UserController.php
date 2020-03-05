<?php
 
namespace App\Http\Controllers;

use App\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
 
class UserController extends Controller
{
    private $successStatus = 200;
 
    public function register(Request $request) {
        try {
            $validator = Validator::make($request->all(),
                [
                    'firstName' => 'required|min:3',
                    'lastName' => 'required|min:3',
                    'username' => 'required|min:6|unique:users',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|alpha_num|min:5',
                    'confirmPassword' => 'required|same:password',
                    'phoneNumber' => 'required|min:1',
                    'birthDate' => 'required|min:1',
                    'address' => 'nullable|min:1',
                    'kecamatan' => 'nullable|min:1',
                    'kabupatenKota' => 'nullable|min:1',
                    'provinsi' => 'nullable|min:1',
                    'negara' => 'nullable|min:1',
                    'fee' => 'nullable|min:1',
                    'bank' => 'nullable|min:1',
                    'bankAccountNumber' => 'nullable|min:1',
                    'idType' => 'nullable|min:1',
                    'idNumber' => 'nullable|min:1',
                ]
            );
     
            if($validator->fails()) return response()->json(['Validation errors' => $validator->errors()], 400);
     
            $user = User::create(array(
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone_number' => $request->phoneNumber,
                'birth_date' => $request->birthDate,
                'address' => $request->address,
                'kecamatan' => $request->kecamatan,
                'kabupaten_kota' => $request->kabupaten,
                'provinsi' => $request->provinsi,
                'negara' => $request->negara,
                'fee' => $request->fee,
                'bank' => $request->bank,
                'bank_account_number' => $request->bankAccountNumber,
                'id_type' => $request->idType,
                'id_number' => $request->idNumber,
            ));         
     
            return response()->json([
                'message' => 'You have registered successfully',
                'data' => [
                    'user' => $user
                ]
            ], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Registration Failed'
            ], 400);
        }
    }
 
    public function login(Request $request) {
        try {
            if (
                ! Auth::attempt(['email' => request('email'), 'password' => request('password')]) &&
                ! Auth::attempt(['username' => request('username'), 'password' => request('password')])
            ) {
                return response()->json(['error'=>'Unauthorized'], 401);
            }

            $user = Auth::user(); 
            $token = $user->createToken('token')->accessToken;

            return response()->json([
                'message' => 'Login success.',
                'data' => [
                    'token' => $token
                ]
            ], $this->successStatus);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Login failed.'
            ], 400);
        }
    }

    public function logout(Request $request) {
        try {
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'You have been successfully logged out.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'You are not logged in.'
            ], 400);
        }
    }
}