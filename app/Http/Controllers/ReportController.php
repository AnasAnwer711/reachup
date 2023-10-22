<?php

namespace App\Http\Controllers;

use App\UserReachup;
use App\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function users()
    {
        $users = User::where('user_type_id', 1)->get();
        // $professionals = User::where('user_type_id', 2)->get();
        // dd($professionals);
        return view('report.users', compact('users'));
    }

    public function professionals()
    {
        $professionals = User::where('user_type_id', 2)->get();
        // $professionals = User::where('user_type_id', 2)->get();
        // dd($professionals);
        return view('report.professionals', compact('professionals'));
    }
    
    public function reachups()
    {
        $reachups = UserReachup::all();
        // $professionals = User::where('user_type_id', 2)->get();
        // dd($professionals);
        return view('report.reachups', compact('reachups'));
    }
}
