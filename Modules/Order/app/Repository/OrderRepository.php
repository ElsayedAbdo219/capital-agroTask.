<?php

namespace Modules\Order\App\Repository;

use Exception;
use Illuminate\Support\Arr;
use Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Modules\OrderItem\Models\OrderItem;
use Modules\Order\App\Interfaces\OrderInterface;

class OrderRepository implements OrderInterface
{
    public function index()
    {
        return Order::orderByDesc('created_at')->with(['user'])->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->id();
        try {
            DB::beginTransaction();
            $dataExcept = Arr::except($data, ['product_id', 'unit_price']);
            $order = Order::create($dataExcept);
            $orderItem = OrderItem::create(
                [
                    'order_id' => $order->id,
                    'product_id' => $data['product_id'],
                    'quantity' => $data['total_amount'],
                    'unit_price' => $data['unit_price'],
                    'total_price' => $data['total_amount'] * $data['unit_price'],
                ]
            );

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return 'The Order Inserting has Proplem , It'.$e;
        }
    }

    public function show($order)
    {
        return $order->load('user');
    }

  public function update($request, $order)
{
    $data = $request->validated();
    $data['user_id'] = auth()->id();
    
    DB::transaction(function () use ($order, $data) {

        $order->update(
            Arr::except($data, ['product_id', 'unit_price', 'quantity'])
        );

        $order->orderItem()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'product_id'  => $data['product_id'],
                'quantity'    => $data['total_amount'],
                'unit_price'  => $data['unit_price'],
                'total_price' => $data['total_amount'] * $data['unit_price'],
            ]
        );
    });

    // return true;
}
    public function delete($order)
    {
        $order->delete();
    }
}
