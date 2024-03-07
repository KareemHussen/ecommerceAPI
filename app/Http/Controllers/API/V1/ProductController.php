<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::paginate();
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
        return $this->respondOk($product, 'product fetched successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) { 

            if(File::exists($product->image)) {
                File::delete($product->image);
            }   

            // If Changed Category i have to move the image to new Folder
            $file = $request->file('image');
            $name =  uniqid() . '.' . $file->extension();
            $file->storeAs('public/images/categories/'. $data['category_id'] . "/products/" , $name);
            $data['image'] = 'storage/images/categories/'.$data['category_id']. "/products/" .$name;
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
