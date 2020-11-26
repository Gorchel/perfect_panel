<?php

namespace App\Classes\Converting;

/**
 * Class SelfCommission
 * @package App\Classes\Commissions
 */
class SelfCommission {
    /**
     * @param float $value
     */
    public function handle(float $value)
    {
        return round($value + ($value * floatval(config('converting.commission'))), 2);
    }
}
