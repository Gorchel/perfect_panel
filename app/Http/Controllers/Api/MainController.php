<?php

namespace App\Http\Controllers\Api;

use App\Classes\Authorization\AuthorizationTokenGetter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Router\RouterManager;
use App\Classes\Integrations\BTC\BTCClient;
use App\Classes\Converting\SelfCommission;
use App\Classes\Converting\ConvertManager;

/**
 * Class MainController
 * @package App\Http\Controllers\Api
 */
class MainController extends Controller
{
    /**
     * Get fixed auth token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function auth()
    {
        $authorizationClass = new AuthorizationTokenGetter;
        return response()->json([
            "status" =>  "success",
            "code" => 200,
            "data" => $authorizationClass->getFixedAuthToken(),
        ]);
    }

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
        //Краткая валидация, можно использовать пакет Validation
        $validationResponse = $this->_validate($request);

        if ($validationResponse['status'] == false) {
            return response()->json([
                "status" =>  "error",
                "code" => 400,
                "message" => $validationResponse['message'],
            ]);
        }

        $convertManager = new ConvertManager;
        $result = $convertManager->handle($request->get('currency_from'), $request->get('currency_to'), $request->get('value'));

        if ($result['status'] == false) {
            return response()->json([
                "status" =>  "error",
                "code" => 400,
                "message" => $result['message'],
            ]);
        }

        $result['data']["currency_from"] = $request->get('currency_from');
        $result['data']["currency_to"] = $request->get('currency_to');
        $result['data']["value"] = $request->get('value');

        return response()->json([
            "status" =>  "success",
            "code" => 200,
            "data" => $result['data'],
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function _validate(Request $request)
    {
        if (!$request->has('currency_from'))
        {
            return ['status' => false, 'message' => 'Required parameter currency_from'];
        }

        if (!$request->has('currency_to'))
        {
            return ['status' => false, 'message' => 'Required parameter currency_to'];
        }

        if (!$request->has('value'))
        {
            return ['status' => false, 'message' => 'Required parameter value'];
        }

        return ['status' => true];
    }
}
