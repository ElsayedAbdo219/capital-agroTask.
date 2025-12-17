<?php

namespace Modules\Product\App\Interface;

use Modules\User\Models\User;
use Modules\Product\Models\Product;
use Modules\Product\App\Interface\ProductInterface;

abstract class ProductRepository implements ProductInterface
{
    public function index()
    {
        return Product::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        Product::create($request->validated());
    }

    public function show($product)
    {
        return $product->load(['orders']);
    }

    public function update($request, $product)
    {
        $product->update($request->validated());
    }

    public function delete($product)
    {
        $product->delete();
    }
}
