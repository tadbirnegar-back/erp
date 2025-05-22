<?php

use Modules\BDM\app\Models\BuildingDossier;
use Modules\BDM\app\Models\Form;

return [
    'modules' => [
        'BDM' => [
            'code' => '1',
            'models' => [
                BuildingDossier::class => '11',
            ],
        ],
    ],
];
