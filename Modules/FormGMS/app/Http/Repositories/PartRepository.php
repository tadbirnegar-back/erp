<?php

namespace Modules\FormGMS\app\Http\Repositories;

use Modules\FormGMS\app\Models\Part;

class PartRepository
{
//    protected Part $part;
//
//    /**
//     * @param Part $part
//     */
//    public function __construct(Part $part)
//    {
//        Part = $part;
//    }

    public function store(array $data)
    {
//        $dataToInsert = $this->dataPreparation($json, $formID);

        try {
            \DB::beginTransaction();

            /** @var Part $part */
//            $part = Part::insert($dataToInsert);
            $part = new Part();

            $part->name = $data['name'] ?? null;
            $part->form_id = $data['formID'];

            $part->save();

            \DB::commit();

            return $part;

        } catch (\Exception $e) {

            \DB::rollBack();

            return $e; // Not recommended to return the exception directly

        }
    }

    private function dataPreparation(string $json, int $formID)
    {
        $arrayOfParts = json_decode($json, true);

        $parts = array_map(function ($data) use ($formID) {
            return [
                'name' => $data['partName'] ?? null,
                'field_id' => $data['fieldID'],
            ];
        }, $arrayOfParts);

        return $parts;

    }
}
