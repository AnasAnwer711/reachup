<?php

namespace App\Http\Controllers\API;

use App\UserKeyword;
use Illuminate\Http\Request;
use App\Http\Resources\UserKeyword as UserKeywordResource;
use Illuminate\Support\Facades\Validator;

class UserKeywordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // $keywords = UserKeyword::all();
            $keywords['keywords'] = auth()->user()->keywords()->select('id', 'user_id', 'keyword')->get();
        // dd($keywords);
            return $this->sendResponse($keywords, 'User keywords retrieved successfully.');
            // return $this->sendResponse(UserKeywordResource::collection($keywords), 'User keywords retrieved successfully.');
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
                'keyword' => 'required',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            
            $input['user_id'] = auth()->user()->id;
    
            $keyword['keyword'] = UserKeyword::create($input);

            return $this->sendResponse(UserKeywordResource::collection($keyword), 'User Keyword created successfully.');
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
     * @param  \App\UserKeyword  $userKeyword
     * @return \Illuminate\Http\Response
     */
    public function show(UserKeyword $userKeyword)
    {
        try {
            $data['keyword'] = $userKeyword;
            return $this->sendResponse(UserKeywordResource::collection($data), 'User keyword retrieved successfully.');
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
     * @param  \App\UserKeyword  $userKeyword
     * @return \Illuminate\Http\Response
     */
    public function edit(UserKeyword $userKeyword)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserKeyword  $userKeyword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserKeyword $userKeyword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserKeyword  $userKeyword
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserKeyword $userKeyword)
    {
        try { 
            if($userKeyword){
                $data['keyword'] = $userKeyword;
                $userKeyword->delete();
                return $this->sendResponse(UserKeywordResource::collection($data), 'User keyword deleted successfully.');
            } else {
                return $this->sendError('No user keyword found');       
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
