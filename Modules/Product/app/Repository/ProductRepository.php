<?php

namespace Modules\Product\App\Interface;

use Illuminate\Support\Facades\DB;
use Modules\Product\Models\Product;
use Modules\Stock\Models\Stock;

abstract class ProductRepository implements ProductInterface
{
    public function index()
    {
        return Product::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        DB::transaction(function () use ($request) {
            $product = Product::create(
                $request->validated()->except(['quantity', 'batch_no', 'expiry_date'])
            );
            Stock::create(
                array_merge(
                    $request->validated()->only(['quantity', 'batch_no', 'expiry_date']),
                    ['product_id' => $product->id]
                )
            );
        });

    }

    public function show($product)
    {
        return $product->load(['stock']);
    }

    public function update($request, $product)
    {
            DB::transaction(function () use ($request,$product) {
            $product->update(
                $request->validated()->except(['quantity', 'batch_no', 'expiry_date'])
            );
            $product?->stock->array_merge(
                    $request->validated()->only(['quantity', 'batch_no', 'expiry_date']),
                    ['product_id' => $product->id]
                );
        });

    }

    public function delete($product)
    {
        $product->delete();
    }
}
