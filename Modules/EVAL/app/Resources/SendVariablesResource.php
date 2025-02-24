<?php

namespace Modules\EVAL\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SendVariablesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'eval_id' => $this->eval_id,
            'circular_max_value' => $this->circular_max_value,
            'option_boxes' => $this->getOptionBoxes($this->circular_max_value),
            'section_title' => $this->section_title,
            'indicator_title' => $this->indicator_title,
            'indicator_coefficient' => $this->indicator_coefficient,
            'variable_id' => $this->variable_id,
            'variable_title' => $this->variable_title,
            'variable_weight' => $this->variable_weight,
            'variable_description' => $this->variable_description,
            'target' => $this->foundTarget($this->ouc_property_value, $this->ouc_property_operation , $this->ouc_property_name),
        ];
    }

    private function getOptionBoxes($maxValue)
    {
        $optionBoxesArray = [];
        $eachOption = $maxValue / 5;
        for ($i = 1; $i < 6; $i++) {
            $optionBoxesArray[] = $i * $eachOption;
        }
        return $optionBoxesArray;
    }

    private function foundTarget($value, $operation, $name)
    {
        if($name == 'جمعیت'){
            if ($value == null) {
                return "کلیه دهیاری ها";
            } else {
                switch ($operation) {
                    case '>':
                        return $name . " بیشتر از " . $value . "  باشد";
                    case '<':
                        return $name . " کمتر از " . $value . "  باشد";
                    case '=':
                        return $name . " برابر با " . $value . "  باشد";
                    default:
                        return "کلیه دهیاری ها";
                }
            }
        }else{
            if ($value == null) {
                return "کلیه دهیاری ها";
            } else {
                if ($operation === '=') {
                    return $name . ($value == 1 ? " باشد" : " نباشد");
                } else {
                    return "عملیات نامعتبر است";
                }
            }
        }

    }

}
