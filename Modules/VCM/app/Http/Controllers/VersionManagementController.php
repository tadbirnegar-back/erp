<?php

namespace Modules\VCM\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\AAA\app\Models\Module;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Traits\DateTrait;
use Modules\VCM\app\Models\VcmFeatures;
use Modules\VCM\app\Models\VcmUserVersion;
use Modules\VCM\app\Models\VcmVersions;

class VersionManagementController extends Controller
{
    use DateTrait;
    public function storeVersion(Request $request)
    {
        try {
            \DB::beginTransaction();
            $data = $request->all();
            $version = VcmVersions::create([
                'create_date' => now(),
                'high_version' => $data['high_version'],
                'low_version' => $data['low_version'],
                'mid_version' => $data['mid_version'],
            ]);

            $features = json_decode($data['feratures']);
            foreach ($features as $feature) {
                VcmFeatures::create([
                    'vcm_version_id' => $version->id,
                    'description' => $feature->description,
                    'module_id' => $feature->module_id,
                ]);
            }
            \DB::commit();
            return response()->json(['message' => 'ورژن با موفقیت ثبت شد']);

        } catch (\Exception $exception) {
            \DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    public function indexVersion(Request $request)
    {
        $data = $request->all();
        if ($data['version_type'] == 1) {
            $high = intval(VcmVersions::orderByDesc('id')->value('high_version'));
            $high = $high + 1;
            $middle = 0;
            $low = 0;
        } elseif ($data['version_type'] == 2) {
            $version = VcmVersions::select('high_version', 'mid_version')
                ->orderByDesc('id')
                ->first();

            $high = intval(optional($version)->high_version);
            $middle = intval(optional($version)->mid_version) + 1;
            $low = 0;

        } else {
            $version = VcmVersions::select('high_version', 'mid_version', 'low_version')
                ->orderByDesc('id')
                ->first();

            $high = intval(optional($version)->high_version);
            $middle = intval(optional($version)->mid_version);
            $low = intval(optional($version)->low_version) + 1;
        }

        return response()->json([
            'high' => $high,
            'middle' => $middle,
            'low' => $low,
        ]);
    }

    public function showVersion()
    {
        $user = \Auth::user();

        $versions = \DB::table('vcm_versions')
            ->whereNotIn('id', function ($query) use ($user) {
                $query->select('vcm_version_id')
                    ->from('vcm_user_versions')
                    ->where('user_id', $user->id);
            })
            ->where('create_date', '>', $user->created_at)
            ->pluck('id');

        $vcm = VcmFeatures::with('module.category', 'version')
            ->whereIn('vcm_version_id', $versions)
            ->get()
            ->groupBy(fn($item) => $item->vcm_version_id) // Group by version ID
            ->map(function ($versionGroup) {
                return $versionGroup
                    ->groupBy(fn($item) => $item->module->category->name) // Group by category name
                    ->map(function ($categoryGroup) {
                        $descriptions = $categoryGroup->pluck('description')->toArray();

                        $firstItem = $categoryGroup->first();
                        return [
                            'id' => $firstItem->id,
                            'description' => $descriptions,
                            'vcm_version_id' => $firstItem->vcm_version_id,
                            'module_id' => $firstItem->module_id,
                            'module' => $firstItem->module,
                            'version' => $firstItem->version,
                        ];
                    })->values(); // Reset array keys
            })
            ->flatten(1) // Flatten the nested structure
            ->values(); // Reset keys to ensure a clean array output

        if ($versions->isNotEmpty()) {
            foreach ($versions as $version) {
                VcmUserVersion::create([
                    'vcm_version_id' => $version,
                    'user_id' => $user->id,
                ]);
            }
            return response()->json($vcm);
        } else {
            return response()->json(['data' => null] , 204);
        }
    }






    public function indexModules()
    {
        $modules = Module::with('category')->get();
        return response()->json($modules);
    }
}
