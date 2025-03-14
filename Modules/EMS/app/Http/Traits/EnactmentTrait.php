<?php

namespace Modules\EMS\app\Http\Traits;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Collection;
use Modules\AAA\app\Models\User;
use Modules\EMS\app\Http\Enums\EnactmentStatusEnum;
use Modules\EMS\app\Http\Enums\MeetingTypeEnum;
use Modules\EMS\app\Http\Enums\RolesEnum;
use Modules\EMS\app\Models\Attachmentable;
use Modules\EMS\app\Models\Enactment;
use Modules\EMS\app\Models\Meeting;
use Modules\EMS\app\Models\MeetingType;
use Modules\OUnitMS\app\Models\CityOfc;
use Modules\OUnitMS\app\Models\DistrictOfc;
use Modules\OUnitMS\app\Models\FreeZone;
use Modules\OUnitMS\app\Models\OrganizationUnit;
use Modules\OUnitMS\app\Models\StateOfc;
use Modules\OUnitMS\app\Models\VillageOfc;
use Morilog\Jalali\Jalalian;

trait EnactmentTrait
{
    private static string $enactmentSecretaryStatus = EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value;
    private static string $enactmentHeyaatStatus = EnactmentStatusEnum::PENDING_BOARD_REVIEW->value;
    private static string $enactmentCompleteStatus = EnactmentStatusEnum::COMPLETED->value;
    private static string $enactmentCancelStatus = EnactmentStatusEnum::CANCELED->value;
    private static string $enactmentDeclinedStatus = EnactmentStatusEnum::DECLINED->value;
    private static string $enactmentPendingForHeyaatDateStatus = EnactmentStatusEnum::PENDING_FOR_BOARD_DATE->value;


    //=========================== roles =============================

    private static string $bakhshdar = RolesEnum::BAKHSHDAR->value;
    private static string $karshenasOstandari = RolesEnum::KARSHENAS_OSTANDARI->value;
    private static string $dabirHeyaat = RolesEnum::DABIR_HEYAAT->value;
    private static string $karshenasMashvarati = RolesEnum::KARSHENAS_MASHVARATI->value;
    private static string $ozvHeyaat = RolesEnum::OZV_HEYAAT->value;
    private static string $ozvShouraRusta = RolesEnum::OZV_SHOURA_RUSTA->value;
    private static string $dabirFreeZone = RolesEnum::DABIR_FREEZONE->value;
    private static string $karshenasMashveratiFZ = RolesEnum::KARSHENAS_MASHVERATI_FREEZONE->value;
    private static string $ozvHeyatFZ = RolesEnum::OZV_HEYAT_FREEZONE->value;
    private static string $raiesMantagheAzad = RolesEnum::RAIES_MANTAGHE_AZAD->value;

    public function indexPendingForSecretaryStatusEnactment(array $data, array $ounits)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;


        if (!empty($data['ounitID'])) {
            $ounits = [$data['ounitID']];
        }
        if (isset($data['district'])) {
            // Get the organization unit and its children
            $ounits = [$data['district']];
        }

        $mt = MeetingType::whereIn('title', [MeetingTypeEnum::HEYAAT_MEETING->value, MeetingTypeEnum::FREE_ZONE->value])->first();


        $query = Enactment::whereHas('meeting', function ($query) use ($ounits, $mt) {
            $query->whereIntegerInRaw('ounit_id', $ounits)
                ->where('meeting_type_id', $mt->id);
        })
            ->whereHas('status', function ($query) {
                $query->join('enactment_status as es', 'enactments.id', '=', 'es.enactment_id')
                    ->join('statuses as s', 'es.status_id', '=', 's.id')
                    ->where('s.name', EnactmentStatusEnum::PENDING_SECRETARY_REVIEW->value)
                    ->where('es.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('enactment_status as sub_rss')
                            ->whereColumn('sub_rss.enactment_id', 'es.enactment_id');
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

        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $dateStart = convertJalaliPersianCharactersToGregorian($data['startDate']);
            $dateEnd = convertJalaliPersianCharactersToGregorian($data['endDate']);

            $query->whereHas('latestMeeting', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('meeting_date', [$dateStart, $dateEnd]);
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

        if (!empty($data['ounitID'])) {
            $ounits = [$data['ounitID']];
        }

        if (isset($data['district'])) {
            // Get the organization unit and its children
            $ounits = [$data['district']];
        }

        $mt = MeetingType::where('title', MeetingTypeEnum::HEYAAT_MEETING->value)->first();


        $query = Enactment::whereHas('meeting', function ($query) use ($ounits, $mt) {
            $query->whereIntegerInRaw('ounit_id', $ounits)
                ->where('meeting_type_id', $mt->id);
        })
            ->whereHas('status', function ($query) {
                $query->join('enactment_status as es', 'enactments.id', '=', 'es.enactment_id')
                    ->join('statuses as s', 'es.status_id', '=', 's.id')
                    ->where('s.name', EnactmentStatusEnum::PENDING_BOARD_REVIEW->value)
                    ->where('es.create_date', function ($subQuery) {
                        $subQuery->selectRaw('MAX(create_date)')
                            ->from('enactment_status as sub_rss')
                            ->whereColumn('sub_rss.enactment_id', 'es.enactment_id');
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
        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $dateStart = convertJalaliPersianCharactersToGregorian($data['startDate']);
            $dateEnd = convertJalaliPersianCharactersToGregorian($data['endDate']);

            $query->whereHas('latestMeeting', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('meeting_date', [$dateStart, $dateEnd]);
            });
        }
        return $query->with(['status', 'latestMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForArchiveStatusEnactment(array $data, array $ounits, $userId)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $statuses = $data['statusID'] ?? null;
        if (!is_null($statuses)) {
            $statuses = json_decode($statuses);
        }
        $reviewStatus = $data['reviewStatusID'] ?? null;
        $searchTerm = $data['title'] ?? null;
        if (!empty($data['ounitID'])) {
            $ounits = [$data['ounitID']];
        }


        if (isset($data['freeZoneID'])) {
            $ounits = OrganizationUnit::with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])->find($data['freeZoneID'])->descendantsAndSelf->flatten()
                ->pluck('id')
                ->toArray();
        }

        if (isset($data['districtID'])) {

            $ounits = OrganizationUnit::with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])->find($data['districtID'])->descendantsAndSelf->flatten()
                ->pluck('id')
                ->toArray();
        }

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })->with(['enactmentReviews' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
            $query->with('status');
        }]);
        $query->when($statuses, function ($query) use ($statuses) {
            $query->whereHas('status', function ($query) use ($statuses) {
                $query->whereIn('status_id', $statuses)
                    ->where('enactment_status.id', function ($subQuery) {
                        $subQuery->select(DB::raw('MAX(id)'))
                            ->from('enactment_status')
                            ->whereColumn('enactment_id', 'enactments.id');
                    });
            });
        });


        $query->when($reviewStatus, function ($query) use ($reviewStatus) {
            if ($reviewStatus == -1) {
                $query->where('final_status_id', null);
            } else {
                $query->where('final_status_id', $reviewStatus);
            }
        });

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhereHas('title', function ($query) use ($searchTerm) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                    });
            });
        });
        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $dateStart = convertJalaliPersianCharactersToGregorian($data['startDate']);
            $dateEnd = convertJalaliPersianCharactersToGregorian($data['endDate']);

            $query->whereHas('latestMeeting', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('meeting_date', [$dateStart, $dateEnd]);
            });
        }


        return $query->with(['status', 'latestHeyaatMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf', 'finalStatus'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForFreeZoneByDistricStatusEnactment(array $data, array $ounits, $userId)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $statuses = $data['statusID'] ?? null;
        if (!is_null($statuses)) {
            $statuses = json_decode($statuses);
        }
        $reviewStatus = $data['reviewStatusID'] ?? null;
        $searchTerm = $data['title'] ?? null;
        if (!empty($data['ounitID'])) {
            $ounits = [$data['ounitID']];
        }


        if (isset($data['freeZoneID'])) {
            $ounits = OrganizationUnit::with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])->find($data['freeZoneID'])->descendantsAndSelf->flatten()
                ->pluck('id')
                ->toArray();
        }

        if (isset($data['districtID'])) {

            $ounits = OrganizationUnit::with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])->find($data['districtID'])->descendantsAndSelf->flatten()
                ->pluck('id')
                ->toArray();
        }

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })->with(['enactmentReviews' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
            $query->with('status');
        }])->whereHas('latestFreeZoneMeeting');
        $query->when($statuses, function ($query) use ($statuses) {
            $query->whereHas('status', function ($query) use ($statuses) {
                $query->whereIn('status_id', $statuses)
                    ->where('enactment_status.id', function ($subQuery) {
                        $subQuery->select(DB::raw('MAX(id)'))
                            ->from('enactment_status')
                            ->whereColumn('enactment_id', 'enactments.id');
                    });
            });
        });


        $query->when($reviewStatus, function ($query) use ($reviewStatus) {
            if ($reviewStatus == -1) {
                $query->where('final_status_id', null);
            } else {
                $query->where('final_status_id', $reviewStatus);
            }
        });

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhereHas('title', function ($query) use ($searchTerm) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                    });
            });
        });
        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $dateStart = convertJalaliPersianCharactersToGregorian($data['startDate']);
            $dateEnd = convertJalaliPersianCharactersToGregorian($data['endDate']);

            $query->whereHas('latestMeeting', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('meeting_date', [$dateStart, $dateEnd]);
            });
        }


        return $query->with(['status', 'latestHeyaatMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf', 'finalStatus'])
            ->orderBy('create_date', 'desc')
            ->paginate($perPage, ['*'], 'page', $pageNum);
    }

    public function indexPendingForFreeZoneEnactment(array $data, array $ounits, $userId)
    {
        $perPage = $data['perPage'] ?? 10;
        $pageNum = $data['pageNum'] ?? 1;
        $statuses = $data['statusID'] ?? null;
        if (!is_null($statuses)) {
            $statuses = json_decode($statuses);
        }
        $reviewStatus = $data['reviewStatusID'] ?? null;
        $searchTerm = $data['title'] ?? null;
        if (!empty($data['ounitID'])) {
            $ounits = [$data['ounitID']];
        }

        if (isset($data['districtID'])) {

            $ounits = OrganizationUnit::with(['descendantsAndSelf' => function ($query) {
                $query->where('unitable_type', VillageOfc::class);
            }])->find($data['districtID'])->descendantsAndSelf->flatten()
                ->pluck('id')
                ->toArray();
        }

        $query = Enactment::whereHas('meeting', function ($query) use ($ounits) {
            $query->whereIntegerInRaw('ounit_id', $ounits);
        })->with(['enactmentReviews' => function ($query) use ($userId) {
            $query->where('user_id', $userId);
            $query->with('status');
        }])->whereHas('latestFreeZoneMeeting');
        $query->when($statuses, function ($query) use ($statuses) {
            $query->whereHas('status', function ($query) use ($statuses) {
                $query->whereIn('status_id', $statuses)
                    ->where('enactment_status.id', function ($subQuery) {
                        $subQuery->select(DB::raw('MAX(id)'))
                            ->from('enactment_status')
                            ->whereColumn('enactment_id', 'enactments.id');
                    });
            });
        });


        $query->when($reviewStatus, function ($query) use ($reviewStatus) {
            if ($reviewStatus == -1) {
                $query->where('final_status_id', null);
            } else {
                $query->where('final_status_id', $reviewStatus);
            }
        });

        $query->when($searchTerm, function ($query) use ($searchTerm) {
            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('MATCH(custom_title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm])
                    ->orWhereHas('title', function ($query) use ($searchTerm) {
                        $query->whereRaw('MATCH(title) AGAINST(? IN BOOLEAN MODE)', [$searchTerm]);
                    });
            });
        });
        if (!empty($data['startDate']) && !empty($data['endDate'])) {
            $dateStart = convertJalaliPersianCharactersToGregorian($data['startDate']);
            $dateEnd = convertJalaliPersianCharactersToGregorian($data['endDate']);

            $query->whereHas('latestMeeting', function ($q) use ($dateStart, $dateEnd) {
                $q->whereBetween('meeting_date', [$dateStart, $dateEnd]);
            });
        }


        return $query->with(['status', 'latestHeyaatMeeting', 'reviewStatuses', 'title', 'ounit.ancestorsAndSelf', 'finalStatus'])
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

            $autoSerial = $nextId . '/' . $meeting->ounit?->unitable_id . '/' . $meeting->ounit?->ancestors[0]->unitable_id . '/' . $jDate;

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
                'receipt_date' => now()
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
                    self::$raiesMantagheAzad,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$dabirFreeZone,
                    self::$karshenasMashvarati,
                    self::$karshenasMashveratiFZ,
                    self::$ozvHeyaat,
                    self::$ozvHeyatFZ,
                    self::$ozvShouraRusta,
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ],
                self::$raiesMantagheAzad => [
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

                self::$dabirFreeZone => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'AcceptDenyBtns',
                ],
                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'MembersBeforeReview',
                ]
            ],
            self::$enactmentPendingForHeyaatDateStatus => [
                'priorities' => [
                    self::$dabirHeyaat,
                    self::$dabirFreeZone,
                    self::$karshenasMashvarati,
                    self::$karshenasMashveratiFZ,
                    self::$ozvHeyaat,
                    self::$ozvHeyatFZ,
                    self::$karshenasOstandari,
                    self::$bakhshdar,
                    self::$raiesMantagheAzad,
                    self::$ozvShouraRusta,
                ],

                //roles with components
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'ReviewBtn',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',

                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'BoardReviewCards',
                ],
                self::$bakhshdar => [
                    'MainEnactment',
                ],
                self::$raiesMantagheAzad => [
                    'MainEnactment',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'RevokeBtn',
                    'CurrentReviewCard',
                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                ],

                self::$dabirFreeZone => [
                    'MainEnactment',
                    'RevokeBtn',
                    'CurrentReviewCard',
                ],

                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'ReviewBtn',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ]

            ],
            self::$enactmentHeyaatStatus => [
                'priorities' => [
                    self::$ozvHeyaat,
                    self::$ozvHeyatFZ,
                    self::$karshenasMashvarati,
                    self::$karshenasMashveratiFZ,
                    self::$karshenasOstandari,
                    self::$bakhshdar,
                    self::$raiesMantagheAzad,
                    self::$dabirHeyaat,
                    self::$dabirFreeZone,
                    self::$ozvShouraRusta,
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
                ],
                self::$raiesMantagheAzad => [
                    'MainEnactment',
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',

                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',
                ],
                self::$dabirFreeZone => [
                    'MainEnactment',
                ],
                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'ReviewBtn',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'ReviewBtn',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ]

            ],
            self::$enactmentCompleteStatus => [
                'priorities' => [
                    self::$karshenasOstandari,
                    self::$bakhshdar,
                    self::$raiesMantagheAzad,
                    self::$dabirHeyaat,
                    self::$dabirFreeZone,
                    self::$karshenasMashvarati,
                    self::$karshenasMashveratiFZ,
                    self::$ozvHeyaat,
                    self::$ozvHeyatFZ,
                    self::$ozvShouraRusta,
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ],
                self::$raiesMantagheAzad => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ],
                self::$karshenasOstandari => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'BoardReviewCards',
                    'FormNumThree'
                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'CurrentReviewCard',
                    'FormNumThree',
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
                self::$dabirFreeZone => [
                    'MainEnactment',
                    'CurrentReviewCard',
                    'FormNumThree',
                ],
                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'CurrentReviewCard',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'ConsultingReviewCards',
                    'CurrentReviewCard',
                ]
            ],
            self::$enactmentCancelStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$karshenasMashvarati,
                    self::$ozvHeyaat,
                    self::$ozvShouraRusta,
                    self::$dabirFreeZone,
                    self::$karshenasMashveratiFZ,
                    self::$ozvHeyatFZ
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'DenyCard',

                ],
                self::$raiesMantagheAzad => [
                    'MainEnactment',
                    'DenyCard',
                ],

                self::$karshenasOstandari => [
                    'MainEnactment',
                    'DenyCard',

                ],
                self::$dabirHeyaat => [
                    'MainEnactment',
                    'DenyCard',

                ],
                self::$karshenasMashvarati => [
                    'MainEnactment',
                    'DenyCard',

                ],
                self::$ozvHeyaat => [
                    'MainEnactment',
                    'DenyCard',

                ],
                self::$ozvShouraRusta => [
                    'MainEnactment',

                ],
                self::$dabirFreeZone => [
                    'MainEnactment',
                    'DenyCard',
                ],
                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'DenyCard',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'DenyCard',
                ]
            ],
            self::$enactmentDeclinedStatus => [
                'priorities' => [
                    self::$bakhshdar,
                    self::$raiesMantagheAzad,
                    self::$karshenasOstandari,
                    self::$dabirHeyaat,
                    self::$dabirFreeZone,
                    self::$karshenasMashvarati,
                    self::$karshenasMashveratiFZ,
                    self::$ozvHeyaat,
                    self::$ozvHeyatFZ,
                    self::$ozvShouraRusta,
                ],

                //roles with components
                self::$bakhshdar => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$raiesMantagheAzad => [
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
                self::$dabirFreeZone => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$karshenasMashveratiFZ => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ],
                self::$ozvHeyatFZ => [
                    'MainEnactment',
                    'MembersBeforeReview',
                    'DenyCard',
                ]
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
                            $query->whereIn('title', ['جلسه هیئت تطبیق', MeetingTypeEnum::FREE_ZONE->value]);
                        })->where('meetings.meeting_date', '>', now())->where('meetings.isTemplate', false)
                            ->withCount('enactments');

                    }]);

            }],
            'ConsultingReviewCards' => [
                'members' => function ($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->with([
                            'roles' => function ($q) {
                                $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value, RolesEnum::OZV_HEYAT_FREEZONE])
                                    ->orWhereIn('name', [RolesEnum::KARSHENAS_MASHVARATI->value, RolesEnum::KARSHENAS_MASHVERATI_FREEZONE])
                                    ->distinct();
                            }]);
                },
                'consultingMembers.enactmentReviews' => function ($query) use ($enactment) {
                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
                },
            ],
            'BoardReviewCards' => ['boardMembers.enactmentReviews' => function ($query) use ($enactment) {
                $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);
            },],
            'CurrentReviewCard' => ['membersNew' => function ($query) use ($enactment, $user) {
                $query->where('employee_id', $user->id)->with(['enactmentReviews' => function ($query) use ($enactment) {
                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment']);

                }, 'person.avatar', 'mr'
                ]);
            },],

            'DenyCard' => ['canceledStatus.meetingMember'],
            'ReviewBtn' => [
                'members' => function ($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->with([
                            'roles' => function ($q) {
                                $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value, RolesEnum::OZV_HEYAT_FREEZONE])
                                    ->orWhereIn('name', [RolesEnum::KARSHENAS_MASHVARATI->value, RolesEnum::KARSHENAS_MASHVERATI_FREEZONE])
                                    ->distinct();
                            }]);
                },
                'userHasReviews' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },],
            'RescheduleBtn' => ['relatedDates' => function ($query) {
                $query
                    ->with(['meetings' => function ($query) {
                        $query->whereHas('meetingType', function ($query) {
                            $query->whereIn('title', ['جلسه هیئت تطبیق', MeetingTypeEnum::FREE_ZONE->value]);
                        })->where('meetings.meeting_date', '>', now())->where('meetings.isTemplate', false)
                            ->withCount('enactments');

                    }]);

            }],
            'FormNumThree' => [
                'members' => function ($query) use ($user) {
                    $query->where('employee_id', $user->id)
                        ->with([
                            'roles' => function ($q) {
                                $q->whereIn('name', [RolesEnum::OZV_HEYAAT->value, RolesEnum::OZV_HEYAT_FREEZONE])
                                    ->orWhereIn('name', [RolesEnum::KARSHENAS_MASHVARATI->value, RolesEnum::KARSHENAS_MASHVERATI_FREEZONE])
                                    ->distinct();
                            }]);
                },
                // MainEnactment logic
                'reviewStatuses',
                'latestMeeting',
                'creator',
                'title',
                'meeting.ounit.unitable',
                'meeting.ounit.ancestorsAndSelf',

                'consultingMembers.enactmentReviews' => function ($query) use ($enactment) {
                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment'])
                        ->with('user.employee.signatureFile');
                },
                // BoardReviewCards logic
                'boardMembers.enactmentReviews' => function ($query) use ($enactment) {
                    $query->where('enactment_id', $enactment->id)->with(['status', 'attachment'])
                        ->with('user.employee.signatureFile');
                },
            ],
            'RevokeBtn' => [
                'members' => function ($query) use ($user) {
                    $query->where('employee_id', $user->id);

                },
                'userHasReviews' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },],
        ]);

        $flattenedComponents = $componentsToRender->only($myPermissions->intersect($componentsToRender->keys())->toArray())
            ->flatMap(fn($relations) => collect($relations)->mapWithKeys(fn($relation, $key) => is_callable($relation) ? [$key => $relation] : [$relation => fn($query) => $query]))->all();


        $enactment = $enactment->load($flattenedComponents);
//        return $enactment;
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
