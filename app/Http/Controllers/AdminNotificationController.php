<?php

namespace App\Http\Controllers;

use App\AdminNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $admin_notifications = AdminNotification::all();
        AdminNotification::where('id', '>', 0)->update(['read'=>1]);
        // dd($categories[0]->revenue_generated);
        return view('admin_notification.index', compact('admin_notifications'));
    }

    public function show($id)
    {
        $admin_notification = AdminNotification::findOrFail($id);
        return view('admin_notification.view', compact('admin_notification'));
    }

    public function edit($id)
    {
        $admin_notification = AdminNotification::findOrFail($id);
        $admin_notification->update(['read'=>1]);
        return view('admin_notification.edit', compact('admin_notification'));
    }


    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            
            $validator = Validator::make($input, [
                'comments' => 'required',
                'resolve_by' => 'required',
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            $admin_notification = AdminNotification::findOrFail($id);
            // dd($input, $id, $input['resolve_by']);

            $targetted_user = User::find($admin_notification->target_id);
            if($targetted_user || $input['resolve_by'] == 'do_nothing'){

                if($input['resolve_by'] == 'delete_permanently')
                    $targetted_user->delete();
                else if($input['resolve_by'] == 'block')
                    $targetted_user->update(['status'=>'blocked']);
                
                $input['resolved'] = 1;
                $input['action_by'] = Auth::user()->id;
                $input['action_by_name'] = Auth::user()->name;
                $admin_notification->fill($input)->save();
    
                return redirect()->route('admin_notification.index')->with('success', 'Notification Resolved Successfully');
            } else {
                return redirect()->route('admin_notification.index')->with('error', 'User has been deleted already');

            }
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->route('admin_notification.index')->with('error', 'Error Occured');
        }
    }
}
