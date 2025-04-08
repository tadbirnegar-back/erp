<?php

namespace Modules\HRMS\app\Http\Traits;

use Illuminate\Support\Facades\Cache;
use Modules\HRMS\app\Models\Job;

trait JobTrait
{
    private string $activeJobName = 'فعال';
    private string $inactiveJobName = 'غیرفعال';

    public function createJob(array $data): Job
    {
        $job = new Job();
        $job->title = $data['title'];
        $job->description = $data['description'] ?? null;
        $job->introduction_video_id = $data['introVideoID'] ?? null;
        $status = $this->activeJobStatus();
        $job->status_id = $status->id;
        $job->save();
        $job->load('introVideo');
        return $job;
    }

    public function getSingleJob(int $id): ?Job
    {
        return Job::find($id);
    }

    public function getListOfJobs()
    {
        return Job::whereHas('status', function ($query) {
            $query->where('name', '=', $this->activeJobName);
        })->with('introVideo')->get();
    }

    public function updateJob(Job $job, array $data): Job
    {
        $job->title = $data['title'] ?? null;
        $job->description = $data['description'] ?? null;
        $job->introduction_video_id = $data['introVideoID'] ?? null;
        $job->save();

        $job->load('introVideo');


        return $job;
    }

    public function deleteJob(Job $job)
    {
        $status = $this->inactiveJobStatus();
        $job->status_id = $status->id;
        $job->save();
        return $job;
    }

    public function activeJobStatus()
    {
        return Cache::rememberForever('job_active_status', function () {
            return Job::GetAllStatuses()
                ->firstWhere('name', '=', $this->activeJobName);
        });
    }

    public function inactiveJobStatus()
    {
        return Cache::rememberForever('job_inactive_status', function () {
            return Job::GetAllStatuses()
                ->firstWhere('name', '=', $this->inactiveJobName);
        });
    }
}
