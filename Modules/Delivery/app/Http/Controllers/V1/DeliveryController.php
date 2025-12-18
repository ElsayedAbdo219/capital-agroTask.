<?php

namespace Modules\Delivery\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\Delivery\Models\Delivery;
use Modules\App\Interface\DeliveryInterface;
use Modules\Delivery\Http\Requests\V1\DeliveryRequest;

class DeliveryController extends Controller
{
  use ApiResponseTrait;
    protected $DeliveryRepository;

    public function __construct(DeliveryInterface $DeliveryRepository)
    {
        $this->DeliveryRepository = $DeliveryRepository;
    }

    public function index(Request $request)
    {
        return $this->DeliveryRepository->index();
    }

    public function store(DeliveryRequest $request)
    {
        $this->DeliveryRepository->store($request);

        return $this->respondWithSuccess('Delivery Created Successfully');
    }

    public function show(Delivery $order)
    {
        $this->checkOnDelivery($order);
        $this->DeliveryRepository->show($order);
    }

    public function update(DeliveryRequest $request, Delivery $order)
    {
        $this->checkOnDelivery($order);
        $this->DeliveryRepository->update($request, $order);

        return $this->respondWithSuccess('Delivery Updated Successfully');
    }

    public function delete(Delivery $order)
    {
        $this->checkOnDelivery($order);
        $this->DeliveryRepository->delete($order);

        return $this->respondWithSuccess('Delivery Deleted Now');
    }

      public function checkOnDelivery($Delivery)
    {
        if(!$Delivery)
        return self::respondWithErrors('Delivery Not Found');
    }
}
