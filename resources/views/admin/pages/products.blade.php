@extends('admin.layouts.admin')

@section('title', 'Products')

@section('content')

<div class="pc-container">
    <div class="pc-content">
<!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Products</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Management</a></li>
                            <li class="breadcrumb-item" aria-current="page">Products</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <div class="page-header">
            <div class="page-block">
                <h5 class="m-b-10">Products</h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">All Products</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
        + Add Product
    </button>
</div>

           <div class="card-body table-responsive">

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Prices</th>
                <th>Status</th>
                <th width="150">Action</th>
            </tr>
        </thead>

        <tbody>
        @forelse($products as $product)
            <tr>

                {{-- Serial --}}
                <td>{{ $products->firstItem() + $loop->index }}</td>

                {{-- Image --}}
                <td width="90">
                    @php
                        $img = $product->images->where('is_default',1)->first();
                    @endphp

                    @if($img)
                        <img src="{{ asset('storage/'.$img->image_path) }}"
                             style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
                    @else
                        <span class="text-muted small">No Image</span>
                    @endif
                </td>

                {{-- Name --}}
                <td>
                    <strong>{{ $product->name }}</strong>
                </td>

                {{-- Description --}}
                <td style="max-width:220px;">
                    <small>{{ Str::limit($product->description, 80) }}</small>
                </td>

                {{-- Prices --}}
                <td>
                    @foreach($product->prices as $price)
                        <div style="color:{{ $price->is_default ? 'green' : '' }}; font-weight:{{ $price->is_default ? 'bold' : 'normal' }};">
                            $ {{ $price->price }}
                            <small class="text-muted ">/ {{ $price->hours }} hrs</small>
                        </div>
                    @endforeach
                </td>

                {{-- Status --}}
               <td>
                    <form action="{{ route('admin.products.toggle', $product->id) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('PATCH')

                        <div class="form-check form-switch m-0">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                onchange="this.form.submit()"
                                {{ $product->status ? 'checked' : '' }}
                                style="cursor:pointer; width:45px; height:22px;"
                            >
                        </div>
                    </form>
                </td>


                {{-- Actions --}}
                <td style="  gap:20px; display:flex; align-items:center;">
                    <button onclick="editProduct({{ $product->id }})"
                      class="text-primary fs-5"
                      style="background:none;border:none;cursor:pointer;">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                     
                    {{-- <form action="{{ route('admin.products.destroy', $product->id) }}" 
                            method="POST" 
                            style="display:inline-block"
                            onsubmit="return confirm('Delete this product?')">

                          @csrf
                          @method('DELETE')

                     <button type="submit" 
                              style="background:none;border:none;color:#ec4141;font-size:18px;cursor:pointer;">
                          <i class="bi bi-trash-fill"></i>
                      </button>

                      </form> --}}
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">
                    No products found
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>

</div>

            
        </div>
     
    </div>
</div>


<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
         @if ($errors->any())
            <div class="alert alert-danger mx-3 mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="modal-body">

          <div class="row g-3">

            {{-- Product Name --}}
            <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            {{-- Description --}}
            <div class="col-md-6">
              <label class="form-label">Description</label>
              <input type="text" name="description" class="form-control">
            </div>


            {{-- Prices --}}
            <div class="col-12">
              <label class="form-label">Prices & Hours</label>

              <div id="priceWrapper">

                <div class="row g-2 mb-2 priceRow">
                  <div class="col-md-1">
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="radio" name="default_price" value="0" checked>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <input type="number" step="0.01" name="prices[0][price]" class="form-control" placeholder="Price">
                  </div>

                  <div class="col-md-4">
                    <input type="number" name="prices[0][hours]" class="form-control" placeholder="Hours">
                  </div>

                  <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100 addPrice">+</button>
                  </div>
                </div>

              </div>
            </div>


            {{-- Images --}}
           <div class="col-md-12">
    <label class="form-label">Product Images</label>

    <div id="imageWrapper">
        <div class="mb-2 image-row">
            <div class="d-flex gap-2 align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="default_image" value="0" checked>
                </div>
                <input type="file" name="images[]" class="form-control">
            </div>
        </div>
    </div>

    <button type="button" id="addImageBtn" class="btn btn-sm btn-primary mt-2">
        + Add More Image
    </button>

    <small class="text-muted d-block mt-1">Select radio button to set default image</small>
</div>


          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Save Product</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Edit Product</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editProductForm" method="POST" enctype="multipart/form-data">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger mx-3 mt-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @method('PUT')

        <div class="modal-body">

          <div class="row g-3">

            {{-- Product Name --}}
            <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            {{-- Description --}}
            <div class="col-md-6">
              <label class="form-label">Description</label>
              <input type="text" name="description" id="edit_description" class="form-control">
            </div>


            {{-- Prices --}}
            <div class="col-12">
              <label class="form-label">Prices & Hours</label>

              <div id="editPriceWrapper">
                <!-- Prices will be loaded here -->
              </div>
            </div>


            {{-- Existing Images --}}
            <div class="col-md-12">
              <label class="form-label">Current Images</label>
              <div id="existingImages" class="row g-2">
                <!-- Existing images will be loaded here -->
              </div>
            </div>

            {{-- New Images --}}
           <div class="col-md-12">
              <label class="form-label">Add New Images</label>

              <div id="editImageWrapper">
                  <div class="mb-2 image-row">
                      <input type="file" name="images[]" class="form-control">
                  </div>
              </div>

              <button type="button" id="addEditImageBtn" class="btn btn-sm btn-primary mt-2">
                  + Add More Image
              </button>
          </div>


          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary">Update Product</button>
        </div>

      </form>

    </div>
  </div>
</div>

@if ($errors->any() && session('edit_product_id'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        editProduct({{ session('edit_product_id') }});

    });

</script>
@elseif($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function () {
        new bootstrap.Modal(document.getElementById('addProductModal')).show();
    });
</script>
@endif

<script>
let priceIndex = 1;
let imageIndex = 1;

document.addEventListener('click', function(e){

    // Add Price Row
    if(e.target.classList.contains('addPrice')){

        let wrapper = document.getElementById('priceWrapper');

        let row = `
        <div class="row g-2 mb-2">
            <div class="col-md-1">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="default_price" value="${priceIndex}">
                </div>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="prices[${priceIndex}][price]" class="form-control" placeholder="Price">
            </div>

            <div class="col-md-4">
                <input type="number" name="prices[${priceIndex}][hours]" class="form-control" placeholder="Hours">
            </div>

            <div class="col-md-3">
                <button type="button" class="btn btn-danger removeRow">x</button>
            </div>
        </div>
        `;

        wrapper.insertAdjacentHTML('beforeend', row);

        priceIndex++;
    }

    // Remove Price Row
    if(e.target.classList.contains('removeRow')){
        e.target.closest('.row').remove();
    }

});

// Add multiple image upload fields
document.getElementById('addImageBtn').addEventListener('click', function () {
    const wrapper = document.getElementById('imageWrapper');

    const div = document.createElement('div');
    div.className = 'mb-2 image-row';

    div.innerHTML = `
        <div class="d-flex gap-2 align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="default_image" value="${imageIndex}">
            </div>
            <input type="file" name="images[]" class="form-control">
            <button type="button" class="btn btn-danger removeImage">X</button>
        </div>
    `;

    wrapper.appendChild(div);
    imageIndex++;
});

// remove image button
document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeImage')){
        e.target.closest('.image-row').remove();
    }
});


// Add image for edit modal
document.getElementById('addEditImageBtn').addEventListener('click', function () {
    const wrapper = document.getElementById('editImageWrapper');

    const div = document.createElement('div');
    div.className = 'mb-2 image-row';

    div.innerHTML = `
        <div class="d-flex gap-2">
            <input type="file" name="images[]" class="form-control">
            <button type="button" class="btn btn-danger removeImage">X</button>
        </div>
    `;

    wrapper.appendChild(div);
});


// Edit Product Function
function editProduct(id) {
    fetch(`/admin/products/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            
            // Set form action
            document.getElementById('editProductForm').action = `/admin/products/${id}`;
            
            // Fill basic fields
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_description').value = data.description || '';
            
            // Load Prices
            let priceHTML = '';
            data.prices.forEach((price, index) => {
                priceHTML += `
                    <div class="row g-2 mb-2">
                        <div class="col-md-1">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="radio" name="default_price" value="${index}" ${price.is_default ? 'checked' : ''}>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <input type="number" step="0.01" name="prices[${index}][price]" class="form-control" placeholder="Price" value="${price.price}">
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="prices[${index}][hours]" class="form-control" placeholder="Hours" value="${price.hours}">
                        </div>
                        <div class="col-md-3">
                            ${index > 0 ? '<button type="button" class="btn btn-danger removeEditPrice">x</button>' : '<button type="button" class="btn btn-success addEditPrice">+</button>'}
                        </div>
                    </div>
                `;
            });
            document.getElementById('editPriceWrapper').innerHTML = priceHTML;
            
            // Load Existing Images
            let imageHTML = '';
            data.images.forEach(image => {
                imageHTML += `
                    <div class="col-md-3 mb-2">
                        <div class="card">
                            <img src="/storage/${image.image_path}" class="card-img-top" style="height:150px;object-fit:cover;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="default_image_existing" value="${image.id}" ${image.is_default ? 'checked' : ''}>
                                        <label class="form-check-label small">Default</label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteImage(${image.id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('existingImages').innerHTML = imageHTML;
            
            // Show modal
            new bootstrap.Modal(document.getElementById('editProductModal')).show();
        });
}

// Delete Image
function deleteImage(imageId) {
    if(confirm('Delete this image?')) {
        fetch(`/admin/products/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(() => {
            location.reload();
        });
    }
}

// Add/Remove price rows in edit modal
document.addEventListener('click', function(e){
    if(e.target.classList.contains('addEditPrice')){
        let wrapper = document.getElementById('editPriceWrapper');
        let index = wrapper.querySelectorAll('.row').length;
        
        let row = `
        <div class="row g-2 mb-2">
            <div class="col-md-1">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="radio" name="default_price" value="${index}">
                </div>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="prices[${index}][price]" class="form-control" placeholder="Price">
            </div>
            <div class="col-md-4">
                <input type="number" name="prices[${index}][hours]" class="form-control" placeholder="Hours">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger removeEditPrice">x</button>
            </div>
        </div>
        `;
        wrapper.insertAdjacentHTML('beforeend', row);
    }
    
    if(e.target.classList.contains('removeEditPrice')){
        e.target.closest('.row').remove();
    }
});

</script>

@endsection