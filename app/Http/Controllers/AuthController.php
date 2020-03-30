<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|confirmed',
            'address' => 'required',
            'phone' => 'required|unique:users',
        ]);
        if ($validator->fails()) {
//            return FirebaseNotification::handleValidation($validator);
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'error' => $validator->errors()
            ]); //Failed validation: 403 Forbidden ("The server understood the request, but is refusing to fulfill it")
        } else {
            try {
                $profile_pic_name = null;
                if ($request->profile_pic_url) {
                    $str = $request->profile_pic_url;
                    $image = base64_decode($str);
                    $profile_pic_name = now()->format('Y-m-d') . '-' . mt_rand() . '.' . 'jpg';
                    $file = Image::make($image);
                    $file->save('uploads/users/' . $profile_pic_name);
                } else {
                    $profile_pic_name = null;
                }
                $user = new User();
                $data = $request->except(['profile_pic_url', 'password', 'password_confirmation']);
                $data['profile_pic_url'] = $profile_pic_name;
                $data['password'] = bcrypt($request->password);
                $user->create($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully created users!'
                ], 201);
            }
            catch (\Exception $exception){

            }
        }

    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required', //['input' => 'required|users:email,phone'],
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'error' => $validator->errors()]);
        } else {
            try {
                $credentials = request(['phone', 'password']);
                if (!Auth::attempt($credentials))
                    return response()->json([
                        'message' => 'Wrong phone or password'
                    ], 401);
                $user = $request->user();
//                if ($users->counct()>0)
                if ($user['profile_pic_url'] != null){
                    $user['profile_pic_url'] = url('/') . '/uploads/users/' . $user->profile_pic_url;
                }
                $user['created_at'] = $user->created_at->diffForHumans();
                $tokenResult = $user->createToken('Personal Access Token');
                if ($request->remember_me){
                    $token = $tokenResult->token;
//                    $token->expires_at = Carbon::now()->addWeeks(1);
                    $token->save();
                }
                $data = array();
                $data['users'] = $user;
                $tokenArray = array('access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
//                    'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
                );
                $data['token'] = $tokenArray;
                return response()->json([
                    'status' => true,
                    'message' => 'Login Successful',
                    'title' => 'Success',
                    'data' => $data,
                ]);

            } catch (\Exception $exception) {
                return response()->json([
                    'status' => false,
                    'message' => 'Exception Occurred | may be token not generated ',
                    'exception' => $exception]);
            }
        }
    }
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
    public function updateProfile(Request $request)
    {
        try{
            if ($request->bearerToken()) {
                $user = auth('api')->user();
                if ($request->name)
                    $user->name = $request->name;
                elseif ($request->address)
                    $user->address = $request->address;
                elseif ($request->email)
                    $user->email = $request->email;
                elseif($request->profile_pic_url) {
                    $str = $request->profile_pic_url;
                    $image = base64_decode($str);
                    $profile_file_name = now()->format('Y-m-d') . '-' . mt_rand() . '.' . 'jpg';
                    $file = Image::make($image);
                    $file->save('uploads/users/' . $profile_file_name);
                    $user->profile_pic_url = $request->profile_pic_url;
                    $user['profile_pic_url'] = $profile_file_name; //which will obviously redirect to image since we have given live server's directory path
                }
                $user->update();
                $user = auth()->user();
                $user['profile_pic_url'] = url('/') . '/uploads/users/' . $user->profile_pic_url;
                return response()->json([
                    'status' => true,
                    'message' => 'Profile Updated successfully',
                    'title' => 'Success',
//                'data' => $users
                ]);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized Access',
                    'title' => 'Failed'
                ], 401);

            }
        }
        catch (\exception $exception){
            return response()->json([
                dd($exception),
                'message'=>'exception occured',
                'exception'=>$exception
            ]);
        }
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|min:5',
            'new_password' => 'required|min:5',
            'confirm_new_password' => 'required|min:5'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
            ]);
        }

        try {
            if ($request->bearerToken()) {
                $user = auth()->user();
                $enrypPass = User::findOrFail($user->id)->password; //select query of eloquent
                if (Hash::check($request['old_password'], $enrypPass)) { //if true oe exist
                    if ($request->new_password == $request->confirm_new_password) {
                        $user->password = bcrypt($request->new_password);
                        $user->update();
                        $tokenResult = $user->createToken('Personal Access Token');
                        $token = $tokenResult->token;
                        $token->expires_at = \Illuminate\Support\Carbon::now()->addWeeks(1);
                        $data['token'] = array(
                            'access_token' => $tokenResult->accessToken,
                            'token_type' => 'Bearer',
                            'expires_at' => Carbon::parse(
                                $tokenResult->token->expires_at
                            )->toDateTimeString()
                        );
                        return response()->json([
                            'status' => true,
                            'message' => 'Password updated successfully',
                            'title' => 'Success',
                            'data' => $data
                        ]);
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => 'New password and confirm new password do not match.',
                            'title' => 'Failed'
                        ]);

                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Wrong old password',
                        'title' => 'Failed'
                    ]);

                }
            }
        } catch (\exception $e) {
            return response()->json([
                'message' => 'Exception occurred'
            ]);
        }

    }

}
