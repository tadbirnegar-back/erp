<?php
namespace Modules\LMS\app\Http\Classes;

use Modules\LMS\app\Http\Classes\Abstracts\EnrollAbstract;

class FreeEnroll extends EnrollAbstract
{
    public function __construct($courseID){
        $this -> courseID = $courseID;

    }

    public function handle()
    {
        $this->storeEnroll();
    }
}
