<?php

namespace App\Http\Controllers;

use App\User;
use App\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        if(Auth::check()){
            if(Auth::user()->user_type_id != 3){
                return redirect()->route('dashboard');
            }
        } 
    }



    public function index()
    {
        $users = User::where('user_type_id', 3)->where('is_superadmin', 0)->get();
        return view('admin.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.add');
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
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'username' => 'required|unique:users,username|regex: /^[a-zA-Z0-9_]*$/',
            'password' => 'required|confirmed|alphaNum|min:6',
            'full_number' => 'required|numeric',
            'selected_country' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:8192',
        ],
        [
            'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
        ]);
            
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }
        // dd($input);

        // $prefix = substr($request->phone, 0, 2);
        // if ($prefix == "65"){
        //     // dd('in Singapore');
        // }else if (substr($prefix, 0, 1) == 1){
        //     // dd('in America');
        // }else{
        //     return back()->withErrors('Phone number must starts with 65 for SG or 1 for US');    
        // }

        //store
        $image = $request->file('image');
        $name = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path('/images');
        // dd(public_path);
        
        $image->move($destinationPath, $name);
        $input = $request->all();
        $input['image'] = url('/').'/images/'.$name;
        // dd($input);
        $input['password'] = bcrypt($input['password']);
        $input['phone'] = $input['full_number'];
        $input['phone_code'] = $input['selected_country'];
        $input['user_type_id'] = UserType::where('name', 'admin')->first()->id ?? null;
        unset($input['password_confirmation']);
        unset($input['selected_country']);
        // unset($input['phone']);
        unset($input['full_number']);
        // unset($request->confirm_password);
        $user = User::create($input);
        // $user = User::create($request->all());

        return redirect()->route('admin.index')->with('success', 'Admin Created Successfully');

        // dd($request->all());
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
        // dd($id);
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
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
        // dd($input);
        //validation
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'username' => 'required|regex: /^[a-zA-Z0-9_]*$/|unique:users,username,'.$id,
            'phone' => 'required',
            'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif,svg|max:8192',

        ],
        [
            'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
        ]);
            
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }

        $user = User::where('id', $id)->first();

        if($request->hasFile('image'))
        {
            // dd($user->image);
            $usersImage = public_path(parse_url($user->image)['path']);
            // $usersImage = public_path("images/{$user->image}"); // get previous image from folder
            if (File::exists($usersImage)) { // unlink or remove previous image from folder
                unlink($usersImage);
            }
            // dd($user);
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            // dd(public_path);
    
            $image->move($destinationPath, $name);
            $input['image'] = url('/').'/images/'.$name;
        }
        $input['phone'] = $input['full_number'];
        $input['phone_code'] = $input['selected_country'];
        unset($input['selected_country']);
        unset($input['full_number']);
        $user->fill($input)->save();
        return redirect()->route('admin.index')->with('success', 'Admin Updated Successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id);
        User::where('id', $id)->delete();
        return response()->json(['success' => true, 'message'=>'Admin Deleted Successfully']);

    }
}
