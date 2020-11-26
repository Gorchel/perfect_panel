<?php

namespace App\Classes\Converting;

use App\Classes\Integrations\BTC\BTCClient;
use App\Classes\Converting\SelfCommission;

/**
 * Class ConvertManager
 * @package App\Classes\Converting
 */
class ConvertManager
{
    /**
     * @var bool|string
     */
    protected $btcResult;

    /**
     * @var mixed
     */
    protected $minConvertingValue;

    /**
     * @var \App\Classes\Converting\SelfCommission
     */
    protected $selfCommissionClass;

    /**
     * ConvertManager constructor.
     */
    public function __construct()
    {
        $btcClient = new BTCClient;
        $this->btcResult = $btcClient->getRates();
        $this->minConvertingValue = config('converting.min_converting_value');
        $this->selfCommissionClass = new SelfCommission;
    }

    /**
     * @param string $currency_from
     * @param string $currency_to
     * @param float $value
     * @return array
     */
    public function handle(string $currency_from, string $currency_to, float $value)
    {
        $responseIsset = $this->checkCurrencyIsset($currency_from, $currency_to);

        if ($responseIsset['status'] == false) {
            return $responseIsset;
        }

        $result = $this->converting($currency_from, $currency_to, $value);

        return $result;
    }

    /**
     * Converting function
     *
     * @param string $currency_from
     * @param string $currency_to
     * @param float $value
     */
    protected function converting(string $currency_from, string $currency_to, float $value)
    {
        if ($currency_from == 'BTC') {
            $result = $this->btc2currency($currency_to, $value);
        } else {
            $result = $this->currency2btc($currency_from, $value);
        }

        return $result;
    }

    /**
     * Converting btc to currency
     *
     * @param string $currency
     * @param float $value
     * @return array
     */
    protected function btc2currency(string $currency, float $value)
    {
        if ($value < $this->minConvertingValue) {
            $value = $this->minConvertingValue;
        }

        $rate = $this->selfCommissionClass->handle($this->btcResult[$currency]['last']);

        $converted_value = round($value * $rate, config('converting.rounding_off_result'));

        return ['status' => true, 'data' => [
            'rate' => $rate,
            'converted_value' => $converted_value,
        ]];
    }

    /**
     * Converting currency to btc
     *
     * @param string $currency
     * @param float $value
     * @return array
     */
    protected function currency2btc(string $currency, float $value)
    {
        if ($value < $this->minConvertingValue) {
            return ['status' => false, 'message' => 'Minimal converting for conversion '.$this->minConvertingValue];
        }

        $rate = $this->selfCommissionClass->handle($this->btcResult[$currency]['last']);

        $converted_value = round($value / $rate, config('converting.rounding_off_result'));

        return ['status' => true, 'data' => [
            'rate' => $rate,
            'converted_value' => $converted_value,
        ]];
    }

    /**
     * Check currency names
     *
     * @param string $currency_from
     * @param string $currency_to
     * @return array
     */
    protected function checkCurrencyIsset(string $currency_from, string $currency_to)
    {
        if ($currency_from != 'BTC' && $currency_to != 'BTC') {
            return ['status' => false, 'message' => 'Wrong BTC param'];
        }

        if ($currency_from == 'BTC') {
            $currency = $currency_to;
        } else {
            $currency = $currency_from;
        }

        if (!isset($this->btcResult[$currency])) {
            return ['status' => false, 'message' => 'Wrong currency name'];
        }

        return ['status' => true];
    }
}
