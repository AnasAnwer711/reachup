<?php

namespace App\Http\Controllers\API;

use App\UserNotification;
use Illuminate\Http\Request;
use App\Http\Resources\UserNotification as UserNotificationResource;
use Illuminate\Support\Facades\Validator;

class UserNotificationController extends BaseController
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            UserNotification::where('user_id', auth()->user()->id)->update(['delivered'=> 1]);
            $notifications['notifications'] = UserNotificationResource::collection(auth()->user()->notifications);
            return $this->sendResponse($notifications, 'User Notification retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'title' => 'required',
                'message' => 'required',
                'user_id' => 'required|integer',
                'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:8192',
                'type' => 'required',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/notifications');
            // dd($destinationPath);

            $image->move($destinationPath, $name);
            $input = $request->all();
            $input['image'] = url('/').'/notifications/'.$name;
            $input['created_by'] = auth()->user()->id;
            
            // dd($input); 
            UserNotification::notification([$input['user_id']], $input);
            $notification['notification'] = UserNotification::create($input);

            return $this->sendResponse(UserNotificationResource::collection($notification), 'User Notification created successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function show(UserNotification $userNotification)
    {
        try {
            $data['notification'] = $userNotification;
            return $this->sendResponse(UserNotificationResource::collection($data), 'User Notification retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function edit(UserNotification $userNotification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserNotification $userNotification)
    {
        // dd($request->all());
        $userNotification->update(['read'=> $request->read]);
        return $this->sendResponse(new UserNotificationResource($userNotification), 'User notification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserNotification  $userNotification
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserNotification $userNotification)
    {
        if($userNotification){
            $data['notification'] = $userNotification;
            $userNotification->delete();
            return $this->sendResponse(UserNotificationResource::collection($data), 'User notification deleted successfully.');
        } else {
            return $this->sendError('No user notification found');       
        }
    }
}
