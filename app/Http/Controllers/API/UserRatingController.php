<?php

namespace App\Http\Controllers\API;

use App\UserRating;
use App\Http\Resources\UserRating as UserRatingResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRatingController extends BaseController
{
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'target_id' => 'required|integer',
                'rate' => 'required|numeric',
                'reviews' => 'required',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            // $input['follower_id'] = auth()->user()->id;
            // $follow['follow'] = UserFollow::updateOrCreate($input);
            // if($request->unfollow == 0){
            $rating['rating'] = UserRating::updateOrCreate([
                'source_id'   => auth()->user()->id,
                'target_id' => $request->target_id,
            ],[
                'rate'   => $request->rate,
                'reviews' => $request->reviews,
            ]);
            return $this->sendResponse(UserRatingResource::collection($rating), 'User rated successfully.');
            // } else {
            //     UserFollow::where('follower_id', auth()->user()->id)->where('following_id', $request->following_id)->delete();
            //     return $this->sendResponse(null, 'User unfollowed successfully.');
            // }
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }

    }
}
