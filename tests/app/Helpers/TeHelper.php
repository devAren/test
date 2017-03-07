<?php
namespace DTApi\Helpers;

use Carbon\Carbon;
use DTApi\Models\Job;
use DTApi\Models\User;
use DTApi\Models\Language;
use DTApi\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeHelper
{

    /**
     * @param $id
     * @return mixed
     */
    public static function fetchLanguageFromJobId($id)
    {
        $language = Language::findOrFail($id);
        if($language)
            return $language->language;
        else
            return '';
    }

    /**
     * @param $user_id
     * @param bool $key
     * @return string
     */
    public static function getUsermeta($user_id, $key = false)
    {
        $user = UserMeta::where('user_id', $user_id)->first();
        if (!$key) {
            $meta = UserMeta::where('user_id', $user_id)->all();
        } else {
            $meta = UserMeta::where('key', '=', $key)->get()->first();
            $meta = $user->usermeta()->where('key', '=', $key)->get()->first()
        }
        if ($meta && $key)
            return $meta->value;
        else if($meta && !$key)
            return $meta;
        else
            return '';

    }

    /**
     * @param $jobs_ids
     * @return array
     */
    public static function convertJobIdsInObjs($jobs_ids = array())
    {
        $jobs = array();
        foreach ($jobs_ids as $job_obj) {
            $jobs[] = Job::findOrFail($job_obj->id);
        }
        return $jobs;
    }

    /**
     * @param $due_time
     * @param $created_at
     * @return mixed
     */
    public static function willExpireAt($due_time, $created_at)
    {
        $due_time = Carbon::parse($due_time);
        $created_at = Carbon::parse($created_at);
        $difference = $due_time->diffInHours($created_at);

        if ($difference <= 24)
            $time = $created_at->addMinutes(90);
        elseif ($difference > 24 && $difference <= 72)
            $time = $created_at->addHours(16);
        elseif ($difference <= 90)
            $time = $due_time;
        else
            $time = $due_time->subHours(48);

        return $time->format('Y-m-d H:i:s');
    }

}

