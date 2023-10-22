<?php

namespace App\Http\Controllers\API;

use App\AdvisorDetail;
use App\Category;
use App\DefaultSetting;
use App\Http\Resources\Category as CategoryResource;
use App\UserType;
use Illuminate\Http\Request;
use App\Http\Resources\UserType as UserTypeResource;
use App\Http\Resources\AdvisorDetail as AdvisorDetailResource;
use App\Http\Resources\AdvisorDetailWithPaginate as AdvisorDetailWithPaginateResource;
use App\Http\Resources\FellowDetail as FellowDetailResource;
use App\UserBlock;
use App\Http\Resources\UserInterest as UserInterestResource;
use App\Http\Resources\UserResource;
use App\Metadata;
use App\User;
use App\UserInterest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CommonController extends BaseController
{
    public function user_types()
    {
        try {
            //code...
            $data = UserType::all();
            return $this->sendResponse(UserTypeResource::collection($data), 'User types retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
            //throw $th;
        }
        
    }
    
    public function metadata()
    {
        $data['meta_data'] = Metadata::first();
        $data['default_setting'] = DefaultSetting::first();

        $data['default_setting']['client_id'] = Crypt::decryptString($data['default_setting']['client_id']);
        $data['default_setting']['secret_id'] = Crypt::decryptString($data['default_setting']['secret_id']);

        return $this->sendResponse($data, 'Metadata retrieved successfully.');
        
    }

    public function explore(Request $request)
    {
        try {
            //code...
        // dd($request->all());
            $categories = Category::whereNull('parent_id')->inRandomOrder()->limit(4)->get();
            
            $blocked_ids = UserBlock::where('user_id', auth()->user()->id)->pluck('blocked_id');
            
            $popular = AdvisorDetail::where('user_id','!=', auth()->user()->id)->with('user.ratings.source')
            ->whereHas('user', function($query) use ($request, $blocked_ids){
                $query->where('name', 'like', '%' . $request->name . '%')
                ->where('user_type_id',2)
                ->where('status','active')
                ->whereNotIn('id', $blocked_ids)
                ;
            })->paginate(4)->toJson();


            // $popular =  AdvisorDetail::with('user.ratings.source')->whereHas('user.ratings', function($query){
            //     $query->orderBy('user_ratings.rate', 'DESC');
            // })->paginate(4)->toJson();
            $popular_decode = json_decode($popular);
            $data['categories'] = CategoryResource::collection($categories);
            // $data['advisors'] = AdvisorDetailResource::collection($advisors);
            $data['popular'] = new AdvisorDetailWithPaginateResource($popular_decode);
            $data['profile_status'] = Auth::user()->profile_complete;
            return $this->sendResponse($data, 'Explore retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }

    }

    public function explore_with_category($category_id)
    {
        try {
            
            $sub_category_id = [];
            //get category of requested id
            $categories = Category::where('id', $category_id)->with('sub_categories')->first();
            
            //if category have no sub categories, and itself it is subcategory then push id in variable
            if(!isset($categories)){
                return $this->sendError('Category not exist');
            }
            if(!$categories->have_subcategories){
                $sub_category_id[] = $categories->id;
            }
            //if category have subcategories, push those subcategory ids into variable  
            else 
            {
                foreach ($categories->sub_categories as $key => $value) {
                    $sub_category_id[] = $value->id;
                }
            }
            // dd($sub_category_id);
            //get advisors of given category id with respect to subcategory from user_interest
            $ui = UserInterest::where('user_type_id', 2)->whereIn('sub_category_id', $sub_category_id)->with('advisors')->groupBy('user_id')->limit(4)->get();
            // dd($ui);
            //push in array of only advisor object
            $advisors_id = [];
            foreach ($ui as $key => $value) {
                if(isset($value->advisors->id)){
                    $advisors_id[] =$value->advisors->id;
                }
            }
            // $advisors = AdvisorDetail::whereIn('id', $advisors_id)->get();

            $blocked_ids = UserBlock::where('user_id', auth()->user()->id)->pluck('blocked_id');
            
            $popular = AdvisorDetail::where('user_id','!=', auth()->user()->id)
            ->whereIn('id', $advisors_id)->with('user.ratings.source')
            ->whereHas('user', function($query) use ($blocked_ids){
                $query->where('status','active')
                ->whereNotIn('id', $blocked_ids)
                ;
            })->paginate(4)->toJson();

            $popular_decode = json_decode($popular);

            $getCategories = Category::whereNull('parent_id')->inRandomOrder()->limit(4)->get();

            $data['categories'] = CategoryResource::collection($getCategories);
            $data['popular'] = new AdvisorDetailWithPaginateResource($popular_decode);
            // $data['advisor'] = AdvisorDetailResource::collection($advisors);

            return $this->sendResponse($data, 'Explore retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }

    }

    public function suggest_advisor(Request $request){
        try { 
            $data = $request->all();

            $user_ids = AdvisorDetail::with('user.ratings.source')
            ->whereHas('user', function($query) use ($request){
                $query->where('name', 'like', '%' . $request->name . '%')
                ->where('status','active')
                ->where('user_type_id',2);
            })->limit(5)->pluck('user_id');
            $user = [];
            foreach ($user_ids as $key => $user_id) {
                if(UserBlock::where('user_id', auth()->user()->id)->where('blocked_id', $user_id)->doesntExist()){
                    $user[] = User::where('id',$user_id)->first()->name;
                }
            }
            return $this->sendResponse($user, 'Explore retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }
    }

    public function find_advisor(Request $request)
    {
        try { 
            $data = $request->all();
            // $blocked_ids = [];
            $blocked_ids = UserBlock::where('user_id', auth()->user()->id)->pluck('blocked_id');
            
            // $popular = AdvisorDetail::with('user.ratings.source')->whereHas('user', function($query) use ($request, $blocked_ids){
            //     $query->where('name', 'like', '%' . $request->name . '%')->where('status','active')->whereNotIn('id', $blocked_ids)
            //     ;
            // })->paginate(4)->toJson();
            // $popular = AdvisorDetail::with(['user.ratings.source'=> function($query) use ($request, $blocked_ids){
            //     $query->where('name', 'like', '%' . $request->name . '%')->where('status','active')->whereNotIn('id', $blocked_ids)
            //     ;
            // }])->paginate(4)->toJson();
            $name = $request->name;
            $popular = AdvisorDetail::with('user.ratings.source')
            ->whereHas('user', function($query) use ($name, $blocked_ids){
                $query->where(function ($q) use($name) {
                    $q->where('name', 'like', '%' . $name . '%')
                    ->orWhere('username', 'like', '%' . $name . '%');
                })
                // $query->where('name', 'like', '%' . $name . '%')
                ->where('status','active')
                ->whereNotIn('id', $blocked_ids)
                ->where('user_type_id',2);
            })->paginate(4)->toJson();

            $popular_decode = json_decode($popular);
            // dd($popular_decode);
            $data['popular'] = new AdvisorDetailWithPaginateResource($popular_decode);
            return $this->sendResponse($data, 'Explore retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }

    }


    public function fellow_details(Request $request)
    {
        try { 
            $input = $request->all();
            
            $validator = Validator::make($input, [
                'type' => 'required|in:follower,following',
                'id' => 'required',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            } 

            $user = User::find($request->id);
            if($user){

                if($request->type == 'follower'){
                    // dd($user);
                    // $data = $user->except_auth_followers;
                    $data = $user->followers;
                    // dd($user->followers);
                } else if($request->type == 'following'){
                    // $data = $user->except_auth_follows;
                    $data = $user->follows;
                    // dd($user->follows);
                }
            } else {
                return $this->sendError('User not found');
            }
            // dd($data);
            $a = FellowDetailResource::collection($data);
            // $a = FellowDetailResource::collection($data);
            // dd($a);

            return $this->sendResponse($a, 'Explore retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }
        }

    }

    // function sendPushNotification($fcm_token, $title, $message, $id = null) {  
    
    //     $url = "https://fcm.googleapis.com/fcm/send";            
    //     $header = [
    //     'authorization: key=' . $your_project_id_as_key,
    //         'content-type: application/json'
    //     ];    

    //     $postdata = '{
    //         "to" : "' . $fcm_token . '",
    //             "notification" : {
    //                 "title":"' . $title . '",
    //                 "text" : "' . $message . '"
    //             },
    //         "data" : {
    //             "id" : "'.$id.'",
    //             "title":"' . $title . '",
    //             "description" : "' . $message . '",
    //             "text" : "' . $message . '",
    //             "is_read": 0
    //           }
    //     }';

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    //     $result = curl_exec($ch);    
    //     curl_close($ch);

    //     return $result;
    // }
}
