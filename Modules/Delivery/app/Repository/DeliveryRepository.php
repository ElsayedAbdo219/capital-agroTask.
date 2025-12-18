<?php

namespace Modules\App\Repository;

use Exception;
use Modules\Order\Models\Order;
use Illuminate\Support\Facades\DB;
use Modules\Delivery\Models\Delivery;
use Modules\OrderItem\Models\OrderItem;
use Modules\App\Interface\DeliveryInterface;

class DeliveryRepository implements DeliveryInterface
{
    public function index()
    {
        return Delivery::orderByDesc('created_at')->cursorPaginate($request->paginateSize ?? 10);
    }

    public function store($request)
    {
        $data = $request->validated();
        $data['deliver_id'] = auth('api')->id();
        Delivery::craete($data);
    }

    public function show($delivery)
    {
        return $delivery;
    }

    public function update($request, $delivery)
    {
        $data = $request->validated();
        $data['user_id'] = auth('api')->id();
        $delivery->update($data);
    }

    public function delete($delivery)
    {
        $delivery->delete();
    }
}
