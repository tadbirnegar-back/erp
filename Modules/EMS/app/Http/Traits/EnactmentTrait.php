<?php

namespace Modules\EMS\app\Http\Traits;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Models\Attachmentable;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\EnactmentStatus;
use Modules\EMS\app\Models\Meeting;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\StateOfc;
use Morilog\Jalali\Jalalian;

trait EnactmentTrait
{
    private static string $enactmentSecretaryStatus = EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value;
    private static string $enactmentHeyaatStatus = EnactmentStatusEnum::PENDING_BOARD_REVIEW->value;
    private static string $enactmentCompleteStatus = EnactmentStatusEnum::COMPLETED->value;
    private static string $enactmentCancelStatus = EnactmentStatusEnum::CANCELED->value;
    private static string $enactmentDeclinedStatus = EnactmentStatusEnum::DECLINED->value;


    //=========================== roles =============================

    private static string $bakhshdar = RolesEnum::BAKHSHDAR->value;
    private static string $karshenasOstandari = RolesEnum::KARSHENAS_OSTANDARI->value;
    private static string $dabirHeyaat = RolesEnum::DABIR_HEYAAT->value;
    private static string $karshenasMashvarati = RolesEnum::KARSHENAS_MASHVARATI->value;
    private static string $ozvHeyaat = RolesEnum::OZV_HEYAAT->value;
    private static string $ozvShouraRusta = RolesEnum::OZV_SHOURA_RUSTA->value;


    public function indexPendingForSecretaryStatusEnactment(array $data, array $ounits)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })
            ->whereHas('status', function ($query) {
                $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                    ->join('statuses as s', 'rss.status_id', '=', 's.id')
                    ->where('s.name', EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value)
                    ->where('rss.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('enactment_status as sub_rss')
                            ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                    });
            });

        if (!empty($data['title'])) {
            $query->where(function ($query) use ($data) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$data['title']])
                    ->orWhereHas('title', function ($query) use ($data) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$data['title']]);
                    });
            });
        }

        return $query->with(['status', 'latestMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForHeyaatStatusEnactment(array $data, array $ounits)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })
            ->whereHas('status', function ($query) {
                $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                    ->join('statuses as s', 'rss.status_id', '=', 's.id')
                    ->where('s.name', EnactmentStatusEnum::PENDING_BOARD_REVIEW->value)
                    ->where('rss.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('enactment_status as sub_rss')
                            ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                    });
            });

        if (!empty($data['title'])) {
            $query->where(function ($query) use ($data) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$data['title']])
                    ->orWhereHas('title', function ($query) use ($data) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$data['title']]);
                    });
            });
        }

        return $query->with(['status', 'latestMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForArchiveStatusEnactment(array $data, array $ounits)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $statuses = $data['statusID'] ?? null;
        $searchTerm = $data['name'] ?? null;

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })
            ->whereHas('status', function ($query) use ($statuses) {
                $query->join('enactment_status as rss', 'enactments.id', '=', 'rss.enactment_id')
                    ->join('statuses as s', 'rss.status_id', '=', 's.id')
                    ->when($statuses, function ($query) use ($statuses) {
                        $query->where('rss.status_id', $statuses);
                    })
                    ->where('rss.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('enactment_status as sub_rss')
                            ->whereColumn('sub_rss.enactment_id', 'rss.enactment_id');
                    });
            });

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhereHas('title', function ($query) use ($searchTerm) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                    });
            });
        });


        return $query->with(['status', 'latestMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function storeEnactment(array|Collection $data, Meeting $meeting)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }
        $preparedData = $this->prepareEnactmentData($data, $meeting);
        $result = Enactment::create($preparedData->toArray()[0]);
        $status = $this->enactmentPendingSecretaryStatus();
        $enactmentStatus = new EnactmentStatus();
        $enactmentStatus->enactment_id = $result->id;
        $enactmentStatus->status_id = $status->id;
        $enactmentStatus->operator_id = $data[0]['creatorID'];
        $enactmentStatus->description = $data[0]['description'] ?? null;
        $enactmentStatus->attachment_id = $data[0]['attachmentID'] ?? null;
        $enactmentStatus->save();
//        $result->statuses()->attach($status->id);

        return $result;
    }

    private function prepareEnactmentData(array|Collection $data, Meeting $meeting)
    {
        if (is_array($data)) {
            $data = collect($data);
        }
        $nextId = DB::table('enactments')->max('id') + 1;

        $data = $data->map(function ($item) use ($meeting, &$nextId) {

            $meeting->load(['ounit.ancestors' => function ($query) {
                $query->whereIn('unitable_type', [CityOfc::class, DistrictOfc::class]);

            }]);
            $jDate = Jalalian::fromCarbon(Carbon::now())->format('Y/m/d');
            $autoSerial = $nextId . '/' . $meeting->ounit?->ancestors[0]->unitable_id . '/' . $meeting->ounit?->ancestors[1]->unitable_id . '/' . $jDate;

            $result = [
                'custom_title' => $item['customTitle'] ?? null,
                'description' => $item['description'] ?? null,
//                'rejection_reason' => $item['rejectionReason'] ?? null,
                'auto_serial' => $autoSerial,
                'serial' => $item['enactmentSerial'] ?? null,
                'title_id' => $item['titleID'] ?? null,
                'creator_id' => $item['creatorID'],
                'meeting_id' => $meeting->id ?? null,
//                'rejection_file_id' => $item['rejectionFileID'] ?? null,
                'create_date' => now(),
            ];
            $nextId++;

            return $result;
        });

        return $data;
    }

    public function readEnactment(int $id)
    {
        return Enactment::findOrFail($id);
    }

    public function updateEnactment(array $data, Enactment $enactment, Meeting $meeting)
    {
        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $preparedData = $this->prepareEnactmentData($data, $meeting)->toArray()[0];
        $enactment->update($preparedData);
        return $enactment;
    }

    public function deleteEnactment(int $id)
    {
        $enactment = Enactment::findOrFail($id);
        $enactment->delete();
        return $enactment;
    }

    public function attachFiles(Enactment $enactment, array $files)
    {
        $attachments = collect($files)->map(function ($file) use ($enactment) {
            return [
                'attachment_id' => $file['fileID'],
                'title' => $file['title'] ?? null,
                'attachmentable_id' => $enactment->id,
                'attachmentable_type' => Enactment::class,
            ];
        })->toArray();

        Attachmentable::insert($attachments);
    }

    private function getByRoleAndStatusCombination()
    {
        $combos = [
            self::$enactmentSecretaryStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$karshenasMashvarati,
                    self::$ozvHeyaat,
                    self::$ozvShouraRusta
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'AcceptDenyBtns',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'AcceptDenyBtns',
                ],
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                ],
            ],
            self::$enactmentHeyaatStatus => [
                'priorities' => [
                    self::$ozvHeyaat,
                    self::$karshenasMashvarati,
                    self::$karshenasOstandari,
                    self::$bakhshdar,
                    self::$dabirHeyaat,
                    self::$ozvShouraRusta

                ],

                //roles with components
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'ReviewBtn',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'ReviewBtn',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'BoardReviewCards',
                    'ConsultingReviewCards',
                ],
                self::$bakhshdar => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'BoardReviewCards',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'ConsultingReviewCards',

                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                ],

            ],
            self::$enactmentCompleteStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$karshenasMashvarati,
                    self::$ozvHeyaat,
                    self::$ozvShouraRusta
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'BoardReviewCards',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                ],
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                ],
            ],
            self::$enactmentCancelStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$karshenasMashvarati,
                    self::$ozvHeyaat,
                    self::$ozvShouraRusta
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                    'DenyCard',
                ],
            ],
            self::$enactmentDeclinedStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$karshenasMashvarati,
                    self::$ozvHeyaat,
                    self::$ozvShouraRusta
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                    'DenyCard',
                ],
            ],

        ];

        return $combos;
    }

    public function getComponentsToRender(array $userRoles, string $enactmentStatus): Collection
    {
        $statusCollection = collect($this->getByRoleAndStatusCombination());
        $userRolesCollection = collect($userRoles);

        $statusRoles = collect($statusCollection->get($enactmentStatus, []));
        $priorities = $statusRoles->get('priorities', []);

        // Check for priority roles in order
        foreach ($priorities as $priorityRole) {
            if ($userRolesCollection->contains($priorityRole) && $statusRoles->has($priorityRole)) {
                return collect($statusRoles->get($priorityRole, []));
            }
        }

        // Return components for other roles
        return $statusRoles
            ->filter(function ($components, $role) use ($userRolesCollection) {
                return $userRolesCollection->contains($role);
            })
            ->flatMap(function ($components) {
                return $components;
            });
    }

    public function enactmentShow(Enactment $enactment, User $user)
    {
        $userRoles = $user->roles->pluck('name')->toArray();

        $myPermissions = $this->getComponentsToRender($userRoles, $enactment->status->name);

        $componentsToRender = collect([
            'MainEnactment' => ['reviewStatuses', 'latestMeeting', 'attachments', 'creator', 'title', 'meeting.ounit.unitable', 'meeting.ounit.ancestorsAndSelf' => function ($q) {
                $q
                    ->where('unitable_type', '!=', StateOfc::class);
            }],
            'MembersBeforeReview' => ['districtOfc'],
            'AcceptDenyBtns' => ['relatedDates' => function ($query) {
                $query
                    ->with(['meetings' => function ($query) {
                        $query->whereHas('meetingType', function ($query) {
                            $query->where('title', '=', 'جلسه هیئت تطبیق');
                        })->where('meetings.meeting_date', '>', now())->where('meetings.isTemplate', false)
                            ->withCount('enactments');

                    }]);

            }],
            'ConsultingReviewCards' => ['consultingMembers.enactmentReviews' => function ($query) use ($enactment) {
                $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
            },
            ],
            'BoardReviewCards' => ['boardMembers.enactmentReviews' => function ($query) use ($enactment) {
                $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
            },],
            'CurrentReviewCard' => ['boardMembers' => function ($query) use ($enactment, $user) {
                $query->where('employee_id', $user->id)->with(['enactmentReviews' => function ($query) use ($enactment) {
                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);

                }]);
            },],

            'DenyCard' => ['canceledStatus.meetingMember'],
            'ReviewBtn' => [
                'members' => function ($query) use ($user) {
                    $query->where('employee_id', $user->id);
                },
                'userHasReviews' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },]
        ]);

        $flattenedComponents = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys())->toArray())
            ->flatMap(fn($relations) => collect($relations)->mapWithKeys(fn($relation, $key) => is_callable($relation) ? [$key => $relation] : [$relation => fn($query) => $query]))->all();


        $enactment = $enactment->load($flattenedComponents);

        $componentsWithData = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys()))->map(function ($relations, $component) use ($enactment) {
            $relationData = collect($relations)->mapWithKeys(function ($relation, $key) use ($enactment) {
                $relationName = is_callable($relation) ? explode('.', $key)[0] : explode('.', $relation)[0];

                if ($enactment->relationLoaded($relationName)) {
                    if (is_callable($relation)) {
                        $component = $key;
                    } else {
                        $component = $relation;
                    }
                    $result = [$component => $enactment->$relationName];
                    if ($relationName !== 'reviewStatuses') {
                        $enactment->unsetRelation($relationName);
                    }

                    return $result;
                }
                return [];

            });
            return $relationData->isNotEmpty() ? [
                'name' => $component,
                'data' => $relationData
            ] : null;
        })->filter()->values();

        return $componentsWithData;

    }

    public function enactmentPendingSecretaryStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value);
    }

    public function enactmentHeyaatStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::PENDING_BOARD_REVIEW->value);
    }

    public function enactmentPendingForHeyaatDateStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::PENDING_FOR_BOARD_DATE->value);
    }

    public function enactmentCompleteStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::COMPLETED->value);
    }

    public function enactmentCancelStatus()
    {
        return Enactment::GetAllStatuses()->firstWhere('name', EnactmentStatusEnum::CANCELED->value);
    }
}
