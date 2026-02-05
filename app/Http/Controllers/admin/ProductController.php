<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Price;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            'prices.*.price' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp'
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
                foreach ($request->prices as $key=> $p) {
                    $product->prices()->create([
                        'hours' => $p['hours'],
                        'price' => $p['price'],
                        'is_default' => $key == 0 ? 1 : 0,
                    ]);
                }
            }

            /* ---------- save images ---------- */
            if ($request->hasFile('images')) {

                foreach ($request->file('images') as $key => $file) {

                    $path = $file->store('products', 'public');

                    $product->images()->create([
                        'image_path' => $path,
                         
                        'is_default' => $key == 0 // first image default
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully');
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
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
        $request->validate([
            'name' => 'required',
            'prices.*.hours' => 'required|integer|min:1',
            'prices.*.price' => 'required|numeric|min:0'
        ]);

        DB::transaction(function () use ($request, $product) {

            /* ---------- update product ---------- */
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status ?? 1
            ]);

            /* ---------- refresh prices ---------- */
            $product->prices()->delete();

            if ($request->prices) {
                foreach ($request->prices as $key => $p) {
                    $product->prices()->create([
                        'hours' => $p['hours'],
                        'price' => $p['price'],
                        'is_default' => $key == 0 ? 1 : 0
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

            /* ---------- change default image ---------- */
            if ($request->default_image) {

                $product->images()->update(['is_default' => 0]);

                Image::where('id', $request->default_image)
                    ->update(['is_default' => 1]);
            }
        });

        return back()->with('success', 'Product updated successfully');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE PRODUCT
    |--------------------------------------------------------------------------
    */
    public function destroy(Product $product)
    {
        // delete images from storage
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SINGLE IMAGE (AJAX optional)
    |--------------------------------------------------------------------------
    */
    public function deleteImage($id)
    {
        $image = Image::findOrFail($id);

        Storage::disk('public')->delete($image->image_path);

        $image->delete();

        return back()->with('success', 'Image removed');
    }


    

  public function toggle($id)
{
    $product = Product::findOrFail($id);

    $product->update([
        'status' => !$product->status
    ]);

    return back()->with('success', 'Status updated');
}


}


