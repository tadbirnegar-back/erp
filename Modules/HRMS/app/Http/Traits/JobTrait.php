<?php

namespace Modules\HRMS\app\Http\Traits;

use Modules\HRMS\app\Models\Job;

trait JobTrait
{
    public function createJob(array $data): Job
    {
        $job = new Job();
        $job->title = $data['title'];
        $job->description = $data['description'] ?? null;
        $job->introduction_video_id = $data['videoID'] ?? null;
        $job->save();

        return $job;
    }

    public function getSingleJob(int $id): ?Job
    {
        return Job::find($id);
    }

    public function getListOfJobs()
    {
        return Job::all();
    }

    public function updateJob(Job $job, array $data): Job
    {
        $job->title = $data['title'] ?? $job->title;
        $job->description = $data['description'] ?? $job->description;
        $job->introduction_video_id = $data['videoID'] ?? $job->introduction_video_id;
        $job->save();

        return $job;
    }

    public function deleteJob(int $id): bool
    {
        $job = Job::findOrFail($id);
        return $job->delete();
    }
}
