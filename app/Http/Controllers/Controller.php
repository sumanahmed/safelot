<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected $activityLog;

    public function __construct(ActivityLog $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    /**
     * activity log
     * @param  $user_id
     * @return boolean
     */
    public function storeActivityLog($user_id, $request)
    {
        $data = array(
            'user_id'   => $user_id,
            'url'       => $request->getUri(),
            'method'    => $request->getMethod(),
            'body'      => json_encode($request->all()),  
            'date'      => date("Y-m-d")        
        );

        try {
            $this->activityLog->create($data);
        } catch (\Exception $ex) {
            return $ex;
        }

        return true;
    }

}
