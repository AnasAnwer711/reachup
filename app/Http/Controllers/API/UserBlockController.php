<?php

namespace App\Http\Controllers\API;

use App\UserBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\UserBlock as UserBlockResource;

class UserBlockController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $blocks['blocks'] = UserBlockResource::collection(auth()->user()->blocks);
            return $this->sendResponse($blocks, 'Blocked users retrieved successfully.');
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
                'blocked_id' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $input['user_id'] = auth()->user()->id;
            $block['block'] = UserBlock::create($input);

            return $this->sendResponse(UserBlockResource::collection($block), 'User has been blocked successfully.');
        } catch (\Throwable $th) {
            // dd($th);
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
     * @param  \App\UserBlock  $userBlock
     * @return \Illuminate\Http\Response
     */
    public function show(UserBlock $userBlock)
    {
        try {
            $data['block'] = $userBlock;
            return $this->sendResponse(UserBlockResource::collection($data), 'Blocked user retrieved successfully.');
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
     * @param  \App\UserBlock  $userBlock
     * @return \Illuminate\Http\Response
     */
    public function edit(UserBlock $userBlock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserBlock  $userBlock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserBlock $userBlock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserBlock  $userBlock
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserBlock $userBlock)
    {
        try { 
            if($userBlock){
                $data['block'] = $userBlock;
                $userBlock->delete();
                return $this->sendResponse(UserBlockResource::collection($data), 'User unblocked successfully.');
            } else {
                return $this->sendError('No user found');       
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
