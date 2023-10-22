<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reportings = Report::get();
        return view('reporting.index', compact('reportings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_reportings = Report::whereNull('parent_id')->get();
        return view('reporting.add', compact('parent_reportings'));
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
                'title' => 'required|unique:reports,title',
                'parent_id' => 'nullable|sometimes|numeric',
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            if(isset($request->parent_id) && !is_null($request->parent_id)){
                $find_parent = Report::find($request->parent_id);
                
                if(!$find_parent)
                    return back()->with('error', 'Select valid parent report to make sub report');                     
                    
            }
            // dd($input);
            Report::create($input);
            return redirect()->route('reporting.index')->with('success', 'Reporting Created Successfully');
        } catch (\Throwable $th) {
            return redirect()->route('reporting.index')->with('error', 'Error Occured');
        }
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
        $reporting = Report::findOrFail($id);
        $parent_reportings = Report::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('reporting.edit', compact('reporting', 'parent_reportings'));
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
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'title' => 'required|unique:reports,title,'.$id,
                'parent_id' => 'nullable|sometimes|numeric',
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            if(isset($request->parent_id) && !is_null($request->parent_id)){
                $find_parent = Report::find($request->parent_id);
                
                if(!$find_parent)
                    return back()->with('error', 'Select valid parent report to make sub report');                     
                    
            }
            $report = Report::findOrFail($id);
            
            $report->fill($input)->save();

            return redirect()->route('reporting.index')->with('success', 'Reporting Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->route('reporting.index')->with('error', 'Error Occured');
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
        $report = Report::findOrFail($id);
        if(count($report->sub_reportings) < 1){
            // dd('in');
            $report->delete();
            return response()->json(['success' => true, 'message'=>'Report Deleted Successfully']);
        } else {
            // dd('in');
            return response()->json(['success' => false, 'message'=>'Unable to delete parent report. Delete dependent categories first']);

        }
    }
}
