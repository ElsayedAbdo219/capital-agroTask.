<?php

namespace Modules\App\Services;

class PaymentService
{
    // Fawaterk API base URL
    // protected $baseUrl = 'https://staging.fawaterk.com/api/v2';

    // Your Fawaterk API key
    // protected $apiKey = config('services.fawaterak.test_api_key');
    // protected $apiKey = config('services.fawaterak.live_api_key');

    public function getMethods()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://staging.fawaterk.com/api/v2/getPaymentmethods',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer '.config('services.fawaterak.live_api_key'),
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200) {
            return response()->json(['error' => __('api.Failed to fetch payment methods'), 'response' => $response], $httpCode);
        }

        return response()->json(json_decode($response, true));
    }

    public function createInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|numeric|in:2,3,4,12,14',
            'country' => 'required|string',
            'currency' => 'required|string',
            'plan_id' => 'required|exists:plans,id',
            'mobileNumber' => 'required_if:payment_method_id,4|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = User::find($request->student_id);
        $planName = Plan::find($request->plan_id);

        $fullName = explode(' ', $student->name);
        $firstName = $fullName[0] ?? 'Unknown';
        $lastName = $fullName[1] ?? '.';

        $postData = [
            'payment_method_id' => $request->payment_method_id,
            'cartTotal' => $request->amount,
            'currency' => $request->currency,
            'invoice_number' => $request->invoice_number ?? strtoupper(Str::random(10)), // توليد الرقم إذا لم يتم إرساله
            'mobileNumber' => $request->mobileNumber,
            'customer' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $student->email,
                'phone' => $student->phone,
                'address' => $student->address,
            ],
            // 'redirectionUrls' => [
            //   'successUrl' => route('payment.success'),
            //   'failUrl' => route('payment.fail'),
            //   'pendingUrl' => route('payment.pending'),
            // ],
            'cartItems' => [
                [
                    'name' => $planName->name ?? 'Hafazny Program',
                    'price' => $request->amount,
                    'quantity' => 1,
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
                'Authorization: Bearer e5879646a7de723ece7ff7bb9a0eb23184dac7b6c6316af3a7',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseArray = json_decode($response, true);
        //   return    $responseArray;
        if (! isset($responseArray['data'])) {
            return response()->json(['message' => __('api.Invalid API response'), 'error' => $responseArray], 500);
        }

        $invoiceId = $responseArray['data']['invoice_id'] ?? null;
        $paymentUrl = $responseArray['data']['payment_data']['redirectTo'] ?? null;
        $paymentData = ! in_array($request->payment_method_id, [2, 4]) ? $responseArray['data']['payment_data'] : null;

        $value = $paymentData ? array_values($paymentData)[0] : null;

        // Alternatively, you can use a foreach loop to get the first value:
        $value = null;
        foreach ($paymentData ?? [] as $key => $val) {
            $value = $val; // Get the value of the first key
            break; // Exit after the first iteration
        }
        // return  [$paymentUrl,  $invoiceId ] ;

        // if (!$invoiceId || !$paymentUrl) {
        //     return response()->json(['message' => 'Incomplete API response', 'error' => $responseArray], 500);
        // }

        DB::beginTransaction();
        try {
            $existingInvoice = Invoice::where('invoice_id', $invoiceId)->first();
            if ($existingInvoice) {
                if ($existingInvoice->status === 'paid') {
                    return response()->json(['message' => __('api.Invoice already paid')], 400);
                }
                $existingInvoice->delete();
            }

            $invoice = new Invoice;
            $invoice->student_id = $student->id;
            $invoice->invoice_id = $invoiceId;
            $invoice->amount = $request->amount;
            $invoice->currency = $request->currency;
            $invoice->plan_id = $request->plan_id;
            $invoice->payment_url = $paymentUrl;
            $invoice->save();

            DB::commit();

            return response()->json(['message' => ! in_array($request->payment_method_id, [2, 4]) ? 'Invoice created successfully , the code is '.$value : 'Invoice created successfully', 'data' => $responseArray], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => __('api.Error creating invoice'), 'error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        \Log::info('Webhook Payload:', $payload);

        // التحقق من صحة hashKey لضمان أن الطلب من Fawaterak
        if (! $this->isValidHashKey($payload)) {
            Log::warning('Invalid hashKey in webhook payload');

            return response()->json(['message' => __('api.Invalid hashKey')], 403);
        }

        if (! isset($payload['invoice_status'], $payload['invoice_id']) || $payload['invoice_status'] !== 'paid') {
            Log::warning('Invalid payload in webhook: ', $payload);

            return response()->json(['message' => __('api.Invalid payload')], 400);
        }

        // البحث عن الفاتورة
        $invoice = Invoice::where('invoice_id', $payload['invoice_id'])->first();
        if (! $invoice) {
            return response()->json(['message' => __('api.Invoice not found')], 404);
        }

        $plan = Plan::find($invoice->plan_id);
        if (! $plan) {
            return response()->json(['message' => __('api.Plan not found')], 404);
        }

        $user = User::find($invoice->student_id);
        if (! $user) {
            return response()->json(['message' => __('api.User not found')], 404);
        }

        // حذف الاشتراكات القديمة
        $subscriptionIds = Subscription::where('user_id', $user->id)->pluck('id')->toArray();
        if (! empty($subscriptionIds)) {
            SubscriptionDetail::whereIn('subscription_id', $subscriptionIds)->delete();
            Subscription::whereIn('id', $subscriptionIds)->delete();
            Invoice::whereIn('subscription_id', $subscriptionIds)->where('id', '!=', $invoice->id)->delete();
        }
        Log::info('Starting transaction for webhook processing');
        // تنفيذ العمليات في Transaction لضمان الاستقرار
        try {
            Log::info('-------------------------------------------------------------------');
            DB::transaction(function () use ($user, $invoice, $plan) {
                $startDate = Carbon::now();
                $endDate = $startDate->copy()->addDays($plan->days_number - 1);

                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'plan_id' => $invoice->plan_id,
                ]);
                Log::info('Subscription created successfully: ', $subscription->toArray());
                Log::info('-------------------------------------------------------------------');

                $invoice->update([
                    'status' => 'paid',
                    'subscription_id' => $subscription->id,
                ]);
                Log::info('Invoice updated successfully: ', $invoice->toArray());
                Log::info('-------------------------------------------------------------------');

                $remaining_hours = sprintf('%02d:%02d:%02d', floor($plan->total_hours), $plan->minutes, 0);
                SubscriptionDetail::create([
                    'subscription_id' => $subscription->id,
                    'total_hours' => $remaining_hours,
                    'remaining_hours' => $remaining_hours,
                    'max_lectures' => $plan->max_lectures,
                ]);
                Log::info('SubscriptionDetail created successfully');
                Log::info('-------------------------------------------------------------------');
            });

            return response()->json(['message' => __('api.Webhook processed successfully')], 200);
        } catch (\Exception $e) {
            \Log::error('Webhook processing failed: '.$e->getMessage());

            return response()->json(['message' => __('api.Error processing webhook')], 500);
        }
    }

    /**
     *  التحقق من صحة hashKey
     */
    private function isValidHashKey($payload)
    {
        $secretKey = config('services.fawaterak.live_api_key');
        if (! isset($payload['hashKey'], $payload['invoice_id'], $payload['invoice_key'], $payload['payment_method'])) {
            return false;
        }
        $queryParam = "InvoiceId={$payload['invoice_id']}&InvoiceKey={$payload['invoice_key']}&PaymentMethod={$payload['payment_method']}";
        // $queryParam = "Domain=https://api.hafzny.com&ProviderKey=FAWATERAK.20193";
        $generatedHash = hash_hmac('sha256', $queryParam, $secretKey, false);

        return hash_equals($generatedHash, $payload['hashKey']);
    }

    // دالة للحصول على العملة بناءً على الدولة
    private function getCurrencyByCountry($country)
    {
        $currencies = [
            'Egypt' => 'EGP',   // مصر
            'Saudi Arabia' => 'SAR',  // السعودية
            'USA' => 'USD',  // الولايات المتحدة
            'UAE' => 'AED',  // الإمارات
            'Kuwait' => 'KWD',  // الكويت
            'Qatar' => 'QAR',  // قطر
            'Bahrain' => 'BHD',  // البحرين
        ];

        // إرجاع العملة بناءً على الدولة، الافتراضي هو الدولار
        return $currencies[$country] ?? 'EGP';
    }

    public function success()
    {
        return response()->json(['message' => __('api.Payment successful')]);
    }

    public function fail()
    {
        return response()->json(['message' => __('api.Payment failed')]);
    }

    public function pending()
    {
        return response()->json(['message' => __('api.Payment is pending')]);
    }

    public function statusInvoice(Request $request)
    {
        $data = $request->validate([
            'invoiceId' => 'required', 'exists:invoices,invoice_id',
        ]);
        $invoice = Invoice::where('invoice_id', $data['invoiceId'])->first();

        if (! $invoice) {
            return response()->json(['message' => __('api.Invoice Not Found')]);
        }

        if ($invoice) {
            return response()->json(['invoice' => $invoice]);
        }
    }
}
