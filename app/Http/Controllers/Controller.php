<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\{ Log, DB, File };

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

    /**
     * Convert base64 to photo & upload.
     * @param  \Illuminate\Http\Request  $request
     * @return $image_url
     */
    public function uploadBase64toPhoto($photo, $directory, $previous_image = null)
    {   
        try {
            $directory = $directory .'/';

            $base64Image = $photo;
            $imageData   = base64_decode($base64Image);
            $filename    = time() . '.png';

            if (!file_exists(storage_path($directory))) {
                mkdir(storage_path($directory), 0777, true);
            }

            file_put_contents(storage_path($directory . $filename), $imageData);

            $imageUrl = url($directory. $filename);

            if (!empty($previous_image)) {
                $this->deleteImage($previous_image, $directory);
            }
            
            return $imageUrl;

        } catch (\Exception $ex) {
            Log::info('image upload ex = '. $ex->getMessage());
            return false;
        }
    }

     /**
     * Delete specific image
     * @param  $image path
     * @return always true
     */
    public function deleteImage($imagePath, $directory)
    {
        $file_name  = substr($imagePath, strrpos($imagePath, '/') + 1);
        $path       = $directory . $file_name;            
        File::delete($path);

        return true;
    }

}
