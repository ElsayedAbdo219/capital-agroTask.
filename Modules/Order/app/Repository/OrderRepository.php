<?php

namespace Modules\App\Repository;

use Exception;
use Illuminate\Support\Facades\DB;
use Modules\App\Interface\OrderInterface;
use Modules\Order\Models\Order;
use Modules\OrderItem\Models\OrderItem;

class OrderRepository implements OrderInterface
{
    public function index()
    {
        return Order::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->id();
        unset($data['product_id'],$data['unit_price']);
        try {
            DB::beginTransaction();
            $order = Order::craete($data);
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
        return $order;
    }

    public function update($request, $order)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->id();
        unset($data['product_id'],$data['unit_price']);
        try {
            DB::beginTransaction();
            $order->update($data);
            $order->orderItem?->update(
                [
                    'product_id' => $data['product_id'],
                    'quantity' => $data['total_amount'],
                    'unit_price' => $data['unit_price'],
                    'total_price' => $data['total_amount'] * $data['unit_price'],
                ]
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return 'The Order Updating has Proplem , It'.$e;
        }
    }

    public function delete($order)
    {
        $order->delete();
    }
}
