<?php

namespace App\Http\Controllers;

use App\Post;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    public function getPost()
    {
        $posts = Post::with(['user'])->get();
        foreach ($posts as $post) {
            $imageRecord = Post::where('id', $post->id)->get(['images']);
            if (is_null($imageRecord->first()->images)) {
                $post['images'] = null;
            } else {
                $myArray = explode(' | ', $post['images']); //gives image nameandlocation
                for ($i = 0; $i < count($myArray); $i++) {
                    $myArray[$i] = url('/') . '/uploads/room/' . $myArray[$i]; //appending url
                }
                $post['images'] = $myArray;
            }

        }
        return response()->json([
            'data' => $posts
        ]);
    }


    public function getUserPost(Request $request)
    {

        $posts = Post::where('user_id', $request->user_id)->with(['user'])->get();
        foreach ($posts as $post) {
            $imageRecord = Post::where('id', $post->id)->get(['images']);
            if (is_null($imageRecord->first()->images)) {
                $post['images'] = null;
            } else {
                $myArray = explode(' | ', $post['images']); //gives image nameandlocation
                for ($i = 0; $i < count($myArray); $i++) {
                    $myArray[$i] = url('/') . '/uploads/room/' . $myArray[$i]; //appending url
                }
                $post['images'] = $myArray;
            }

        }
        return response()->json([
            'data' => $posts
        ]);
    }

    public function getVehicleInfo()
    {
        $vehicles = Vehicle::with(['user'])->get();
        return response()->json([
            'data' => $vehicles
        ]);
    }

    public function deleteVehicleInfo(Request $request)
    {
        try {
            $vehicle = Vehicle::findOrFail($request->id);
            $vehicle->delete();
            return response()->json([
                'status'=>true,
                'message' => 'Successfully_deleted'

            ]);
        } catch (\Exception $exception) {
            return response()->json([
                dd($exception),
                'status'=>false,
                'message' => 'exception occured'
            ]);
        }
    }
    public function storeVehicle(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'price' => 'required',
                'owner_name' => 'required',
                'contact' => 'required',
                'service_area' => 'required',
            ]);
            if ($validator->fails()) {
//            return FirebaseNotification::handleValidation($validator);
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'error' => $validator->errors()
                ]); //Failed validation: 403 Forbidden ("The server understood the request, but is refusing to fulfill it")
            } else {
                if ($request->bearerToken()) {
                    $user = auth('api')->user()->id;
                    $data['added_by'] = $user;
                    $data['title'] = $request->title;
                    $data['contact'] = $request->phone;
                    $data['price'] = $request->price;
                    $data['owner_name'] = $request->owner_name;
                    $data['service_area'] = $request->service_area;
                    //To-do -> check null images
                    Vehicle::create($data);
                    return response()->json([
                        'status'=>true,
                        'message' => 'vehicle added successfully'
                    ]);

                } else {
                    return response()->json([
                        'status'=>false,
                        'message' => 'not logged in'
                    ]);
                }
            }
        } catch
        (\Exception $exception) {
            return response()->json([
                'status'=>false,
                'message' => 'exception occured'
            ]);
        }

    }

    public function storePost(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|string',
                'location' => 'required',
                'city' => 'required',
                'property_type' => 'required',
                'facilities' => 'required',
            ]);
            if ($validator->fails()) {
//            return FirebaseNotification::handleValidation($validator);
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'error' => $validator->errors()
                ]); //Failed validation: 403 Forbidden ("The server understood the request, but is refusing to fulfill it")
            } else {
                if ($request->bearerToken()) {
                    $user = auth('api')->user()->id;
                    if ($images = $request->file('img')) {
                        foreach ($images as $image) {
                            $destinationPath = 'uploads/room/'; // upload path
                            $extension = $image->getClientOriginalExtension();
                            $image_url = now()->format('Y-m-d') . '-' . mt_rand() . '.' . $extension;
                            $image->move($destinationPath, $image_url);
                            $img[] = $image_url;
                        }
                    }
//                $post = new Post();
                    if ($images = $request->file('img')) {
                        $data['images'] = implode(" | ", $img);
                    } else {
                        $data['images'] = null;
                    }
                    $data['user_id'] = $user;
                    $data['title'] = $request->title;
                    $data['description'] = $request->description;
                    $data['price'] = $request->price;
                    $data['location'] = $request->location;
                    $data['city'] = $request->city;
                    $data['facilities'] = $request->facilities;
                    $data['property_type'] = $request->property_type;
                    $data['status'] = 0; //by default available
                    //To-do -> check null images
                    Post::create($data);
                    return response()->json([
                        'status'=>true,
                        'message' => 'post created successfully'
                    ]);

                } else {
                    return response()->json([
                        'status'=>false,
                        'message' => 'not logged in'
                    ]);
                }
            }
        } catch
        (\Exception $exception) {
            return response()->json([
                'status'=>false,
                'message' => 'exception occured'
            ]);
        }

    }

    public function updatePost(Request $request)
    {
        try {
            if ($request->bearerToken()) {
                $data = Post::findOrFail($request->id);
                if ($images = $request->file('img')) {
                    foreach ($images as $image) {
                        $destinationPath = 'uploads/room/'; // upload path
                        $extension = $image->getClientOriginalExtension();
                        $image_url = now()->format('Y-m-d') . '-' . mt_rand() . '.' . $extension;
                        $image->move($destinationPath, $image_url);
                        $img[] = $image_url;
                        $data['images'] = implode(" | ", $img);
                    }
                    //else if --> if once returns true, do not execute again
                }
                if ($request->description) {
                    $data['description'] = $request->description;
                }
                if ($request->price) {
                    $data['price'] = $request->price;
                }
                if ($request->location) {
                    $data['location'] = $request->location;
                }
                if ($request->city) {
                    $data['city'] = $request->city;
                }
                if ($request->facilities) {
                    $data['facilities'] = $request->facilities;
                }
                if ($request->property_type) {
                    $data['property_type'] = $request->property_type;
                }
                //To-do -> check null images
                $data->update(); //$post->update($data)
                return response()->json([
                    'status'=>true,
                    'message' => 'updated successfully'
                ]);

            } else {
                return response()->json([
                    'status'=>false,
                    'message' => 'not logged in'
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
//                dd($exception),
                'status'=>false,
                'message' => 'exception occured'
            ]);
        }
    }

    public function deletePost(Request $request)
    {
        try {
            $post = Post::findOrFail($request->id);
            $post->delete();
            return response()->json([
                'status'=>true,
                'message' => 'Successfully_deleted'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status'=>false,
                'message' => 'exception occured'
            ]);
        }


    }

    public function makeSoldOut(Request $request)
    {
        try {
            if ($request->bearerToken()) {
                $post = Post::findOrFail($request->post_id);
                $post['status'] = 1; //sold out
                $post->update();

                return response()->json([
                    'status'=>true,
                    'message' => 'Success'
                ]);
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message' => 'unauthorized'
                ]); //unauthorized
            }

        } catch (\Exception $exception) {
            return response()->json([
                'status'=>false,
                'message' => 'not logged in'
            ]);
        }
    }

}
