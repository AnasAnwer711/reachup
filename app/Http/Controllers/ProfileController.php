<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile.index');
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
        //
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
        //
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
        $user->fill($input)->save();
        return redirect()->route('profile.index')->with('success', 'Profile Updated Successfully');
    }

    public function update_password(Request $request, $id)
    {
        //validation
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|alphaNum|min:6',
            'new_password' => 'required|confirmed|alphaNum|min:6',
        ]);
            
            
        if($validator->fails()){
            return back()->withErrors($validator);    
        }

        $user = Auth::user();

        if(Hash::check($request->old_password, $user->password)){
            User::where('id', auth()->user()->id)->update(['password'=> bcrypt($request->new_password)]);
            return redirect()->route('profile.index')->with('success', 'Password Changed Successfully');

        } else {
            return redirect()->route('profile.index')->with('error', 'Your old password did not matched');
        }
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
