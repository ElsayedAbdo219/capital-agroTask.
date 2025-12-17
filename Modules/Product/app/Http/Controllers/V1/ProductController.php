<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\Product\Models\Product;
use App\Http\Controllers\Controller;
use Modules\Product\Http\Requests\ProductRequest;
use Modules\Product\App\Interface\ProductInterface;
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
        $this->checkOnProduct($product);
        $this->ProductRepository->show($product);

        return $product->load(['orders']);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->checkOnProduct($product);
        $this->ProductRepository->update($request, $product);

        return $this->respondWithSuccess('Product Updated Successfully');
    }

    public function delete(Product $product)
    {
        $this->checkOnProduct($product);
        $this->ProductRepository->delete($product);

        return $this->respondWithSuccess('Product Deleted Now');
    }
    
    public function checkOnProduct($product)
    {
        if(!$product)
        return self::respondWithErrors('Product Not Found');
    }
}
