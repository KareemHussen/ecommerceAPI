<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Models\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexProductRequest $request)
    {
        $data = $request->validated();

        $query = Product::isLive(true);

        $query->when(isset($data['query']) , function($query) use($data){
           $query->where('name' , 'like' , '%'.$data['query'].'%'); 
        });

        $query->when(isset($data['sort_by']) , function($query) use($data){
            if($data['asc']){
                $query->orderBy($data['sort_by']);
            } else{
                $query->orderByDesc($data['sort_by']);
            }
        });

        $products = $query->paginate($data['per_page'] ?? 15);


        return $this->respondOk($products, 'Products fetched successfully');
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = $request->user->id;

        if ($request->hasFile('image')) { 
            $file = $request->file('image');
            $name =  uniqid() . '.' . $file->extension();
            $file->storeAs('public/images/categories/'. $data['category_id'] . "/products/" , $name);
            $data['image'] = 'storage/images/categories/'. $data['category_id'] . "/products/" .$name;
        } 

        $product = product::create($data);
        return $this->respondCreated($product, 'product created successfully');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (!$product->live) {
            return $this->respondNotFound('Product not found.');
        }
        return $this->respondOk($product, 'product fetched successfully');
    }

    #TODO("Optimize -------------------------------------------------------------------------------------------------------------")
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $category_id = $data['category_id'] ?? $product->category_id;

        if ($request->hasFile('image') && isset($data['category_id'])) {
            

            if(File::exists($product->image)) {
                File::delete($product->image);
            }   

            $file = $request->file('image');
            $name =  uniqid() . '.' . $file->extension();
            $file->storeAs('public/images/categories/'. $category_id . "/products/" , $name);
            $data['image'] = 'storage/images/categories/'.$category_id. "/products/" .$name;

        } else if (!$request->hasFile('image') && isset($data['category_id']) ) {
            
            // move image to new folder

            if(File::exists($product->image)) {
                $name = basename($product->image);
                $destinationDirectory = 'storage/images/categories/'. $data['category_id'] . '/products/';

                // Destination file path
                $destinationFilePath = $destinationDirectory . $name;

                // Create the destination directory if it does not exist
                if (!File::exists($destinationDirectory)) {
                    File::makeDirectory($destinationDirectory, 0755, true);
                }


                File::move($product->image, $destinationFilePath);
                $data['image'] = 'storage/images/categories/'.$data['category_id']. "/products/" .$name;

            }   

        } 

        $product->update($data);
        return $this->respondOk($product, 'product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if(File::exists($product->image)) {
            File::delete($product->image);
        }   
        
        $product->delete();
        return $this->respondNoContent();
    }
}
