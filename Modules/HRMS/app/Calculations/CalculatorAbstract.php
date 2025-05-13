<?php

namespace Modules\HRMS\app\Calculations;

abstract class CalculatorAbstract
{
    function evalFormula(string $formula, array $params)
    {
        extract($params, EXTR_SKIP);
        return eval('return ' . $formula . ';');
    }
}
