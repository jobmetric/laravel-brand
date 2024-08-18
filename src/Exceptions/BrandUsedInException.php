<?php

namespace JobMetric\Brand\Exceptions;

use Exception;
use Throwable;

class BrandUsedInException extends Exception
{
    public function __construct(int $brand_id, int $number, int $code = 400, ?Throwable $previous = null)
    {
        parent::__construct(trans('brand::base.exceptions.brand_used_in', [
            'brand_id' => $brand_id,
            'number' => $number,
        ]), $code, $previous);
    }
}
