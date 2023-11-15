<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
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
    public function uploadBase64toPhoto($image, $destinationPath, $width = null, $height = null, $previousImage = null)
    {   
        try {
            // $directory = $directory .'/';
            // $base64Image = $photo;
            // $imageData   = base64_decode($base64Image);
            // $filename    = time() . '.png';
            // if (!file_exists(storage_path($directory))) {
            //     mkdir(storage_path($directory), 0777, true);
            // }
            // file_put_contents(storage_path($directory . $filename), $imageData);
            // $imageUrl = url($directory. $filename);



            
            $image_parts = explode(";base64,", $image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $ext = $image_type_aux[1];

            $image = str_replace('data:image/'.$ext.';base64,', '', $image);
            $file  = str_replace(' ', '+', $image);

            // if (!Str::startsWith($destinationPath, '/')) {
            //     $destinationPath = '/' . $destinationPath;
            // }

            $finalDestinationPath = 'app/public/' . $destinationPath;

            self::checkDirectory($finalDestinationPath);

            $imageResize = Image::make(base64_decode($file));
            $name = rand(100000, 999999) . time() . '.' . $ext;

            if (!empty($width) && !empty($height)) {

                $orgWidth  = $imageResize->width();
                $orgHeight = $imageResize->height();

                if ($orgWidth >= $orgHeight) {
                    $imageResize->resize($width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } else {
                    $imageResize->resize(null, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }

            $imageResize->save(storage_path("{$finalDestinationPath}/{$name}"));

            $imageUrl = $destinationPath. '/' . $name;



            if (!empty($previousImage)) {
                $this->deleteImage($previousImage, $finalDestinationPath);
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

    /**
     * Create directory and set permission
     *
     * @param string $dir The directory in which file to be uploaded
     * @return void
     */
    private static function checkDirectory($dir)
    {
        File::makeDirectory(storage_path($dir), 0777, true, true);
    }

}
