<?php

namespace Modules\HRMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AAA\app\Models\User;
use Modules\HRMS\app\Http\Traits\JobTrait;
use Modules\HRMS\app\Http\Traits\LevelTrait;
use Modules\HRMS\app\Http\Traits\PositionTrait;
use Modules\HRMS\app\Http\Traits\RecruitmentScriptTrait;
use Modules\HRMS\app\Models\Employee;
use Modules\HRMS\app\Models\HireType;
use Modules\HRMS\app\Models\Job;
use Modules\HRMS\app\Models\Level;
use Modules\HRMS\app\Models\Position;
use Modules\HRMS\app\Models\RecruitmentScript;
use Modules\HRMS\app\Models\RecruitmentScriptStatus;
use Modules\HRMS\app\Models\ScriptType;
use Modules\HRMS\app\Models\WorkForce;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\PersonMS\app\Models\Natural;
use Modules\PersonMS\app\Models\Person;
use Spatie\SimpleExcel\SimpleExcelReader;

class AddAccRcs extends Seeder
{
    use JobTrait, PositionTrait, LevelTrait, JobTrait, RecruitmentScriptTrait;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        try {
            \DB::beginTransaction();

            $pathToXlsx = realpath(__DIR__ . '/Fanni.xlsx');

            $rcs = SimpleExcelReader::create($pathToXlsx)
                ->getRows();
            $rcStatus = $this->activeRsStatus();

            $activePosStatus = $this->activePositionStatus();
            $activeLevelStatus = $this->activeLevelStatus();
            $activeJobStatus = $this->activeJobStatus();


            $scriptType = ScriptType::where('title', 'استخدام مسئول فنی')->first();
            $hireType = HireType::where('title', 'پاره وقت')->first();


            $position = Position::where('name', 'مسئول فنی')->where('status_id', $activePosStatus->id)->first();

            $level = Level::where('name', 'پایه')->where('status_id', $activeLevelStatus->id)->first();

            $job = Job::where('title', 'مسئول فنی')->where('status_id', $activeJobStatus->id)->first();


            $rcs->each(function ($rc) use ($hireType, $position, $level, $job, $scriptType, $rcStatus) {

//                $village = VillageOfc::with('organizationUnit')->where('abadi_code', $rc['کد آبادی'])->first();
                $village = OrganizationUnit::joinRelationship('village', function ($join) use ($rc) {
                    $join->where('abadi_code', $rc['کد آبادی']);
                })
                    ->where('name', $rc['آبادی'])
                    ->first();
                if (is_null($village)) {
                    dd($rc);
                }

                $organID = $village->id;


//                $natural = Natural::where('mobile', ltrim($rc['شماره موبایل مسئول امور مالی دهیاری'], '0'))->first();

                $person = Person::where('national_code', $rc['کد ملی'])->first();
                if (is_null($person)) {
                    $natural = Natural::create([
                        'mobile' => ltrim($rc['شماره موبایل مسئول امور فنی دهیاری'], '0'),
                        'first_name' => $rc['نام'],
                        'last_name' => $rc['نام خانوادگی'],
                        'gender_id' => 1
                    ]);
                    $person = Person::create([
                        'national_code' => $rc['کد ملی'],
                        'display_name' => $rc['نام'] . ' ' . $rc['نام خانوادگی'],
                        'personable_type' => Natural::class,
                        'personable_id' => $natural->id,
                        'create_date' => now(),
                    ]);
                }


//                dd(ltrim($rc['شماره موبایل مسئول امور مالی دهیاری'], '0'));
                $user = $person->user;
                if (is_null($user)) {
                    $user = User::create([
                        'mobile' => ltrim($rc['شماره موبایل مسئول امور فنی دهیاری'], '0'),
                        'password' => bcrypt($rc['کد ملی']),
                        'person_id' => $person->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $workforce = $person->workForces()->where('workforceable_type', Employee::class)->first();
                if (is_null($workforce)) {
                    $employee = Employee::create([]);
                    $workforce = WorkForce::create([
                        'workforceable_type' => Employee::class,
                        'workforceable_id' => $employee->id,
                        'isMarried' => 0,
                        'person_id' => $person->id,
                    ]);
                }

                $hasScript = RecruitmentScript::where('employee_id', $workforce->workforceable_id)
                    ->where('organization_unit_id', $organID)
                    ->where('script_type_id', $scriptType->id)
                    ->whereHas('latestStatus', function ($query) use ($rcStatus) {
                        $query->where('name', $rcStatus->name);
                    })
                    ->exists();

                if (!$hasScript) {
                    $recruitment = RecruitmentScript::create([
                        'employee_id' => $workforce->workforceable_id,
                        'organization_unit_id' => $organID,
                        'script_type_id' => $scriptType->id,
                        'hire_type_id' => $hireType->id,
                        'position_id' => $position->id,
                        'level_id' => $level->id,
                        'job_id' => $job->id,
                        'create_date' => now(),
                        'start_date' => now(),
                        'operator_id' => 1907
                    ]);


                    RecruitmentScriptStatus::create([
                        'recruitment_script_id' => $recruitment->id,
                        'status_id' => $rcStatus->id,
                        'create_date' => now(),
                    ]);
                }
            });
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            dd([$e->getMessage(),
                $e->getLine(),
                $e->getFile()
//                $e->getTrace()
            ]);

        }


    }

}
