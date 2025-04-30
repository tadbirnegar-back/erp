<?php

namespace Modules\HRMS\app\Calculations;

use Modules\HRMS\app\Http\Enums\HireTypeEnum;
use Modules\HRMS\app\Models\ScriptType;
use Modules\OUnitMS\app\Models\OrganizationUnit;

class VillagerScriptTypePartTimeHireTypeCalculator extends CalculatorAbstract
{
    private ScriptType $scriptType;
    private HireTypeEnum $hireType;
    private OrganizationUnit $organizationUnit;

    public function __construct(ScriptType $scriptType, HireTypeEnum $hireType, OrganizationUnit $organizationUnit)
    {
        $this->scriptType = $scriptType;
        $this->hireType = $hireType;
        $this->organizationUnit = $organizationUnit;
    }

    public function calculate()
    {

    }

    //پایه
    public function getBaseSalary()
    {
        $baseSalary = 0;
        return $baseSalary;
    }

    public function getBaseYears()
    {
        $baseYears = 0;
        return $baseYears;
    }

    //فوق العاده های مستمر
    public function getVillageDegreeExtra($baseSalary)
    {
        $degree = $this->organizationUnit->village->degree;
        $percentage = match ($degree) {
            1, 2 => 0.1,
            3, 4 => 0.15,
            5, 6 => 0.2,
        };

        $formula = '$baseSalary * $percentage';
        $params = ['baseSalary' => $baseSalary, 'percentage' => $percentage];

        $result = $this->evalFormula($formula, $params);

        return $result;
    }

    public function getBigVillageExtra($baseSalary)
    {
        $formula = '$baseSalary * $percentage';
        $population = $this->organizationUnit->village->population_1395 >= 10000 ? $this->organizationUnit->village->population_1395 : 0;
        $params = ['baseSalary' => $baseSalary, 'percentage' => $population];

        $result = $this->evalFormula($formula, $params);

        return $result;
    }

    public function getVillageSupervisorExtra($baseSalary)
    {
        return $this->getVillageDegreeExtra($baseSalary);
    }

    public function getEducationExtra($baseSalary)
    {
        $formula = '$baseSalary * $percentage';
        $params = ['baseSalary' => $baseSalary, 'percentage' => 0];

        $result = $this->evalFormula($formula, $params);

        return $result;
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

    public function getMissionExtra($minimum)
    {
        $formula = '$minimum * $percentage';
        $params = ['minimum' => $minimum, 'percentage' => 50];

        return $this->evalFormula($formula, $params);

    }

    public function getClothesArticle12Extra()
    {
        return 0;
    }

    public function getEidiArticle15Extra($baseSalary)
    {
        $formula = '($baseSalary * 2) * $days / 365';
        $params = ['baseSalary' => $baseSalary, 'days' => 30];

        return $this->evalFormula($formula, $params);
    }

    public function getEndOfWorkExtra($baseSalary)
    {
        $formula = '$baseSalary * $ExperienceYears';
        $params = ['baseSalary' => $baseSalary, 'ExperienceYears' => 1];

        return $this->evalFormula($formula, $params);
    }

    public function getEducationArticle18($baseSalary)
    {
        $formula = '$baseSalary * 1.176 * $educationTime';
        $params = ['baseSalary' => $baseSalary, 'educationTime' => 1];

        return $this->evalFormula($formula, $params);
    }

    public function getEvaluationArticle8($baseSalary)
    {
        $formula = '$baseSalary * 0.1';
        $params = ['baseSalary' => $baseSalary];

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
        return 5000000;
    }

    public function getChildrenBonusExtra()
    {
        return 0;
    }

    public function getRightOfHomeExtra()
    {
        return 9000000;
    }

    public function getAllowanceForConsumableItems()
    {
        return 14000000;
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
