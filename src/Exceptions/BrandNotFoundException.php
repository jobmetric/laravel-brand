<?php

namespace JobMetric\Brand\Exceptions;

use Exception;
use Throwable;

class BrandNotFoundException extends Exception
{
    public function __construct(int $number, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('unit::base.exceptions.brand_not_found', [
            'number' => $number,
        ]), $code, $previous);
    }
}
