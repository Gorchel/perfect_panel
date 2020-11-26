<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Router\RouterManager;
use App\Classes\Integrations\BTC\BTCClient;
use App\Classes\Commissions\SelfCommission;

/**
 * Class MainController
 * @package App\Http\Controllers\Api
 */
class MainController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function entryPoint(Request $request)
    {
        if (empty($request->get('method'))) {
            return response()->json([
                "status" =>  "error",
                "code" => 400,
                "message" => "Missing method",
            ]);
        }

        $router = new RouterManager;
        $action = $router->handle($request->get('method'), $request->method());

        if (empty($action)) {
            return response()->json([
                "status" =>  "error",
                "code" => 400,
                "message" => "Method not found",
            ]);
        }

        return $this->{$action}($request);
    }

    /**
     * @param Request $request
     */
    private function rates(Request $request)
    {
        $btcClient = new BTCClient;
        $rates = $btcClient->getRates();

        if ($request->has('currency')) {
            if (!isset($rates[$request->get('currency')])) {
                return response()->json([
                    "status" =>  "error",
                    "code" => 400,
                    "message" => "Wrong currency value",
                ]);
            }

            $currentRate = $rates[$request->get('currency')];
            $rates = [];
            $rates[$request->get('currency')] = $currentRate;
        }

        $selfCommission = new SelfCommission;

        $rates = array_map(function($rate) use ($selfCommission) {
            return $selfCommission->handle(floatval($rate['last']));
        }, $rates);

       return response()->json([
           "status" =>  "success",
           "code" => 200,
           "data" => $rates,
       ]);
    }

    /**
     * @param Request $request
     */
    private function convert(Request $request)
    {

    }
}
