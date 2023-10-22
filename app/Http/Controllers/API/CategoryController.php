<?php

namespace App\Http\Controllers\API;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Resources\Category as CategoryResource;
use App\Http\Resources\CategoryKeyword as CategoryKeywordResource;
use Illuminate\Support\Facades\Validator;


class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // $data['categories'] = Category::whereNull('parent_id')->select('id', 'title', 'image', 'parent_id')->with(array('sub_categories'=>function($query) {
            //     $query->select('id','title', 'image', 'parent_id');
            // }))->get();
            $data['categories'] = CategoryResource::collection(Category::whereNull('parent_id')->get());
            
            // dd($data);
            return $this->sendResponse($data, 'Categories retrieved successfully.');
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
                'title' => 'required|unique:categories,title',
                'parent_id' => 'nullable|sometimes|numeric',
                'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:8192',
            ],
            [
                'image.mimes' => 'The image must be file of type jpeg,png,jpg,gif,svg'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors()->first());       
            }
            if(isset($request->parent_id) && !is_null($request->parent_id)){
                $find_parent = Category::find($request->parent_id);
                
                if(!$find_parent)
                    return $this->sendError('Select valid parent category to make sub category.');       
                    
                    
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
            
            $category = Category::create($input);
            
            return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
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
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        try {
            $data['category'] = $category;
            return $this->sendResponse(CategoryResource::collection($data), 'Category retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //
    }

    public function sub_category(Category $category)
    {
        // dd($category);
        try {
            $data['sub_category'] = CategoryResource::collection(Category::where('parent_id', $category->id)->get());
            
            return $this->sendResponse($data, 'Sub categories retrieved successfully.');
        } catch (\Throwable $th) {
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
        }
    }

    public function category_keywords(Category $category)
    {
        // dd($category);
        try {
            if($category){
                $data = [];
                if(count($category->category_keywords) > 0){
                    $data['category_keywords'] = CategoryKeywordResource::collection($category->category_keywords);
                }
                return $this->sendResponse($data, 'Cateogry keywords retrieved successfully.');
            }
        } catch (\Throwable $th) {
            // dd($th);
            if(\App::environment() == 'production'){
                return $this->sendError('Error Occured');
            } else {
                return $this->sendError('Error Occured', $th->getMessage());
            }  
        }
    }
}
