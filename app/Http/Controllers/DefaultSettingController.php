<?php

namespace App\Http\Controllers;

use App\DefaultRule;
use App\DefaultSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class DefaultSettingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        if(Auth::check()){
            if(Auth::user()->user_type_id != 3){
                return redirect()->route('dashboard');
            }
        } 
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $settings = DefaultSetting::all();
        // return view('setting.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $setting = DefaultSetting::first();
        $rules = DefaultRule::get();
        $default_rules = $rules->where('rule_type', 'default');
        $user_before_cancel_rules = $rules->where('rule_type', 'cancel')->where('approximately', 'before')->where('action_by', 'user');
        $advisor_before_cancel_rules = $rules->where('rule_type', 'cancel')->where('approximately', 'before')->where('action_by', 'advisor');
        $user_after_cancel_rules = $rules->where('rule_type', 'cancel')->where('approximately', 'after')->where('action_by', 'user');
        $advisor_after_cancel_rules = $rules->where('rule_type', 'cancel')->where('approximately', 'after')->where('action_by', 'advisor');
        // dd($default_rules, $user_before_cancel_rules, $advisor_before_cancel_rules);
        // $c_key_exist =false;
        // $s_key_exist =false;
        $default_set =false;
        if($setting){
            // $c_key_exist =true;
            // $s_key_exist =true;
            $default_set = true;
        } 
        return view('setting.add', compact('setting', 'default_rules', 'user_before_cancel_rules', 'advisor_before_cancel_rules', 'user_after_cancel_rules', 'advisor_after_cancel_rules', 'default_set'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        //validation
        $validator = Validator::make($request->all(), [
            'session_hour' => 'required',
            'client_id' => 'required',
            'secret_id' => 'required',
        ]);
            
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }

        $input['client_id'] = Crypt::encrypt($request->client_id);
        $input['secret_id'] = Crypt::encrypt($request->secret_id);
        if(isset($input['client_check']))
            unset($input['client_check']);
        if(isset($input['secret_check']))
            unset($input['secret_check']);
        
        $setting = DefaultSetting::create($input);

        return redirect()->route('setting.create')->with('success', 'Settings Created Successfully');
    }

    public function default_percentage(Request $request)
    {

        //validation
        $validator = Validator::make($request->all(), [
            'platform_percentage' => 'required|integer',
            'advisor_percentage' => 'required|integer',
        ]);
        // dd($validator);
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        $total = intVal($request->platform_percentage) + intVal($request->advisor_percentage);
        if($total != 100){
            return back()->withErrors(['Percentage value not set propely']);    
        }

        DefaultRule::where('rule_type', 'default')->where('concern', 'platform')->update(['percentage'=> $request->platform_percentage]);
        DefaultRule::where('rule_type', 'default')->where('concern', 'advisor')->update(['percentage'=> $request->advisor_percentage]);
        return redirect()->route('setting.create')->with('success', 'Default rules updated successfully');

        
    }

    public function cancel_before_percentage(Request $request)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'user_user_percentage' => 'required|integer',
            'platform_user_percentage' => 'required|integer',
            'user_advisor_percentage' => 'required|integer',
            'advisor_advisor_percentage' => 'required|integer',
        ]);
        // dd($validator);
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        $total1 = intVal($request->user_user_percentage) + intVal($request->platform_user_percentage);
        if($total1 != 100){
            return back()->withErrors(['User percentage value not set propely']);    
        }
        $advisor_advisor_percentage = $request->advisor_advisor_percentage;
        if($advisor_advisor_percentage < 0) 
            $advisor_advisor_percentage = 0;
            
        $total2 = intVal($request->user_advisor_percentage) + intVal($advisor_advisor_percentage);
        if($total2 != 100){
            return back()->withErrors(['Advisor percentage value not set propely']);    
        }

        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'before')
        ->where('concern', 'user')
        ->where('action_by', 'user')
        ->update(['percentage'=> $request->user_user_percentage]);
        
        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'before')
        ->where('concern', 'platform')
        ->where('action_by', 'user')
        ->update(['percentage'=> $request->platform_user_percentage]);

        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'before')
        ->where('concern', 'user')
        ->where('action_by', 'advisor')
        ->update(['percentage'=> $request->user_advisor_percentage]);
        
        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'before')
        ->where('concern', 'advisor')
        ->where('action_by', 'advisor')
        ->update(['percentage'=> $request->advisor_advisor_percentage]);

        return redirect()->route('setting.create')->with('success', 'Cancel when more than 48 hours remaining rules updated successfully');

        
    }
    
    public function cancel_after_percentage(Request $request)
    {
        // dd($request->all());
        //validation
        $validator = Validator::make($request->all(), [
            'user_user_percentage' => 'required|integer',
            'platform_user_percentage' => 'required|integer',
            'advisor_user_percentage' => 'required|integer',
            'user_advisor_percentage' => 'required|integer',
            'advisor_advisor_percentage' => 'required|integer',
        ]);
        // dd($validator);
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        
        $total1 = intVal($request->user_user_percentage) + intVal($request->platform_user_percentage) + intVal($request->advisor_user_percentage);
        if($total1 != 100){
            return back()->withErrors(['User percentage value not set propely']);    
        }
        $advisor_advisor_percentage = $request->advisor_advisor_percentage;
        if($advisor_advisor_percentage < 0) 
            $advisor_advisor_percentage = 0;
            
        $total2 = intVal($request->user_advisor_percentage) + intVal($advisor_advisor_percentage);
        if($total2 != 100){
            return back()->withErrors(['Advisor percentage value not set propely']);    
        }

        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'after')
        ->where('concern', 'user')
        ->where('action_by', 'user')
        ->update(['percentage'=> $request->user_user_percentage]);
        
        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'after')
        ->where('concern', 'platform')
        ->where('action_by', 'user')
        ->update(['percentage'=> $request->platform_user_percentage]);
        
        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'after')
        ->where('concern', 'advisor')
        ->where('action_by', 'user')
        ->update(['percentage'=> $request->advisor_user_percentage]);

        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'after')
        ->where('concern', 'user')
        ->where('action_by', 'advisor')
        ->update(['percentage'=> $request->user_advisor_percentage]);
        
        DefaultRule::where('rule_type', 'cancel')
        ->where('approximately', 'after')
        ->where('concern', 'advisor')
        ->where('action_by', 'advisor')
        ->update(['percentage'=> $request->advisor_advisor_percentage]);

        return redirect()->route('setting.create')->with('success', 'Cancel when less than 48 hours remaining rules updated successfully');

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = DefaultSetting::findOrFail($id);
        return view('setting.edit', compact('setting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        //validation
        $validator = Validator::make($request->all(), [
            'session_hour' => 'required',
            'client_id' => 'required_if:client_check,1',
            'secret_id' => 'required_if:secret_check,1',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        // dd($input);
        if($input['client_check'])
            $input['client_id'] = Crypt::encryptString($request->client_id);
        if($input['secret_check'])
            $input['secret_id'] = Crypt::encryptString($request->secret_id);
        $setting = DefaultSetting::findOrFail($id);
        // if($input['client_check'] == '0')
        unset($input['client_check']);
        unset($input['secret_check']);
        // dd($input);
        $setting->fill($input)->save();
        return redirect()->route('setting.create')->with('success', 'Settings Updated Successfully');
        
    }

    public function additional_charges(Request $request, $id)
    {
        $input = $request->all();
        // validation
        $validator = Validator::make($input, [
            // 'is_additional_charges' => 'required',
            'title' => 'required_if:is_additional_charges,on',
            'description' => 'required_if:is_additional_charges,on',
            'percentage' => 'required_if:is_additional_charges,on',
        ]);

        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        $setting = DefaultSetting::findOrFail($id);
        // dd($input);
        if(isset($input['is_additional_charges']) && $input['is_additional_charges'] == 'on')
            $input['is_additional_charges'] = 1;
        else{
            $input['is_additional_charges'] = 0;
            $input['title'] = null;
            $input['description'] = null;
            $input['percentage'] = null;
        }
        // dd($input);

        $setting->fill($input)->save();
        return redirect()->route('setting.create')->with('success', 'Settings Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
