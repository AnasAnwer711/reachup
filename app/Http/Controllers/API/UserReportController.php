<?php

namespace App\Http\Controllers\API;

use App\AdminNotification;
use App\Report;
use App\UserReport;
use Illuminate\Http\Request;
use App\Http\Resources\UserReport as UserReportResource;
use App\User;
use Illuminate\Support\Facades\Validator;

class UserReportController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            return $this->sendResponse(UserReportResource::collection(auth()->user()->reports), 'User reported retrieved successfully.');
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
                'report_id' => 'required|integer',
                'target_id' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }

            $input['source_id'] = auth()->user()->id;
            // if(Report::where('id',$input['report_id'])->whereNull('parent_id')->exists()){
            //     return $this->sendError('Inavlid report selected.');       
            // }
            if(UserReport::where('source_id',$input['source_id'])->where('report_id', $input['report_id'])->exists()){
                return $this->sendError('User already reported for this problem.');       
            }
    
            $user_report['user_report'] = UserReport::create($input);

            $target_name = '';
            $source_name = '';
            $reason = '';
            $source_user = User::find($input['source_id']);
            if($source_user){

                $source_name = $source_user->name;
            }

            $target_user = User::find($input['target_id']);
            if($target_user){

                $target_name = $target_user->name;
            }

            $report = Report::find($input['report_id']);
            if($report){
                $reason = $report->title;
            }
            $message = $target_name." has been reported by ".$source_name." against ".$reason;
            AdminNotification::create([
               'message' => $message, 
               'source_id' => $input['source_id'], 
               'source_id' => $source_name, 
               'target_id' => $input['target_id'], 
               'target_id' => $target_name, 
               'read' => 0, 
            ]);

            $users = User::where('user_type_id', 3)->all();

            // $emailTemplate = view('emails.password_reset',[ 'user' => $user,'token' => $token]);
            // $messagetemp = $emailTemplate->render();
            
            $subject = "User Reported";
            // Always set content-type when sending HTML email
            $headers[] = "MIME-Version: 1.0";
            $headers[] .= "Content-type:text/html;charset=UTF-8";
            
            // More headers
            $headers[] .= 'From: Reachup <no-reply@reachup.com>';
            foreach ($users as $key => $user) {
                $to = $user->email;
                mail($to,$subject,$message, implode("\r\n", $headers));
            }

            

            return $this->sendResponse(UserReportResource::collection($user_report), 'User reported successfully.');
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
     * @param  \App\UserReport  $userReport
     * @return \Illuminate\Http\Response
     */
    public function show(UserReport $userReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserReport  $userReport
     * @return \Illuminate\Http\Response
     */
    public function edit(UserReport $userReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserReport  $userReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserReport $userReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserReport  $userReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserReport $userReport)
    {
        //
    }
}
