<?php

namespace Modules\FormGMS\app\Http\Repositories;

use Modules\FormGMS\app\Models\Form;


class FormRepository
{
//    protected Form $form;
//
//    /**
//     * @param Form $form
//     */
//    public function __construct(Form $form)
//    {
//        Form = $form;
//    }

    public function store(array $data)

    {

        try {

            \DB::beginTransaction();



            /** @var Form $form */

            $form = new Form();



            $form->name = $data['formName'];

            $form->creator_id = $data['userID'];
            $status = Form::GetAllStatuses()->where('name', '=', 'فعال')->first();
            $form->status_id = $status->id;

//            $form->create_date = now();



            $form->save();



            \DB::commit();



            return $form;

        } catch (\Exception $e) {

            \DB::rollBack();



            return $e; // Not recommended to return the exception directly

        }

    }








}
