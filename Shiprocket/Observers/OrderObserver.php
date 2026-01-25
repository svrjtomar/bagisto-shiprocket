<?php

namespace Webkul\Shiprocket\Observers;

use Webkul\Sales\Models\Order;
use Webkul\Shiprocket\Services\ShiprocketService;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order)

    {
        Log::info('SR CHECKPOINT 1', [
            'order_id' => $order->id,
            'status'   => $order->status,
        ]);

        // Only process new orders
        if ($order->status !== 'pending') {
            return;
        }

        // Ensure addresses exist
        if (! $order->billing_address || ! $order->shipping_address) {
            Log::warning('SR STOP: missing address', [
                'order_id' => $order->id,
            ]);
            return;
        }

        // Prevent duplicate Shiprocket calls
        if ((int) $order->shiprocket_order_created === 1) {
            Log::warning('SR STOP: already created', [
                'order_id' => $order->id,
            ]);
            return;
        }

        $shiprocket = app(ShiprocketService::class);

        // Token must exist
        if (! $shiprocket->token()) {
            Log::warning('SR STOP: no token', [
                'order_id' => $order->id,
            ]);
            return;
        }

        Log::info('SR CHECKPOINT 2: calling Shiprocket API', [
            'order_id' => $order->id,
        ]);

        $payload = [
            'order_id' => 'BAGISTO-' . $order->id,
            'order_date' => now()->format('Y-m-d'),
            'pickup_location' => 'Primary',

            'billing_customer_name' => $order->billing_address->first_name,
            'billing_last_name'     => $order->billing_address->last_name ?? '',
            'billing_address'       => $order->billing_address->address1,
            'billing_city'          => $order->billing_address->city,
            'billing_pincode'       => $order->billing_address->postcode,
            'billing_state'         => $order->billing_address->state,
            'billing_country'       => $order->billing_address->country,
            'billing_email'         => $order->customer_email,
            'billing_phone'         => $order->billing_address->phone,

            'shipping_customer_name' => $order->shipping_address->first_name,
            'shipping_last_name'     => $order->shipping_address->last_name ?? '',
            'shipping_address'       => $order->shipping_address->address1,
            'shipping_city'          => $order->shipping_address->city,
            'shipping_pincode'       => $order->shipping_address->postcode,
            'shipping_state'         => $order->shipping_address->state,
            'shipping_country'       => $order->shipping_address->country,

            'payment_method' => $order->payment->method,
            'sub_total'      => $order->sub_total,
            'length'         => 10,
            'breadth'        => 10,
            'height'         => 10,
            'weight'         => max(0.1, $order->items->sum('total_weight')),
            'order_items'    => [],
        ];

        foreach ($order->items as $item) {
            $payload['order_items'][] = [
                'name'  => $item->name,
                'sku'   => $item->sku,
                'units' => (int) $item->qty_ordered,
                'selling_price' => $item->price,
            ];
        }

        $response = $shiprocket->createOrder($payload);

        Log::info('SR RAW API RESPONSE', [
            'order_id' => $order->id,
            'response' => $response,
        ]);

        if (! empty($response['order_id'])) {
    $order->updateQuietly([
        'shiprocket_order_created' => 1,
    ]);
}
    }
}
