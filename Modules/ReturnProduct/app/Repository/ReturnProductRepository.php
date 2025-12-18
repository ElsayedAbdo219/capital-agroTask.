<?php

namespace Modules\ReturnProduct\App\Repository;

use Illuminate\Support\Facades\DB;
use Modules\ReturnProduct\App\Interface\ReturnProductInterface;
use Modules\ReturnProduct\Models\ReturnProduct;
use Modules\Stock\Models\Stock;

class ReturnProductRepository implements ReturnProductInterface
{
    public function index()
    {
        return ReturnProduct::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function show($returnProduct)
    {
        return $returnProduct;
    }

    public function store($request)
    {
        DB::beginTransaction(function () use ($request) {
            $returnProduct = ReturnProduct::create($request->validated());
            Stock::where('product_id', $returnProduct?->orderItem?->product_id)->decrement('quantity',$returnProduct->quantity);
        });
    }

    public function update($request, $returnProduct) 
    {
        DB::beginTransaction(function () use ($request ,  $returnProduct) {
            $returnProduct?->update($request->validated());
            Stock::where('product_id', $returnProduct?->orderItem?->product_id)->decrement('quantity',$returnProduct->quantity);
        });

    }

    public function delete($returnProduct) 
    {
      $returnProduct->delete();
    }
}
