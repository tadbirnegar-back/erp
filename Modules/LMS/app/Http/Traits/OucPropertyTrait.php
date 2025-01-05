<?php
namespace Modules\LMS\app\Http\Traits;
use Modules\LMS\app\Models\OucProperty;

trait OucPropertyTrait
{
    public function getOucPropertyIdByName($name)
    {
        $oucProperty = OucProperty::where('name', $name)->first();
        return $oucProperty->id;
    }
}
