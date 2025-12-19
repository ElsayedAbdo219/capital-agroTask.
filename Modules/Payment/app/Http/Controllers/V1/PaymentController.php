<?php

namespace Modules\Payment\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Modules\Payment\Models\Payment;
use App\Http\Controllers\Controller;
use Modules\Payment\App\Services\PaymentService;
use Modules\Payment\Http\Requests\CreateInvoiceRequest;
use Modules\Payment\Http\Requests\V1\PayManuallyRequest;

class PaymentController extends Controller
{
      use ApiResponseTrait;

    protected $PaymentService;

    public function __construct(PaymentService $PaymentService)
    {
        $this->PaymentService = $PaymentService;
    }

    public function getMethods(Request $request)
    {
        return $this->PaymentService->getMethods();
    }

    public function createInvoice(CreateInvoiceRequest $request)
    {
      $result =   $this->PaymentService->createInvoice($request);

        return $this->respondWithSuccess('Payment Created Successfully');
    }

    public function handleWebhook(Request $request)
    {
        $this->PaymentService->handleWebhook($request);
        return $this->respondWithSuccess('Webhook Handled Successfully');

    }

    public function payManually(PayManuallyRequest $request)
    {
        $this->PaymentService->payManually($request);
        return $this->respondWithSuccess('Manual Payment Recorded Successfully');
    }

  }