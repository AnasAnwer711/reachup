<?php

namespace App\Http\Controllers;

use App\AdvisorDetail;
use App\User;
use App\UserNotification;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function allUsers(Request $request)
    {
        // dd($request->all());
        $users = User::whereIn('user_type_id', [1, 2])->orderBy('created_at', 'DESC')->get();

        if ($request->ajax()) {

            return DataTables::of($users)
                ->addIndexColumn()
                // ->setRowAttr([
                //     'data-is-advisor' => function($users) {
                //         if($users->user_type_id == 2){
                //             return true;
                //         } else {
                //             return false;
                //         }
                //     },
                // ])
                ->setRowId(function ($users) {
                    return $users->id;
                })
                ->filter(function ($instance) use ($request) {
                    if (!empty($request->get('email'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return Str::contains($row['email'], $request->get('email')) ? true : false;
                        });
                    }

                    if (!empty($request->get('name'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['name']), Str::lower($request->get('name')))) {
                                return true;
                            }

                            return false;
                        });
                    }

                    if (!empty($request->get('user_type'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['user_type']), Str::lower($request->get('user_type')))) {
                                return true;
                            }

                            return false;
                        });
                    }
                    
                    if (!empty($request->get('status'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['status']), Str::lower($request->get('status')))) {
                                return true;
                            }

                            return false;
                        });
                    }

                    if (!empty($request->get('following_from'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['my_total_followings'] >= $request->get('following_from')){
                                return true;
                            }
                            return false;
                        });
                    }
                    if (!empty($request->get('following_to'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['my_total_followings'] <= $request->get('following_to')){
                                return true;
                            }
                            return false;
                        });
                    }
                    if (!empty($request->get('follower_from'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['my_total_followers'] >= $request->get('follower_from')){
                                return true;
                            }
                            return false;
                        });
                    }
                    if (!empty($request->get('follower_to'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['my_total_followers'] <= $request->get('follower_to')){
                                return true;
                            }
                            return false;
                        });
                    }


                    if (!empty($request->get('rating_from'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['avg_rating'] >= $request->get('rating_from')){
                                return true;
                            }
                            return false;
                        });
                    }

                    if (!empty($request->get('rating_to'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if($row['avg_rating'] <= $request->get('rating_to')){
                                return true;
                            }
                            return false;
                        });
                    }

                })
                ->editColumn('user_type', function ($users) {
                    return $users->user_type->name;
                })
                ->editColumn('status', function ($users) {
                    if ($users->status == 'active') {

                        $label = '<span class="label label-success">' . ucfirst($users->status) . '</span>';
                    } else {
                        $label = '<span class="label label-danger">' . ucfirst($users->status) . '</span>';
                    }
                    return $label;
                })

                ->editColumn('advisor_status', function ($users) {
                    $label = '';
                    if($users->user_type_id == 2){
                        if(isset($users->advisor)){
                            if ($users->advisor->status == 'active') {

                                $label = '<span class="label label-success">' . ucfirst($users->advisor->status) . '</span>';
                            } else {
                                $label = '<span class="label label-danger">' . ucfirst($users->advisor->status) . '</span>';
                            }
                        } else {
                            $label = '<span class="label label-info">Incomplete</span>';
                        }
                    }
                    return $label;
                    
                })
                ->editColumn('average_rating', function ($users) {
                    return $users->avg_rating;
                })
                ->editColumn('total_spent', function ($users) {
                    return '$'.number_format($users->total_spent, 2);
                })
                ->editColumn('total_followers', function ($users) {
                    return $users->my_total_followers;
                })

                ->editColumn('total_followings', function ($users) {
                    return $users->my_total_followings;
                })

                // ->addColumn('action', function ($row) {
                //     $btn = '';
                //     $btn .= '<a href="javascript:void(0)" data-toggle="modal"
                //     data-target="#notifyUserModal'.$row->id.'" data-notifyid="'.$row->id.'"><i class="fa fa-bell ml-2"></i></a>
                //     <a href="javascript:void(0)" data-toggle="modal"
                //         data-target="#userDetailModal'.$row->id.'">
                //         <i class="fa fa-eye ml-2"></i>
                //     </a>
                //     <a href="edit_user/'.$row->id.'">
                //         <i class="fa fa-pencil ml-2"></i>
                //     </a>';
                //     if($row->user_type_id == 2){
                //         if(isset($row->advisor)){
                //             $btn .= '<a href="javascript:void(0)" data-toggle="modal"
                //                 data-target="#reviewAdvisorModal'.$row->id.'">
                //                 <i class="fa fa-search-plus ml-2"></i>
                //             </a>';
                //         }
                //     }
                //     $btn .= '<a href="delete_user/'.$row->id.'" onclick="return deleteFunction();">
                //         <i class="fa fa-trash ml-2"></i>
                //     </a>';

                //     return $btn;
                // })
                // ->rawColumns(['status', 'advisor_status', 'action'])
                ->rawColumns(['status', 'advisor_status'])
                ->make(true);
        }

        return view('users.index', compact('users'));
    }

    public function edit_user($id)
    {
        $user = User::where('id', $id)->with('ratings', 'follows', 'followers')->first();
        return view('users.edit', compact('user'));
    }

    public function destroy($id)
    {
        // dd($id);
        $user = User::find($id);
        User::where('id', $id)->delete();
        if($user){

            // $emailTemplate = view('emails.account_blocked',[ 'user' => $user]);
            // $messagetemp = $emailTemplate->render();
        
            // $to = $user->email;
            // $subject = "Account Deleted";
            // // Always set content-type when sending HTML email
            // $headers[] = "MIME-Version: 1.0";
            // $headers[] .= "Content-type:text/html;charset=UTF-8";
            
            // // More headers
            // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
            // mail($to,$subject,$messagetemp, implode("\r\n", $headers));
            Mail::send('emails.account_blocked', ['user' => $user], function ($m) use ($user) {
                //$m->from('no_reply@botanicalgarden.patronassist.com', 'Museum');
                $m->to($user->email, $user->name)->subject('Account Deleted');
            });
        }
        return redirect()->route('allUsers')->with('success', 'User Deleted successfully');
        // return response()->json(['success' => true, 'message'=>'User Deleted Successfully']);
    }

    public function update_user_status(Request $request, $id)
    {
        // dd($request->all(), $id);
        $status = 'blocked';
        if (isset($request->status) && $request->status == 'on') {
            $status = 'active';
        }
        $user = User::find($id);
        if($user){

            User::where('id', $id)->update(['status' => $status]);
            if($status == 'blocked'){

                // $emailTemplate = view('emails.account_blocked',[ 'user' => $user]);
                // $messagetemp = $emailTemplate->render();
            
                // $to = $user->email;
                // $subject = "Account Blocked";
                // // Always set content-type when sending HTML email
                // $headers[] = "MIME-Version: 1.0";
                // $headers[] .= "Content-type:text/html;charset=UTF-8";
                
                // // More headers
                // $headers[] .= 'From: Reachup <no-reply@reachup.com>';
                // mail($to,$subject,$messagetemp, implode("\r\n", $headers));
                // dd('check Mail');
                Mail::send('emails.account_blocked', ['user' => $user], function ($m) use ($user) {
                    //$m->from('no-reply@reachup.com', 'Reachup');
                    $m->to($user->email, $user->name)->subject('Account Blocked');
                });
            }
            return redirect()->route('allUsers')->with('success', 'Status updated successfully');
        } else {
            return back()->withErrors('User not found');
        }
 
    }

    public function notify_user(Request $request, $id)
    {
        // dd($request->all());
        // dd(htmlspecialchars($request->email_content));
        try {
            $input = $request->all();
            if ($input['notify'] == 'notification') {

                $validator = Validator::make($input, [
                    'title' => 'required',
                    'message' => 'required',
                    'image' => 'sometimes|nullable|max:8192',
                    'type' => 'required',
                ],
                    [
                        // 'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg',
                    ]);
                // dd($validator->messages());
                if ($validator->fails()) {
                    return back()->withErrors($validator);

                }
                // dd($input);
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $name = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/notifications');
                    // dd($destinationPath);

                    $image->move($destinationPath, $name);
                    $input = $request->all();
                    $input['image'] = url('/') . '/notifications/' . $name;
                }

                $input['user_id'] = $id;
                $input['created_by'] = auth()->user()->id;
                unset($input['notify']);
                unset($input['subject']);
                unset($input['email_content']);
                $user = User::findOrFail($id);
                $notify = UserNotification::notification([$user->id], $input);
                // dd($notify);
                if ($notify) {
                    $notification['notification'] = UserNotification::create($input);

                    // User::where('id', $id)->update(['status' => $status]);
                    return redirect()->route('allUsers')->with('success', 'Notification sent successfully');
                } else {
                    return redirect()->route('allUsers')->with('error', 'Something went wrong');
                }
            } else {
                // dd($input);
                $validator = Validator::make($input, [
                    'subject' => 'required',
                    'email_content' => 'required'
                ]);
                // dd($validator->messages());
                if ($validator->fails()) {
                    return back()->withErrors($validator);
                }

                $file_path = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $name = time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('/notifications');
                    // dd($destinationPath);

                    $image->move($destinationPath, $name);
                    $input = $request->all();
                    $input['image'] = url('/') . '/notifications/' . $name;
                    $file_path = $input['image'];
                }

                // $html = htmlspecialchars($input['email_content']);
                // $html = $input['email_content'];
                // $message = $input['email_content'];
                $html = $input['email_content'];
                // dd($html);
                // $subject = 'Notification';
                $subject = $input['subject'];
                // Always set content-type when sending HTML email
                $headers[] = "MIME-Version: 1.0";
                // $headers[] .= "Content-type:text/html;charset=UTF-8";
                // $headers[] .= "Content-type: text/html; charset=iso-8859-1";

                // More headers
                // $headers[] .= 'X-Mailer: PHP/' . phpversion();
                $headers[] .= 'From: Reachup <no-reply@reachup.com>';
                $user = User::findOrFail($id);
                $to = $user->email;
                // $html = stripcslashes($message);
                // dd($to, $subject, $message, $headers);

                // if(mail($to,$subject,$message, implode("\r\n", $headers))){
                //     return redirect()->route('allUsers')->with('success', 'Your email has been sent successfully');
                // } else{
                //     return redirect()->route('allUsers')->with('error','Unable to send email. Please try again.');
                // }
                // dd($subject, $html, $to);
                if(\App::environment() == 'production'){

                    Mail::send([], [], function ($message) use ($subject, $html, $to, $file_path) {
                        $message->to($to);
                        $message->subject($subject);
                        
                        // ->attach($file_path)
                        
                        // here comes what you want
                        //   ->setBody('Hi, welcome user!'); // assuming text/plain
                        // or:
                        if ($file_path) {
                            $message->attach($file_path);
                        }
    
                        $message->setBody($html, 'text/html');
                         // for HTML rich messages
                    });
                }
                return redirect()->route('allUsers')->with('success', 'Your email has been sent successfully');


            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->route('allUsers')->with('error','Unable to send email. Please try again.');
        }

    }
   
    public function notify_multiple_users(Request $request)
    {
        // dd($request->all());
        $filter = $request['filter'];
        $input = $request['data'];

        // dd(isset($filter['email']));

        $qry = User::whereIn('user_type_id', [1, 2]);

        // dd($qry->get());
        if(isset($filter['name'])){
            $qry->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if(isset($filter['email'])){
            $qry->where('email', 'like', '%' . $filter['email'] . '%');
        }
        if(isset($filter['user_type'])){
            $user_type_id = 1;
            if($filter['user_type'] == 'advisor'){
                $user_type_id = 2;
            } 
            $qry->where('user_type_id', $user_type_id);
        }
        if(isset($filter['status'])){
            $qry->where('status',  $filter['status']);
        }

        // dd($filter);

        // if(isset($filter['following_from'])){
            
        //     $qry->whereHas('follows', function($qry) use ($filter){
        //         $qry->where('rate', '>=',  $filter['following_from'] );
        //     });
        // }

        // if(isset($filter['following_to'])){
        //     $qry->whereHas('follows', function($qry) use ($filter){
        //         $qry->where('rate', '<=',  $filter['following_to'] );
        //     });
        // }
        // if(isset($filter['follower_from'])){
            
        //     $qry->whereHas('followers', function($qry) use ($filter){
        //         $qry->where('rate', '>=',  $filter['follower_from'] );
        //     });
        // }

        // if(isset($filter['follower_to'])){
        //     $qry->whereHas('followers', function($qry) use ($filter){
        //         $qry->where('rate', '<=',  $filter['follower_to'] );
        //     });
        // }
        if(isset($filter['rating_from'])){
            
            $qry->whereHas('ratings', function($qry) use ($filter){
                $qry->where('rate', '>=',  $filter['rating_from'] );
            });
        }

        if(isset($filter['rating_to'])){
            $qry->whereHas('ratings', function($qry) use ($filter){
                $qry->where('rate', '<=',  $filter['rating_to'] );
            });
        }


        // dd($qry->toSql());
        $users = $qry->get();

        $fusers=[];
        // dd(isset($filter['following_from']));
        foreach ($users as $key => $user) {
            // dd('in');
            // dd($user->my_total_followings >= $filter['following_from']);
            if(isset($filter['following_from']) && isset($filter['following_to'])){
                // dd($user->my_total_following, $filter['following_from']);
                if($user->my_total_followings >= $filter['following_from'] && $user->my_total_followings <= $filter['following_to']){
                    $fusers[] = $user;
                }
            }

            // if(isset($filter['following_to'])){
            //     // dd($user->my_total_followings <= $filter['following_to']);

            //     if($user->my_total_followings <= $filter['following_to']){
            //         // dd($user);
            //         $fusers[] = $user;
            //     }
            // }

            if(isset($filter['follower_from']) && isset($filter['follower_to'])){

                if($user->my_total_followers >= $filter['follower_from'] && $user->my_total_followers <= $filter['follower_to']){
                    $fusers[] = $user;
                }
            }

            // if(isset($filter['follower_to'])){

            //     if($user->my_total_followers <= $filter['follower_to']){
            //         $fusers[] = $user;
            //     }
            // }
        }
        if(count($fusers)){
            $users = $fusers;
        }
        // dd($users);
        // dd('out');
            //handle Extra 0 param coming from frontend
            unset($input['0']);
            $input['created_by'] = auth()->user()->id;
            $user_ids = [];
            foreach ($users as $user) {
                // dd($user);
                $input['user_id'] = $user->id;
                // dd($input);
                // $notification['notification'] = UserNotification::create($input);
                $user_ids[] = $user->id;
            }

            $notify = UserNotification::notification($user_ids, $input);
            // dd($notify);

            // $input['user_id'] = $id;
            
            // dd($notify);
            // $user = User::findOrFail($id);
            // $notify = UserNotification::notification([$user->id], $input);
            if ($notify) {
            //    dd('in');
                return response()->json(['success'=>true, 'message'=> 'Notification sent successfully']);
                // return redirect()->route('allUsers')->with('success', 'Notification sent successfully');
            } else {
                return redirect()->route('allUsers')->with('error', 'Something went wrong');
            }

    }

    public function update_advisor_status(Request $request, $id)
    {
        // dd($request->all(), $id);
        $user = User::findOrFail($id);
        if (isset($user->advisor)) {

            AdvisorDetail::where('id', $user->advisor->id)->update(['status' => $request->advisor_status]);
            return redirect()->route('allUsers')->with('success', 'Advisor reviewed successfully');
        } else {
            return redirect()->route('allUsers')->with('error', 'Something went wrong');

        }
    }
}
