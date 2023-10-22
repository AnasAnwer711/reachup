<?php

namespace App\Http\Controllers\API;

use App\Category;
use App\Http\Controllers\Controller;
use App\UserInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserInterest as UserInterestResource;

class UserInterestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $interests['interests'] = UserInterestResource::collection(auth()->user()->interests);
            return $this->sendResponse($interests, 'User Interest retrieved successfully.');
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
                'sub_category_ids' => 'required',
                'user_type_id' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            $sub_categories = explode (",", $input['sub_category_ids']); 
            // dd($sub_categories);

            $input['user_id'] = auth()->user()->id;
            $categories = Category::whereIn('id',$sub_categories)->get();
            // dd($categories);
            foreach ($categories as $key => $category) {
                # code...
                if($category->have_subcategories){
                    return $this->sendError('Inavlid '.$category->title.' category selected.');       
                }
                // if(UserInterest::where('user_id',$input['user_id'])->where('sub_category_id', $category->id)->exists()){
                //     return $this->sendError('User Interest for category '.$category->title.' already exists.');       
                // }
            }

            UserInterest::where('user_id',$input['user_id'])->delete();

            foreach ($categories as $key => $category) {
                # code...
                $interest['interest'] = UserInterest::create([
                    'user_type_id' => $input['user_type_id'],
                    'user_id' => $input['user_id'],
                    'sub_category_id' => $category->id,
                ]);
            }
            // dd($category);

            return $this->sendResponse([], 'User Interest created successfully.');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserInterest $userInterest)
    {
        try {
            $data['interest'] = $userInterest;
            return $this->sendResponse(UserInterestResource::collection($data), 'User Interest retrieved successfully.');
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
     * @param  int  UserInterest $userInterest
     * @return \Illuminate\Http\Response
     */
    public function edit(UserInterest $userInterest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  UserInterest $userInterest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserInterest $userInterest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  UserInterest $userInterest
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserInterest $userInterest)
    {
        try{
            if($userInterest){
                $data['interest'] = $userInterest;
                $userInterest->delete();
                return $this->sendResponse(UserInterestResource::collection($data), 'User interest deleted successfully.');
            } else {
                return $this->sendError('No user interest found');       
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
