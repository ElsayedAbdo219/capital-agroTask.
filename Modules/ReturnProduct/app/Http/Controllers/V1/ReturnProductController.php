<?php

namespace Modules\ReturnProduct\Http\Controllers\V1;

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

    public function show(ReturnProduct $returnProduct)
    {
        return $this->ReturnProductRepository->show($returnProduct);
    }

    public function update(ReturnProductRequest $request, ReturnProduct $returnProduct)
    {
        $this->ReturnProductRepository->update($request, $returnProduct);

        return $this->respondWithSuccess('ReturnProduct Updated Successfully');
    }

    public function delete(ReturnProduct $returnProduct)
    {
        $this->ReturnProductRepository->delete($returnProduct);

        return $this->respondWithSuccess('ReturnProduct Deleted Now');
    }

  
}
