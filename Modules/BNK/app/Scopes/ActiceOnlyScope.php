<?php

namespace Modules\ACMS\app\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Modules\BNK\app\Http\Enums\ChequeStatusEnum;
use Modules\BNK\app\Models\BnkChequeStatus;

class ActiceOnlyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply($builder, Model $model): void
    {
        $builder->joinRelationshipUsingAlias('statuses', ['statuses' => function ($join) {
            $join
                ->whereRaw(BnkChequeStatus::getTableName() . '.create_date = (SELECT MAX(create_date) FROM accDocument_status WHERE document_id = acc_documents.id)')
                ->where('statuses.name', '!=', ChequeStatusEnum::DELETED->value);
        }]);
    }
}
