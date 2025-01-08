<?php

namespace App\Http\Middleware;

use App\Models\Device;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    //     $deviceId = $request->header('device_id');
    //    // dd($deviceId , Device::where('device_id', $deviceId)->exists());
    //     if (!$deviceId || !Device::where('device_id', $deviceId)->exists()) {
    //         $responseData = [
    //             'status'        => false,
    //             'error' => trans('messages.device_error')
    //         ];
    //         return response()->json($responseData, 401);
    //     }

        return $next($request);
    }
}
