<?php

namespace Modules\HRMS\app\Calculations;

use Modules\HRMS\app\Http\Enums\RelationTypeEnum;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Person;

class VillagerScriptTypePartTimeHireTypeCalculator extends CalculatorAbstract
{
    private ScriptType $scriptType;
    private HireType $hireType;
    private OrganizationUnit $organizationUnit;
    private Person $person;
    private $baseSalary;

    public function __construct(ScriptType $scriptType, HireType $hireType, OrganizationUnit $organizationUnit, Person $person)
    {
        $this->scriptType = $scriptType;
        $this->hireType = $hireType;
        $this->organizationUnit = $organizationUnit;
        $this->person = $person;
        $this->baseSalary = $this->getBaseSalary();
    }

    public function calculate()
    {

    }

    //پایه
    public function getBaseSalary()
    {
        if (is_null($this->baseSalary)) {
            $baseSalary = 3463656;
            $this->baseSalary = $baseSalary;
        }
        return $this->baseSalary;
    }

    public function getBaseYears()
    {
        $baseYears = 508435;
        return $baseYears;
    }

    //فوق العاده های مستمر
    public function getVillageDegreeExtra()
    {
        $degree = (int)$this->organizationUnit->village->degree;
        $percentage = match ($degree) {
            1, 2 => 0.1,
            3, 4 => 0.15,
            5, 6 => 0.2,
//            default => 0.25,
        };

        $formula = '$baseSalary * $percentage';
        $params = ['baseSalary' => $this->baseSalary, 'percentage' => $percentage];

        $result = $this->evalFormula($formula, $params);

        return floor($result);
    }

    public function getBigVillageExtra()
    {
        $formula = '$baseSalary * $percentage';
        $population = $this->organizationUnit->village->population_1395 >= 10000 ? 0.2 : 0;
        $params = ['baseSalary' => $this->baseSalary, 'percentage' => $population];

        $result = $this->evalFormula($formula, $params);

        return floor($result);
    }

    public function getVillageSupervisorExtra()
    {
        return $this->getVillageDegreeExtra();
    }

    public function getEducationExtra()
    {
        $educationRecord = $this->person->latestEducationRecord;

        $percentage = match ($educationRecord->levelOfEducation->name) {
            'دیپلم', 'کاردانی' => 0.05,
            'کارشناسی' => 0.1,
            'کارشناسی ارشد' => 0.15,
            'دکتری' => 0.2,
            default => 0,
        };
        $formula = '$baseSalary * $percentage';
        $params = ['baseSalary' => $this->baseSalary, 'percentage' => $percentage];

        $result = $this->evalFormula($formula, $params);

        return floor($result);
    }

    public function getIsarExtra()
    {
        return 0;
    }

    public function getDifficultyOfWorkExtra()
    {
        return 0;
    }

    public function getJazbExtra()
    {
        return 0;
    }

    public function getBadWheatherExtra()
    {
        return 0;
    }

    public function getNightShiftExtra()
    {
        return 0;
    }

// فوق العاده غیرمستمر

    public function getOverTimeExtra()
    {
        return 0;
    }

    public function getMissionExtra()
    {
        $minimum = 10000;
        $formula = '$minimum * $percentage';
        $params = ['minimum' => $minimum, 'percentage' => 50];

        return $this->evalFormula($formula, $params);

    }

    public function getClothesArticle12Extra()
    {
        return 0;
    }

    public function getEidiArticle15Extra()
    {
        $formula = '($baseSalary * 2) * $days / 365';
        $params = ['baseSalary' => $this->baseSalary, 'days' => 30];

        return $this->evalFormula($formula, $params);
    }

    public function getEndOfWorkExtra()
    {
        $formula = '$baseSalary * $ExperienceYears';
        $params = ['baseSalary' => $this->baseSalary, 'ExperienceYears' => 1];

        return $this->evalFormula($formula, $params);
    }

    public function getEducationArticle18()
    {
        $formula = '$baseSalary * 1.176 * $educationTime';
        $params = ['baseSalary' => $this->baseSalary, 'educationTime' => 1];

        return $this->evalFormula($formula, $params);
    }

    public function getEvaluationArticle8()
    {
        $formula = '$baseSalary * 0.1';
        $params = ['baseSalary' => $this->baseSalary];

        return $this->evalFormula($formula, $params);
    }

    public function getOperationalExpanseExtra()
    {
        return 0;
    }

    public function getProductivityBonusExtra()
    {
        return 0;
    }

    //مزایا رفاهی

    public function getMarriageExtra()
    {
        $natural = $this->person->natural;
        if ($natural->isMarried) {
            return 5000000;
        }
        return 0;
    }

    public function getChildrenBonusExtra()
    {
        $childrenCount = $this->person->dependents()
            ->where('relation_type_id', RelationTypeEnum::CHILD->value)
            ->count();

        $formula = '$baseSalary * $childrenCount * 3';
        $params = ['baseSalary' => $this->baseSalary, 'childrenCount' => $childrenCount];

        return $this->evalFormula($formula, $params);
    }

    public function getRightOfHomeExtra()
    {
        return 9000000;
    }

    public function getAllowanceForConsumableItems()
    {
        return 22000000;
    }

    //حقوق ثابت
    public function getFixedSalary()
    {
        return 0;
    }

    //حقوق مشمول بیمه
    public function getSalaryContainsInsurance()
    {
        return 0;
    }

    //حق بیمه
    public function getInsuranceRight()
    {
        return 0;
    }

    public function getTotalSalary()
    {
        return 0;
    }

    public function getTaxes()
    {
        return 0;
    }

    public function getTotalDeductions()
    {
        return 0;
    }

    public function getMustBePayedSalary()
    {
        return 0;
    }

    public function getFinalMustBePayedSalary()
    {
        return 0;
    }
}
