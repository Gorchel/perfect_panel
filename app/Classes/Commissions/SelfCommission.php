<?php

namespace App\Classes\Commissions;

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
        return round($value + ($value * floatval(config('commissions.default'))), 2);
    }
}
