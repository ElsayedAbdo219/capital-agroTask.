<?php

namespace Modules\Order\Http\Controllers\V1;

use Illuminate\Http\Request;
use Modules\Order\Models\Order;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\Order\App\Interfaces\OrderInterface;
use Modules\Order\Http\Requests\V1\OrderRequest;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $OrderInterface;

    public function __construct(OrderInterface $OrderInterface)
    {
        $this->OrderInterface = $OrderInterface;
    }

    public function index(Request $request)
    {
        return $this->OrderInterface->index();
    }

    public function store(OrderRequest $request)
    {
        $this->OrderInterface->store($request);

        return $this->respondWithSuccess('Order Created Successfully');
    }

    public function show(Order $order)
    {
        $this->checkOnOrder($order);
        $this->OrderInterface->show($order);
    }

    public function update(OrderRequest $request, Order $order)
    {
        $this->checkOnOrder($order);
        $this->OrderInterface->update($request, $order);

        return $this->respondWithSuccess('Order Updated Successfully');
    }

    public function delete(Order $order)
    {
        $this->checkOnOrder($order);
        $this->OrderInterface->delete($order);

        return $this->respondWithSuccess('Order Deleted Now');
    }

      public function checkOnOrder($order)
    {
        if(!$order)
        return self::respondWithErrors('Order Not Found');
    }
    
}
