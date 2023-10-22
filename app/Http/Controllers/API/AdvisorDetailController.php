<?php

namespace App\Http\Controllers\API;

use App\AdvisorAvailability;
use App\AdvisorDetail;
use App\Http\Resources\AdvisorDetail as AdvisorDetailResource;
use App\Http\Resources\AdvisorDetailAvailability as AdvisorDetailAvailabilityResource;
use App\Http\Resources\AdvisorProfileDetail as AdvisorProfileDetailResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvisorDetailController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if(auth()->user()->user_type_id == 2 && is_null(auth()->user()->advisor)){
                return $this->sendError('Unable to access your profile, please complete your profile before accessing');  
            }
            $advisor_detail['advisor_detail'] = new AdvisorDetailAvailabilityResource(auth()->user()->advisor);
            return $this->sendResponse($advisor_detail, 'Advisor Profile retrieved successfully.');
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
            // dd(json_decode($input['availability']), json_decode($input['json']));
            $validator = Validator::make($input, [
                'personal_info' => 'required',
                'session_rate' => 'required',
                // 'from_time' => 'required',
                // 'to_time' => 'required',
                'availability' => 'required',
                'featured_image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $availabilities = json_decode($input['availability']);

            if($availabilities == null){
                return $this->sendError('Something went wrong in availability field.');       
            }
            // return response()->json($request->all());

            // dd(auth()->user()->id);
            $input['user_id'] = auth()->user()->id;
            $user = User::find($input['user_id']);
            if($user->user_type_id != 2){
                return $this->sendError('Unauthorised');       
            }

            $input['featured_image'] = null;

            $findAdvisorDetail = AdvisorDetail::where('user_id', $input['user_id'])->first();
            if($findAdvisorDetail){
                $input['featured_image'] = $findAdvisorDetail->featured_image;
            } 
            // else {
                // $input['featured_image'] = null;
            // }

            if($request->hasFile('featured_image')){

                $image = $request->file('featured_image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/advisors');
                
                $image->move($destinationPath, $name);
                // dd($input);
                $input['featured_image'] = url('/').'/advisors/'.$name;
            }
            // $input['featured_image'] = $name;

            $advisorDetail = AdvisorDetail::updateOrCreate([
                'user_id'   => $input['user_id'],
            ],[
                'personal_info' => $input['personal_info'],
                'featured_image' => $input['featured_image'],
                'session_rate' => $input['session_rate'],
                'is_verified' => 0,
                'status' => 'pending',
            ]);


            AdvisorAvailability::where('advisor_id', $advisorDetail->id)->delete();
            foreach ($availabilities as $key => $value) {
                # code...
                // $string = substr($value->available_days, 1, -1);
                // // dd($string);
                // $av_days = explode(',',$string);
                foreach ($value->available_days as $key => $day) {
                    AdvisorAvailability::create([
                        'advisor_id' => $advisorDetail->id,
                        'from_time' => $value->from_time,
                        'to_time' => $value->to_time,
                        'day' => $day,
                    ]);
                    # code...
                }
            }

            $advisor_detail['advisor_detail'] = new AdvisorDetailAvailabilityResource($advisorDetail);

            return $this->sendResponse($advisor_detail, 'Advisor profile updated successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
        }
        // dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AdvisorDetail  $advisorDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AdvisorDetail $advisorDetail)
    {
        try {
            $data['advisor_detail'] = $advisorDetail;
            return $this->sendResponse(AdvisorProfileDetailResource::collection($data), 'Advisor Details retrieved successfully.');
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
        }
        // dd($advisorDetail);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AdvisorDetail  $advisorDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(AdvisorDetail $advisorDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AdvisorDetail  $advisorDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdvisorDetail $advisorDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AdvisorDetail  $advisorDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdvisorDetail $advisorDetail)
    {
        //
    }
}
