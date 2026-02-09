<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Price;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST PRODUCTS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $products = Product::with(['images', 'prices'])
            ->latest()
            ->paginate(10);

        return view('admin.pages.products', compact('products'));
    }



    /*
    |--------------------------------------------------------------------------
    | CREATE PAGE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.products.create');
    }


    /*
    |--------------------------------------------------------------------------
    | STORE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'prices.*.hours' => 'required|integer|min:1',
            'prices.*.price' => 'required|numeric|min:0|max:10000',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp'
        ], [

            //  custom messages
            'name.required' => 'Product name is required',

            'prices.*.hours.required' => 'Hours is required',
            'prices.*.hours.integer' => 'Hours must be a number',
            'prices.*.hours.min' => 'Hours must be at least 1',

            'prices.*.price.required' => 'Price is required',
            'prices.*.price.numeric' => 'Price must be a valid number',
            'prices.*.price.min' => 'Price cannot be negative',
            'prices.*.price.max' => 'Price cannot exceed $10,000',

            'images.*.image' => 'Only images are allowed',
            'images.*.mimes' => 'Allowed formats: jpg, jpeg, png, webp',
        ], [

            //  friendly field names (removes prices.0.price ugly text)
            'prices.*.price' => 'Price',
            'prices.*.hours' => 'Hours',
            'images.*' => 'Image'
        ]);


        DB::transaction(function () use ($request) {

            /* ---------- create product ---------- */
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => 1
            ]);

            /* ---------- save prices ---------- */
            if ($request->prices) {
                $defaultPrice = $request->default_price ?? 0; // Get selected default or use 0

                foreach ($request->prices as $key => $p) {
                    $product->prices()->create([
                        'hours' => $p['hours'],
                        'price' => round((float)$p['price'], 2),

                        'is_default' => $key == $defaultPrice ? 1 : 0,
                    ]);
                }
            }

            /* ---------- save images ---------- */
            if ($request->hasFile('images')) {
                $defaultImage = $request->default_image ?? 0; // Get selected default or use 0

                foreach ($request->file('images') as $key => $file) {

                    $path = $file->store('products', 'public');

                    $product->images()->create([
                        'image_path' => $path,
                        'is_default' => $key == $defaultImage ? 1 : 0
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE (Returns JSON for modal)
    |--------------------------------------------------------------------------
    */
    public function edit(Product $product)
    {
        // eager load relations
        $product->load([
            'prices:id,product_id,price,hours,is_default',
            'images:id,product_id,image_path,is_default'
        ]);

        return response()->json($product);
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'prices.*.hours' => 'required|integer|min:1',
            'prices.*.price' => 'required|numeric|min:0|max:10000',
        ], [
            'name.required' => 'Product name is required',

            'prices.*.hours.required' => 'Hours is required',
            'prices.*.hours.min' => 'Hours must be at least 1',

            'prices.*.price.required' => 'Price is required',
            'prices.*.price.numeric' => 'Price must be a number',
            'prices.*.price.max' => 'Price cannot exceed $10,000',
            'prices.*.price.min' => 'Price cannot be negative',
        ]);


        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                // ->with('open_modal', 'edit')
                ->with('edit_product_id', $product->id); // 👈 send id
        }

        DB::transaction(function () use ($request, $product) {

            /* ---------- update product ---------- */
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            /* ---------- refresh prices ---------- */
            $product->prices()->delete();

            if ($request->prices) {
                $defaultPrice = $request->default_price ?? 0; // Get selected default or use 0

                foreach ($request->prices as $key => $p) {
                    $product->prices()->create([
                        'hours' => $p['hours'],
                        'price' => round((float)$p['price'], 2),

                        'is_default' => $key == $defaultPrice ? 1 : 0
                    ]);
                }
            }

            /* ---------- upload new images ---------- */
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $file) {

                    $path = $file->store('products', 'public');

                    $product->images()->create([
                        'image_path' => $path,
                        'is_default' => 0
                    ]);
                }
            }

            /* ---------- change default image for existing images ---------- */
            if ($request->default_image_existing) {

                $product->images()->update(['is_default' => 0]);

                Image::where('id', $request->default_image_existing)
                    ->update(['is_default' => 1]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */
    // public function destroy(Product $product)
    // {
    //     // delete images from storage
    //     foreach ($product->images as $img) {
    //         Storage::disk('public')->delete($img->image_path);
    //     }

    //     $product->delete();

    //     return back()->with('success', 'Product deleted successfully');
    // }


    /*
    |--------------------------------------------------------------------------
    | DELETE SINGLE IMAGE (AJAX)
    |--------------------------------------------------------------------------
    */
    public function deleteImage($id)
    {
        $image = Image::findOrFail($id);

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return response()->json(['success' => true]);
    }




    /*
    |--------------------------------------------------------------------------
    | TOGGLE STATUS
    |--------------------------------------------------------------------------
    */
    public function toggle($id)
    {
        $product = Product::findOrFail($id);

        $product->update([
            'status' => !$product->status
        ]);

        return back()->with('success', 'Status updated');
    }
}
