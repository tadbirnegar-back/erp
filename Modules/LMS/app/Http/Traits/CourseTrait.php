<?php
namespace Modules\LMS\app\Http\Traits;

use Modules\AAA\app\Models\User;

trait CourseTrait {
    public function courseShow($course , $user)
    {

        if($course -> price > 0){
            $user -> load('')
        }
    }
}
