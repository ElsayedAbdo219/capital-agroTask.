<?php

namespace Modules\Product\App\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Product\App\Interfaces\ProductInterface;
use Modules\Product\Models\Product;
use Modules\Stock\Models\Stock;

class ProductRepository implements ProductInterface
{
    public function index()
    {
        return Product::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            $product = Product::create(
                array_diff_key($data, array_flip(['quantity', 'batch_no', 'expiry_date']))
            );

            Stock::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'batch_no' => $data['batch_no'],
                'expiry_date' => $data['expiry_date'],
            ]);
        });

    }

    public function show($product)
    {
        return $product->load(['stock']);
    }

    public function update($request, $product)
    {
        $data = $request->validated();
        DB::transaction(function () use ($product, $data) {
            $product->update(
                array_diff_key($data, array_flip(['quantity', 'batch_no', 'expiry_date']))
            );
            $product->stock()->updateOrCreate(
                ['product_id' => $product->id],
                Arr::only($data, ['quantity', 'batch_no', 'expiry_date'])
            );
        });

    }

    public function delete($product)
    {
        $product->delete();
    }
}
