<?php

namespace Webkul\Shiprocket\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webkul\Shiprocket\Services\ShiprocketService;

class ShiprocketServiceProvider extends ServiceProvider
{
    public function boot()
    {
        /**
         * Fire when order is placed
         */
        Event::listen('checkout.order.save.after', function ($order) {

            \Log::info('SR EVENT FIRED', [
                'order_id' => $order->id,
            ]);

            // Prevent duplicate Shiprocket order
            if ((int) $order->shiprocket_order_created === 1) {
                return;
            }

            $shiprocket = app(ShiprocketService::class);

            // Token must exist (Test API clicked)
            if (! $shiprocket->token()) {
                \Log::warning('SR STOP: no token', [
                    'order_id' => $order->id,
                ]);
                return;
            }

            // Pickup location must exist
            
           $pickupLocation = $shiprocket->getPrimaryPickupLocation();

if (! $pickupLocation) {
    \Log::error('SR STOP: No primary pickup location');
    return;
}



            // Normalize phone (India-safe)
            $billingPhone = preg_replace('/\D/', '', $order->billing_address->phone ?? '');
            $billingPhone = substr($billingPhone, -10);

            // Normalize addresses
            $billingAddress = is_array($order->billing_address->address)
                ? implode(', ', $order->billing_address->address)
                : $order->billing_address->address;

            $shippingAddress = is_array($order->shipping_address->address)
                ? implode(', ', $order->shipping_address->address)
                : $order->shipping_address->address;

            $payload = [
                'order_id' => 'BAGISTO-' . $order->id,
                'order_date' => now()->format('Y-m-d'),
                'pickup_location' => $pickupLocation,

                'shipping_is_billing' => true,

                'billing_customer_name' => $order->billing_address->first_name,
                'billing_last_name'     => $order->billing_address->last_name ?? '',
                'billing_address'       => $billingAddress,
                'billing_city'          => $order->billing_address->city,
                'billing_pincode'       => $order->billing_address->postcode,
                'billing_state'         => $order->billing_address->state,
                'billing_country'       => $order->billing_address->country,
                'billing_email'         => $order->customer_email,
                'billing_phone'         => $billingPhone,

                'shipping_customer_name' => $order->shipping_address->first_name,
                'shipping_last_name'     => $order->shipping_address->last_name ?? '',
                'shipping_address'       => $shippingAddress,
                'shipping_city'          => $order->shipping_address->city,
                'shipping_pincode'       => $order->shipping_address->postcode,
                'shipping_state'         => $order->shipping_address->state,
                'shipping_country'       => $order->shipping_address->country,

                'payment_method' => $order->payment->method,
                'sub_total'      => (float) $order->sub_total,

                'length'  => 10,
                'breadth' => 10,
                'height'  => 10,
                'weight'  => max(0.1, $order->items->sum('total_weight')),

                'order_items' => [],
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

            \Log::info('SR ORDER RESPONSE', [
                'order_id' => $order->id,
                'response' => $response,
            ]);

            if (! empty($response['order_id'])) {
                $order->updateQuietly([
                    'shiprocket_order_created' => 1,
                ]);
            }
        });

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'shiprocket');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register()
    {
        $this->app->singleton(
            ShiprocketService::class,
            fn () => new ShiprocketService()
        );
    }
}
