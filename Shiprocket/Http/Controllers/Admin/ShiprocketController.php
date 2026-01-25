<?php

namespace Webkul\Shiprocket\Http\Controllers\Admin;


use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Webkul\Shiprocket\Services\ShiprocketService;
use Webkul\Core\Models\CoreConfig;

class ShiprocketController extends Controller

{

    public function config()
    {
        return view('shiprocket::admin.config');
    }

    public function save(Request $request)
    {
        $channelCode = core()->getCurrentChannelCode();
        $localeCode  = core()->getCurrentLocale()->code;

        /* =========================
           ACTIVATE INTEGRATION ONLY
        ==========================*/
     if ($request->input('action') === 'activate') {

    $type = $request->input('integration_type');

    if (! in_array($type, ['api', 'channel'])) {
        return redirect()->back();
    }

    \Webkul\Core\Models\CoreConfig::updateOrCreate(
        [
            'code'         => 'shiprocket.active_integration',
            'channel_code' => 'default',
            'locale_code'  => 'en',
        ],
        [
            'value' => $type,
        ]
    );

    return redirect()
        ->back()
        ->with(
            'success',
            $type === 'api'
                ? 'Shiprocket API integration activated successfully.'
                : 'Shiprocket Channel integration activated successfully.'
        );
}


        /* =========================
           SAVE CREDENTIALS ONLY
        ==========================*/
       
       if ($request->input('action') === 'credentials') {

    // 🔴 FORCE SAVE EMAIL (even if unchanged)
    
    \Webkul\Core\Models\CoreConfig::updateOrCreate(
        [
            'code'         => 'shiprocket.email',
            'channel_code' => 'default',
            'locale_code'  => 'en',
        ],
        [
            'value' => (string) $request->input('email'),
        ]
    );

    // 🔴 SAVE PASSWORD ONLY IF PROVIDED
    if (strlen($request->input('password')) > 0) {
        \Webkul\Core\Models\CoreConfig::updateOrCreate(
            [
                'code'         => 'shiprocket.password',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ],
            [
                'value' => $request->input('password'),
            ]
        );
    }

    // 🔴 SAVE CHANNEL ID IF PRESENT
    if (strlen($request->input('channel_id')) > 0) {
        \Webkul\Core\Models\CoreConfig::updateOrCreate(
            [
                'code'         => 'shiprocket.channel_id',
                'channel_code' => 'default',
                'locale_code'  => 'en',
            ],
            [
                'value' => $request->input('channel_id'),
            ]
        );
    }

    return redirect()
        ->back()
        ->with('success', 'Shiprocket credentials saved successfully.');
}

       
       
    }

  

    
 public function testApi(ShiprocketService $shiprocket)
{
    try {
        if ($shiprocket->authenticate()) {
            return response()->json([
                'success' => true,
                'message' => 'Shiprocket authenticated successfully. Token cached.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Authentication failed. Check email & API password.',
        ], 400);

    } catch (\Throwable $e) {
        \Log::error('Shiprocket Test API Error', [
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Server error while authenticating Shiprocket.',
        ], 500);
    }
}

public function testChannel(ShiprocketService $shiprocket)
{
    return response()->json(
        $shiprocket->testChannelConnection()
    );
}

public function fetch()
{
    return response()->json([
        'email'      => \Webkul\Core\Models\CoreConfig::where('code', 'shiprocket.email')
            ->where('channel_code', 'default')
            ->where('locale_code', 'en')
            ->value('value'),

        'password'   => \Webkul\Core\Models\CoreConfig::where('code', 'shiprocket.password')
            ->where('channel_code', 'default')
            ->where('locale_code', 'en')
            ->value('value'),

        'channel_id' => \Webkul\Core\Models\CoreConfig::where('code', 'shiprocket.channel_id')
            ->where('channel_code', 'default')
            ->where('locale_code', 'en')
            ->value('value'),
    ]);
}



public function pickupLocations(ShiprocketService $service)

{
  
    if (! $service->token()) {
        return response()->json([
            'success' => false,
            'message' => 'Please authenticate Shiprocket first (Test API)',
        ], 401);
    }
    

    return response()->json([
        'success' => true,
        'data' => $service->fetchPickupLocations(),
    ]);
}


}
