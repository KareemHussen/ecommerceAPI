<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{

    private CategoryService $categoryService;
    
    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::whereNull('parent_id')->select("id" , "name" , "parent_id")->with('children:id,name,parent_id')->paginate();
        return $this->respondOk(CategoryResource::collection($categories), 'Categories fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if(!$this->categoryService->checkValidParent($data['parent_id'])){
            
            return $this->respondError("Can't create sub category under sub category");
        }

        $category = Category::create($request->validated());
        return $this->respondCreated(CategoryResource::make($category), 'Category created successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->respondOk(CategoryResource::make($category->load('children:id,name,parent_id')), 'Category fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        
        $data = $request->validated();

        if(!$this->categoryService->checkValidParent($data['parent_id'])){
            
            return $this->respondError("Can't create sub category under sub category");
        }

        $category->update($request->validated());
        return $this->respondOk($category, 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->respondNoContent();
    }
}
