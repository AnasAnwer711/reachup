<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryKeyword;
use App\Notifications\Slack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('parent_category', 'user_interest_categories')->get();
        // dd($categories[0]->revenue_generated);
        return view('category.index', compact('categories'));
    }

    public function get_categories()
    {
        $categories = Category::with('parent_category', 'user_interest_categories')->whereNull('parent_id')->get();
        // dd($categories[0]->revenue_generated);
        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parent_categories = Category::whereNull('parent_id')->get();
        return view('category.add', compact('parent_categories'));
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
            // dd($input);
            $validator = Validator::make($input, [
                'title' => 'required|unique:categories,title',
                'parent_id' => 'nullable|sometimes|numeric',
                // 'parent_id' => 'required_if:category,child',
                'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            if(isset($request->parent_id) && !is_null($request->parent_id)){
                $find_parent = Category::find($request->parent_id);
                
                if(!$find_parent)
                    return back()->with('error', 'Select valid parent category to make sub category');                     
                    
            }
            // dd($input);
            // $input['user_id'] = auth()->user()->id;
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/categories');
            // dd($name);
            
            $image->move($destinationPath, $name);
            $input = $request->all();
            $input['image'] = url('/').'/categories/'.$name;
            
            // $input['image'] = $name;
            $keywordsInput = $input['keywords'];
            $keywords = explode(",",$keywordsInput);
            // if(isset($input['parent_id']) && $input['category'] == 'parent')
            //     unset($input['parent_id']);
            // unset($input['category']);
            unset($input['keywords']);
            // dd($keywords);
            $category = Category::create($input);
            foreach ($keywords as $key => $value) {
                CategoryKeyword::create([
                    'category_id' => $category->id,
                    'keyword'=> $value
                ]);
            }
            return redirect()->route('category.index')->with('success', 'Category Created Successfully');
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->route('category.index')->with('error', 'Error Occured');
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
        $category = Category::findOrFail($id);
        $parent_categories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        $catKeywords = $category->category_keywords;
        $keywords = [];
        $category_keywords = "";
        if(count($catKeywords) > 0){
            foreach ($catKeywords as $key => $value) {
                $keywords[] = $value->keyword;
            }
            // dd($keywords);
            $category_keywords = implode(",",$keywords);
            // dd($category_keywords);
        }

        // dd('out');
        return view('category.edit', compact('category', 'parent_categories', 'category_keywords'));
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
                'title' => 'required|unique:categories,title,'.$id,
                'parent_id' => 'nullable|sometimes|numeric',
                'image' => 'sometimes|nullable|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
            
            if($validator->fails()){
                return back()->withErrors($validator);      
            }
            // dd($input);
            if(isset($input['parent_id']) && $input['category'] == 'parent')
                $input['parent_id'] = null;

            if(isset($request->parent_id) && !is_null($request->parent_id)){
                $find_parent = Category::find($request->parent_id);
                
                if(!$find_parent)
                    return back()->with('error', 'Select valid parent category to make sub category');                     
                    
            }
            $category = Category::findOrFail($id);
            if($request->hasFile('image'))
            {
                $existingImage = public_path(parse_url($category->image)['path']);
                if (File::exists($existingImage)) { // unlink or remove previous image from folder
                    unlink($existingImage);
                }
                $image = $request->file('image');
                $name = time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/categories');
        
                $image->move($destinationPath, $name);
                $input['image'] = url('/').'/categories/'.$name;
            }

            $keywordsInput = $input['keywords'];
            $keywords = explode(",",$keywordsInput);
            
            unset($input['keywords']);
            unset($input['category']);
            // dd($keywords);
            CategoryKeyword::where('category_id', $id)->delete();
            foreach ($keywords as $key => $value) {
                CategoryKeyword::create([
                    'category_id' => $id,
                    'keyword'=> $value
                ]);
            }
           
            $category->fill($input)->save();

            return redirect()->route('category.index')->with('success', 'Category Updated Successfully');
        } catch (\Throwable $th) {
            return redirect()->route('category.index')->with('error', 'Error Occured');
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
        $category = Category::findOrFail($id);
        // dd($category->category_interests);
        if(!$category->have_subcategories){
            if($category->category_interests > 0){
                return response()->json(['success' => false, 'message'=>'Unable to delete category. Users already associated with this category']);
            }
            $category->delete();
            return response()->json(['success' => true, 'message'=>'Category Deleted Successfully']);
        } else {
            return response()->json(['success' => false, 'message'=>'Unable to delete parent category. Delete dependent categories first']);

        }

    }
}
