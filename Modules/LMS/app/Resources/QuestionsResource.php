<?php

namespace Modules\LMS\app\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     */
    public function toArray($request,): array
    {
        return [
            'id' => $request->id,
            'title' => $request->title,
            'creator' => $request->creator ? [
                'id' => $request->creator->id,
                'name' => $request->creator->name,
            ] : null,
            'difficulty' => $request->difficulty ? [
                'id' => $request->difficulty->id,
                'name' => $request->difficulty->name,
            ] : null,
            'lesson' => $request->lesson ? [
                'id' => $request->lesson->id,
                'title' => $request->lesson->title,
            ] : null,
            'questionType' => $request->questionType ? [
                'id' => $request->questionType->id,
                'name' => $request->questionType->name,
            ] : null,
            'status' => $request->status ? [
                'name' => $request->status->name,
                'className' => $request->status->class_name,
            ] : null,
            'repository' => $request->repository ? [
                'id' => $request->repository->id,
                'name' => $request->repository->name,
            ] : null,

        ];


    }
}
