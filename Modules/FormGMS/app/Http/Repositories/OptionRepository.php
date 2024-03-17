<?php

namespace Modules\FormGMS\app\Http\Repositories;

use Modules\FormGMS\app\Models\Option;

class OptionRepository
{
//    protected Option $option;
//
//    /**
//     * @param Option $option
//     */
//    public function __construct(Option $option)
//    {
//        Option = $option;
//    }


    public function bulkStore(array $json,int $fieldID)
    {
        $dataToInsert = $this->dataPreparation($json, $fieldID);
        try {
            \DB::beginTransaction();
            /**
             * @var Option $option
             *
             */
            $option = Option::insert($dataToInsert);
//            $option->label = $data['label'];
//            $option->field_id = $data['fieldID'];
//
//            $option->save();

            \DB::commit();

            return $option;

        } catch (\Exception $e) {
            \DB::rollBack();
            return $e;
        }
    }

    public function bulkUpdate(array $data,int $fieldID)
    {
        $dataToUpsert = $this->dataPreparation($data, $fieldID);
        $result = Option::upsert($dataToUpsert,['id']);

        return $result;
    }

    private function dataPreparation(array $arrayOfOptions,int $fieldID)
    {
//        $arrayOfOptions = json_decode($json, true);

        $options=array_map(function ($data) use ($fieldID){
            return [
                'label' => $data['label'],
                'field_id' => $fieldID,
                'id' => $data['id'] ?? null,

            ];
        },$arrayOfOptions);

        return $options;

    }


}
