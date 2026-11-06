<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class Images extends Controller
{
    public function emotionsCatigorise($name)
    {
        $typeArray = [
            'on_phone_calm' => 9,
            'on_phone_pleased' => 10,
            'on_phone_happy' => 11,
            'hologram_calm' => 12,
            'hologram_happy' => 13,
            'hologram_angry' => 14,
            'hologram_pleased' => 15,
            'hologram_worry' => 16,
            'hologram_thoughtful' => 17,
            'hologram_sad' => 18,
            'happy_1' => 20,
            'happy' => 0,
            'calm' => 1,
            'angry' => 2,
            'sad' => 3,
            'worry' => 4,
            'sleeping' => 5,
            'thoughtful' => 6,
            'pleased' => 7,
            'stranger' => 8,
            'with_box' => 19,
            'shocked' => 21

        ];

        [$keys] = Arr::divide($typeArray);

        $neededType = null;

        foreach ($keys as $emotion) {
            if (str_contains($name, $emotion)) {
                $neededType = $typeArray[$emotion];
                break;
            }
        }
        return $neededType;
    }

    public function iconScaleCheck($scale)
    {
        $arr = ['L', 'l', 'm', 'M', 'S', 's'];
        $res = 'L';
        foreach ($arr as $item) {
            if ($scale == $item) {
                $res = $scale;
                break;
            }
        }
        return $res;

    }

    public function deleteFiles($name,$filePath)
    {
        $imageName[0] = $name . '_L' . '.webp';
        $imageName[1] = $name . '_M' . '.webp';
        $imageName[2] = $name . '_S' . '.webp';
        $imageName[3] = $name . '_locked_L' . '.webp';
        $imageName[4] = $name . '_locked_M' . '.webp';
        $imageName[5] = $name . '_locked_S' . '.webp';

        foreach ($imageName as $names)
        if (\Storage::disk('gcs')->exists("$filePath/$names")) {
            \Storage::disk('gcs')->delete("$filePath/$names");
        }
    }

    private function lAndMAndSSizesGeneration($entity, $nameL, $nameM, $nameS, $file)
    {
        $pubPath = public_path();
        Storage::disk('gcs')->putFileAs("/$entity/", $file, "$nameL");

        //webp - M


        exec("identify -format '%h' " . '"' . $pubPath . "/$entity/" . $nameL . '"', $out);

        $imgHeightInit = (int)str_replace("'", "", $out[0]);


        $imgHeight = (int)round($imgHeightInit * 2 / 3);


        exec("convert -resize x$imgHeight " . '"' . $pubPath . "/$entity/" . $nameL . '"' . " " . '"' . $pubPath . "/$entity/" . $nameM . '"');
        Storage::disk('gcs')->putFileAs(
            "/$entity/",
            Storage::disk('gcs')->get("$pubPath/$entity/$nameM"),
            "$nameM");

        //webp - S
        $imgHeight = (int)round($imgHeight / 3);

        exec("convert -resize x$imgHeight " . '"' . $pubPath . "/$entity/" . $nameM . '"' . " " . '"' . $pubPath . "/$entity/" . $nameS . '"');
        Storage::disk('gcs')->putFileAs(
            "/$entity/",
            Storage::disk('gcs')->get("$pubPath/$entity/$nameS"),
            "$nameS");
    }

    public function pngConvertAdventures( $itemId, $entity, $image, $locked = true)
    {
        $imageNamePNGL = $itemId . '_L' . '.png';
        $imageNameL = $itemId . '_L' . '.webp';
        $imageNameM = $itemId . '_M' . '.webp';
        $imageNameS = $itemId . '_S' . '.webp';

        if ($locked) {
            $imageNameLockedL = $itemId . '_locked_L' . '.webp';
            $imageNameLockedM = $itemId . '_locked_M' . '.webp';
            $imageNameLockedS = $itemId . '_locked_S' . '.webp';
        }
        $pubPath = public_path();
//        $image = str_replace('data:image/png;base64,', '', $image);
//        $image = str_replace(' ', '+', $image);


//        Storage::disk($host . '_' . $entity . '_uploads')->put($imageNamePNGL, base64_decode($image));
//        Storage::disk('gcs')->putFileAs("$entity",$image,$imageNamePNGL);
//        $file = Storage::disk('gcs')->get("$entity/$imageNamePNGL");



        $image = Image::make($image);
        (string)$image->save("$pubPath/$entity/$imageNameL", 94, 'webp');
        $image = Image::make("$pubPath/$entity/$imageNameL");

        Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameL", $imageNameL);


        if ($locked) {
            $image->backup();

            $image->greyscale()->save("$pubPath/$entity/$imageNameLockedL" , 100);
            $imageLocked = Image::make("$pubPath/$entity/$imageNameLockedL");

            Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameLockedL", $imageNameLockedL);

            $image->reset();
        }
        $imgHeightInit = $image->height();
        $imgHeight = round($imgHeightInit * 2 / 3, 0, PHP_ROUND_HALF_UP);

        $image->resize(null, $imgHeight, function ($constraint) {
            $constraint->aspectRatio();
//                $constraint->upsize(); // prevent possible upsizing
        })->save("$pubPath/$entity/$imageNameM");

        Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameM", $imageNameM);

//
        $imgHeight = round($imgHeightInit / 3, 0, PHP_ROUND_HALF_UP);

        $image->resize(null, $imgHeight, function ($constraint) {
            $constraint->aspectRatio();
//                $constraint->upsize(); // prevent possible upsizing
        })->save("$pubPath/$entity/$imageNameS" );

        Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameS", $imageNameS);


        if ($locked) {
            $imgLockedHeightInit = $imageLocked->height();
            $imgLockedHeight = round($imgLockedHeightInit * 2 / 3, 0, PHP_ROUND_HALF_UP);

            $imageLocked->resize(null, $imgLockedHeight, function ($constraint) {
                $constraint->aspectRatio();
//                $constraint->upsize(); // prevent possible upsizing
            })->save("$pubPath/$entity/$imageNameLockedM");

            Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameLockedM", $imageNameLockedM);

            $imgLockedHeight = round($imgLockedHeightInit / 3, 0, PHP_ROUND_HALF_UP);

            $imageLocked->resize(null, $imgLockedHeight, function ($constraint) {
                $constraint->aspectRatio();
//                $constraint->upsize(); // prevent possible upsizing
            })->save("$pubPath/$entity/$imageNameLockedS" );

            Storage::disk('gcs')->putFileAs("$entity","$pubPath/$entity/$imageNameLockedS", $imageNameLockedS);
        }
    }

//    public function pngConvertAdventures($itemId, $entity, $locked = true, $file = null)
//    {
//        $imageNamePNGL = $itemId . '.png';
//        if ($file <> null) {
//            $imageNamePNGL = $file . '.png';
//        }
//
//        $imageNameL = $itemId . '_L' . '.webp';
//        $imageNameM = $itemId . '_M' . '.webp';
//        $imageNameS = $itemId . '_S' . '.webp';
//        $pubPath = public_path();
//
//
//        exec('convert ' . '"' . $pubPath . "/$entity/" . $imageNamePNGL . '"' . ' -quality 94 -define webp:lossless=false ' . '"' . $pubPath . "/$entity/" . $imageNameL . '"');
//
//        $file = Storage::disk('gcs')->get("$pubPath/$entity/$imageNameL");
//
//        $this->lAndMAndSSizesGeneration($entity, $imageNameL, $imageNameM, $imageNameS, $file);
//
//        if ($locked) {
//            $imageNameLockedL = $itemId . '_locked_L' . '.webp';
//            $imageNameLockedM = $itemId . '_locked_M' . '.webp';
//            $imageNameLockedS = $itemId . '_locked_S' . '.webp';
//
//            //convert <img_in> -set colorspace Gray -separate -average <img_out>
//            exec('convert ' . '"' . $pubPath . "/$entity/" . $imageNameL . '"' . ' -set colorspace Gray -separate -average ' . '"' . $pubPath . "/$entity/" . $imageNameLockedL . '"');
//
//            $file = Storage::disk('gcs')->get("$pubPath/$entity/$imageNameLockedL");
//
//
//            $this->lAndMAndSSizesGeneration($entity, $imageNameLockedL, $imageNameLockedM, $imageNameLockedS, $file);
//
//            $this->deleteFiles($imageNameLockedL, $entity);
//            $this->deleteFiles($imageNameLockedM, $entity);
//            $this->deleteFiles($imageNameLockedS, $entity);
//        }
//
//        $this->deleteFiles($imageNamePNGL, $entity);
//        $this->deleteFiles($imageNameL, $entity);
//        $this->deleteFiles($imageNameM, $entity);
//
//    }
//
    public function updateNameCleaner($request)
    {
        $file = $request->image[0];
        if (config('values.debugBar')) {
            $re = '/.+?(?=<)/m';
            $str = $file;
            preg_match_all($re, $str, $matches, PREG_SET_ORDER);
            $file = $matches[0][0];
            $file = str_replace('.png', '', $file);
        } else {
            $file = str_replace('.png', '', $file);
        }
        return $file;
    }
//
    public function apiMediaModel($url, $timeStamps, $type)
    {
        $res = null;
        if (!$url == null)
            $res = [
                'url' => $url,
                'type' => $type,
                'version' => $timeStamps
            ];
        return $res;
    }
}
