<?php

namespace Modules\HRMS\app\Http\Enums;

enum RelationTypeEnum: int
{

    case MOTHER = 1;
    case FATHER = 2;
    case BROTHER = 3;
    case SISTER = 4;
    case SPOUSE = 5;
    case CHILD = 6;

}
