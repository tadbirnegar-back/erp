<?php

namespace Modules\FormGMS\app\Http\Repositories;

use Illuminate\Support\Collection;
use Mockery\Exception;
use Modules\FormGMS\app\Models\Field;

class FieldRepository
{
//    protected Field $field;
//
//    /**
//     * @param Field $field
//     */
//    public function __construct(Field $field)
//    {
//        Field = $field;
//    }

    public function store(array $data)
    {
        try {
            \DB::beginTransaction();

            /** @var Field $field */
            $field = new Field;

            $field->label = $data['label'];
            $field->placeholder = $data['placeholder'];
            $field->is_required = $data['isRequired'] ?? false;
            $field->part_id = $data['partID'];
            $field->type_id = $data['typeID'];

            $field->save();

            \DB::commit();

            return $field;

        } catch (\Exception $e) {

            \DB::rollBack();

            return $e;

        }
    }

    public function bulkStore(array|Collection $data,int $partID)
    {
        $dataToInsert = $this->dataPreparation($data, $partID);
        try {
            \DB::beginTransaction();
            $fields = Field::insert($dataToInsert);
            \DB::commit();
            return $fields;
        }catch (Exception $e){
            \DB::rollBack();
            return $e;
        }
    }
    private function dataPreparation(array|Collection $data,int $partID)
    {
        if (!($data instanceof Collection)) {
            $data = collect($data);
        }

        $fields = $data->map(function ($field) use ($partID){
            return [
                'label' => $field['label'],
                'placeholder' => $field['placeholder'],
                'is_required' => $field['isRequired'] ?? false,
                'part_id' => $partID,
                'type_id' => $field['typeID'],
            ];

        });

        return $fields->toArray();


    }

}
