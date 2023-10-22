<?php

namespace App\Http\Controllers\API;

use App\UserFollow;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserFollow as UserFollowResource;
use App\User;
use App\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserFollowController extends BaseController
{
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'following_id' => 'required|integer',
                'unfollow' => 'required|integer'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            // $input['follower_id'] = auth()->user()->id;
            // $follow['follow'] = UserFollow::updateOrCreate($input);
            $user = User::find($request->following_id);
            if($user){

                if($request->unfollow == 0){
                    $follow['follow'] = UserFollow::updateOrCreate([
                        'follower_id'   => auth()->user()->id,
                        'following_id' => $request->following_id,
                    ],[
    
                    ]);
                    $auth_user = auth()->user()->name;
                    $notification['title'] = 'User Follow';
                    $notification['message'] = "$auth_user has started following you.";
                    $notification['type'] = 'profile';
                    $notification['user_id'] = $request->following_id;
                    $notification['created_by'] = auth()->user()->id;
    
                    $notify = UserNotification::notification([$request->following_id], $notification);
                    if($notify){
                        // $notification['user_reachup_id'] = $reachup->id;
                        $notification['notification'] = UserNotification::create($notification);
                    }
    
                    return $this->sendResponse(UserFollowResource::collection($follow), 'User followed successfully.');
                } else {
                    // dd(UserFollow::where('follower_id', auth()->user()->id)->where('following_id', $request->following_id)->exists());
                    if(UserFollow::where('follower_id', auth()->user()->id)->where('following_id', $request->following_id)->exists()){
                        UserFollow::where('follower_id', auth()->user()->id)->where('following_id', $request->following_id)->delete();
                        return $this->sendResponse(null, 'User unfollowed successfully.');
                    } else {
                        return $this->sendError('Unauthorized');
                    }
                }
            } else {
                return $this->sendError('No user found to follow/unfollow');
            }
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }
}
