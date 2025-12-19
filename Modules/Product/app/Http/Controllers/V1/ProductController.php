<?php

namespace Modules\Product\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\Product\Models\Product;
use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\V1\ProductRequest;
use Modules\Product\App\Interfaces\ProductInterface;
class ProductController extends Controller
{
      use ApiResponseTrait;

    protected $ProductRepository;

    public function __construct(ProductInterface $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }

    public function index(Request $request)
    {
        return $this->ProductRepository->index();
    }

    public function store(ProductRequest $request)
    {
        $this->ProductRepository->store($request);

        return $this->respondWithSuccess('Product Created Successfully');
    }

    public function show(Product $product)
    {
        $this->ProductRepository->show($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->ProductRepository->update($request, $product);

        return $this->respondWithSuccess('Product Updated Successfully');
    }

    public function delete(Product $product)
    {
        $this->ProductRepository->delete($product);

        return $this->respondWithSuccess('Product Deleted Now');
    }
    
  
}
