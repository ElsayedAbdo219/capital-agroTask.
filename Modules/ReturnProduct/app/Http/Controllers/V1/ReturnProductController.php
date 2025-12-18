<?php

namespace Modules\ReturnProduct\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Modules\ReturnProduct\Models\ReturnProduct;
use Modules\ReturnProduct\App\Interface\ReturnProductInterface;
use Modules\ReturnProduct\Http\Requests\V1\ReturnProductRequest;

class ReturnProductController extends Controller
{
    use ApiResponseTrait;
    protected $ReturnProductRepository;

    public function __construct(ReturnProductInterface $ReturnProductRepository)
    {
        $this->ReturnProductRepository = $ReturnProductRepository;
    }

    public function index(Request $request)
    {
        return $this->ReturnProductRepository->index();
    }

    public function store(ReturnProductRequest $request)
    {
        $this->ReturnProductRepository->store($request);

        return $this->respondWithSuccess('ReturnProduct Created Successfully');
    }

    public function show(ReturnProduct $order)
    {
        $this->checkOnReturnProduct($order);
        $this->ReturnProductRepository->show($order);
    }

    public function update(ReturnProductRequest $request, ReturnProduct $order)
    {
        $this->checkOnReturnProduct($order);
        $this->ReturnProductRepository->update($request, $order);

        return $this->respondWithSuccess('ReturnProduct Updated Successfully');
    }

    public function delete(ReturnProduct $order)
    {
        $this->checkOnReturnProduct($order);
        $this->ReturnProductRepository->delete($order);

        return $this->respondWithSuccess('ReturnProduct Deleted Now');
    }

      public function checkOnReturnProduct($returnProduct)
    {
        if(!$returnProduct)
        return self::respondWithErrors('ReturnProduct Not Found');
    }
}
