<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\ShowAdminCategoryRequest;
use App\Http\Requests\Category\ShowCategoryRequest;
use App\Models\Category;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Product;
use App\Services\CategoryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

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
        $categories = Category::whereNull('parent_id')->select("id" , "name")->with('children:id,name,parent_id')->get();
        return $this->respondOk(CategoryResource::collection($categories), 'Categories fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();


        if(isset($data['parent_id']) && !$this->categoryService->checkValidParent($data['parent_id'])){
            
            return $this->respondError("Can't create sub category under sub category");
        }

        $category = Category::create($request->validated());
        return $this->respondCreated(CategoryResource::make($category), 'Category created successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category , ShowCategoryRequest $request)
    {
        $data = $request->validated();
        $perPage = $data['per_page'] ?? 15;

        $query = Product::query()->isLive(true)->where('category_id' , $category->id)->with('main_image');

        $query->when(isset($data['query']) , function($query) use($data){
            $query->where('name' , 'like' , '%'.$data['query'].'%'); 
         })
         ->when(isset($data['is_offer']) , function($query) use($data){
             $query->where('special_offer' , '>' , Carbon::now());
         })
         ->when(isset($data['is_daily_offer']) , function($query) use($data){
             $query->where('daily_offer' , '>' , Carbon::now());
         })
         ->when(isset($data['sort_by']) , function($query) use($data){
             if($data['asc']){
                 $query->orderBy($data['sort_by']);
             } else{
                 $query->orderByDesc($data['sort_by']);
             }
         });

        $category->products = $query->paginate($perPage);

        return $this->respondOk(CategoryResource::make($category), "Category fetched successfully with it's products");
    }

    /**
     * Display the specified resource for admin.
     */
    public function show_admin(Category $category , ShowAdminCategoryRequest $request)
    {
        $data = $request->validated();
        $perPage = $data['per_page'] ?? 15;

        $query = Product::query()->where('category_id' , $category->id)->with('main_image');

        $query->when(isset($data['live']) , function($query) use($data){
            $query->isLive($data['live']);
         })->when(isset($data['query']) , function($query) use($data){
            $query->where('name' , 'like' , '%'.$data['query'].'%'); 
         })
         ->when(isset($data['is_offer']) , function($query) use($data){
             $query->where('special_offer' , '>' , Carbon::now());
         })
         ->when(isset($data['is_daily_offer']) , function($query) use($data){
             $query->where('daily_offer' , '>' , Carbon::now());
         })
         ->when(isset($data['sort_by']) , function($query) use($data){
             if($data['asc']){
                 $query->orderBy($data['sort_by']);
             } else{
                 $query->orderByDesc($data['sort_by']);
             }
         });

        $category->products = $query->paginate($perPage);

        return $this->respondOk(CategoryResource::make($category), "Category fetched successfully with it's products");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        
        $data = $request->validated();


        if(isset($data['parent_id']) && $category->id == $data['parent_id']){
            return $this->respondError("Category can't be a sub category of itself");
        }

        if(isset($data['parent_id']) && !$this->categoryService->checkValidParent($data['parent_id'])){
            
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
        $path = "storage/images/categories/". $category->id;
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        $category->delete();


        return $this->respondNoContent();
    }
}
