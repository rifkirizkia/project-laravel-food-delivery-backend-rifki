<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    //index
    public function index(Request $request){
        // Get product by request user id
        $products = Product::with('user')->where('user_id', $request->user_id)->get();
        // $products = Product::with('user')->get();

        // Return the products as a JSON response
        return response()->json([
            'status' =>'success',
            'message' => 'Products retrieved successfully',
            'data' => $products
        ], 200);
    }
    //get product by user id
    public function getProductByUserId(Request $request){
        $products = Product::with('user')->where('user_id', $request->user_id)->get();

        return response()->json([
            'status' =>'success',
           'message' => 'Products retrieved successfully',
           'data' => $products
        ]);
    }
    //store product
    public function store(Request $request){
        // Validate the request data
        $request->validate([
            'name' =>'required',
            'description' =>'required',
            'price' =>'required|numeric',
            'is_available' =>'required|boolean',
            'is_favorite' =>'required|boolean',
            'image' =>'required|image',
        ]);
        $user = $request->user();
        $request->merge(['user_id' => $user->id]);
        $data = $request->all();

        $data = $request->all();
        $product = Product::create($data);
        //check if image is available
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time(). '.'. $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $product->image = $imageName;
            $product->save();
        }
        // Return the created product as a JSON response
        return response()->json([
           'status' =>'success',
           'message' => 'Product created successfully',
            'data' => $product
        ]);
    }
    //update product
    public function update(Request $request, $id){
        // Validate the request data
        $request->validate([
            'name' =>'required',
            'description' =>'required',
            'price' =>'required|integer',
            'stock' =>'required|integer',
            'is_available' =>'required|boolean',
            'is_favorite' =>'required|boolean',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
               'status' => 'error',
               'message' => 'Product not found'
            ], 404);
        }

        $data = $request->all();
        $product->update($data);
        // Return the updated product as a JSON response
        return response()->json([
           'status' =>'success',
           'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }
    //delete product
    public function destroy($id){
        // Find the product by ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
               'status' => 'error',
               'message' => 'Product not found'
            ], 404);
        }

        // Delete the product
        $product->delete();

        // Return a success response
        return response()->json([
           'status' =>'success',
           'message' => 'Product deleted successfully'
        ]);
    }
}
