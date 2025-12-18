<?php

namespace Modules\App\Services;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\User\Models\User;
use Modules\Order\Models\Order;
use Modules\Stock\Models\Stock;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Invoice\Models\Invoice;
use Modules\Payment\Models\Payment;

class PaymentService
{
  use ApiResponseTrait;

    public function getMethods()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://staging.fawaterk.com/api/v2/getPaymentmethods',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.config('services.fawaterak.test_api_key'),
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // curl_close($curl);

        if ($httpCode !== 200) {
            return $this->respondWithError("Request Data are InCorrect,try again!");
        }

        return response()->json(json_decode($response, true));
    }

    public function createInvoice(Request $request)
    {
        $request->validated();
        $order = Order::find($request->order_id);
        $student = $order->user;
        $fullName = explode(' ', $student?->name);
        $firstName = $fullName[0] ?? 'Unknown';
        $lastName = $fullName[1] ?? '.';

        $postData = [
            'payment_method_id' => $request->payment_method_id,
            'cartTotal' => $request->amount,
            'currency' => $request->currency,
            'invoice_number' => $request->invoice_number ?? strtoupper(Str::random(10)), 
            'mobileNumber' => $student->phone ?? 01010001010,
            'customer' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $student->email ?? '',
                'phone' => $student->phone ?? '',
                'address' => $student->address ?? '',
            ],
            'cartItems' => [
                [
                    'name' => 'Feed order',
                    'price' => $request->amount,
                    'quantity' => $order->total_amount,
                ],
            ],
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://staging.fawaterk.com/api/v2/invoiceInitPay',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.config('services.fawaterak.test_api_key'),
            ],
        ]);

        $response = curl_exec($curl);
        // curl_close($curl);

        $responseArray = json_decode($response, true);
        if (! isset($responseArray['data']))
        {
          $this->respondWithError(" Data Not Found,try again!");
        }

        $invoiceId = $responseArray['data']['invoice_id'] ?? null;
        $paymentUrl = $responseArray['data']['payment_data']['redirectTo'] ?? null;
        $paymentData = ! in_array($request->payment_method_id, [2, 4]) ? $responseArray['data']['payment_data'] : null;

        $value = $paymentData ? array_values($paymentData)[0] : null;
        $value = null;
        foreach ($paymentData ?? [] as $key => $val) {
            $value = $val; 
            break; 
        }

        DB::beginTransaction();
        try {
            $existingInvoice = Invoice::where('invoice_id', $invoiceId)->first();
            if ($existingInvoice) {
                if ($existingInvoice->status === 'paid') {
                    $this->respondWithError("Invoice Already Exists!");
                }
                $existingInvoice->delete();
            }

            $payment = new Payment;
            $payment->order_id = $request->order_id;
            $payment->amount = $request->amount;
            $payment->method = $request->method;
            $payment->save();

            $invoice = new Invoice;
            $invoice->invoice_id = $invoiceId;
            $invoice->amount = $request->amount;
            $invoice->currency = $request->currency;
            $invoice->order_id = $request->order_id;
            $invoice->payment_url = $paymentUrl;
            $invoice->save();


            DB::commit();

            return response()->json(['message' => ! in_array($request->payment_method_id, [2, 4]) ? 'Invoice created successfully , the code is '.$value : 'Invoice created successfully', 'data' => $responseArray], 201);
        } catch (\Exception $e) {
            DB::rollBack();

           return $this->respondWithError(" Data Not Found,try again!" ,['error' => $e->getMessage()]);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        \Log::info('Webhook Payload:', $payload);
        if (! $this->isValidHashKey($payload)) {
            Log::warning('Invalid hashKey in webhook payload');
             return $this->errorForbidden('Invalid hashKey');
        }

        if (! isset($payload['invoice_status'], $payload['invoice_id']) || $payload['invoice_status'] !== 'paid') {
            Log::warning('Invalid payload in webhook: ', $payload);
              return $this->respondWithError('Invalid payload');
        }

        // البحث عن الفاتورة
        $invoice = Invoice::where('invoice_id', $payload['invoice_id'])->first();
        if (! $invoice) {
          return $this->errorNotFound('Invoice Not Found');
        }

        $order = Order::find($invoice->order_id);
        $payment = Payment::find($order->id);
        if (! $order) {
            return $this->errorNotFound('Invoice Not Found');
        }
        Log::info('Starting transaction for webhook processing');
        try {
            DB::transaction(function () use ( $invoice, $order , $payment) {
                  $payment->update([
                    'status' => 'paid'
                  ]);
                 Stock::where('product_id',$order->orderItem->product_id)->first()?->decrement('quantity',$order->total_amount);
                Log::info('Invoice updated successfully: ', $invoice->toArray());
            
                Log::info('SubscriptionDetail created successfully');
            });
            return $this->respondWithSuccess('Webhook processed successfully');
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: '.$e->getMessage());

          

        }
    }

    private function isValidHashKey($payload)
    {
        $secretKey = config('services.fawaterak.test_api_key');
        if (! isset($payload['hashKey'], $payload['invoice_id'], $payload['invoice_key'], $payload['payment_method'])) {
            return false;
        }
        $queryParam = "InvoiceId={$payload['invoice_id']}&InvoiceKey={$payload['invoice_key']}&PaymentMethod={$payload['payment_method']}";
        $generatedHash = hash_hmac('sha256', $queryParam, $secretKey, false);

        return hash_equals($generatedHash, $payload['hashKey']);
    }

    
}
