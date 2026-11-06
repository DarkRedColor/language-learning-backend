<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Language;
use App\Models\SubChapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ApiRequest extends Controller
{
    public function getSftpContent($name, $pathToFile, $disk)
    {
        if (Storage::disk($disk)->exists("$name")) {
            return Storage::disk($disk)->url("$name");
        } elseif (Storage::disk('gcs')->exists("$pathToFile/$name")) {
            Storage::disk($disk)
                ->put($name, Storage::disk('gcs')
                    ->get("$pathToFile/$name"));
            return Storage::disk("gcs")->url("$pathToFile/$name");
        } else
            return response('something is wrong', 400);
    }

    public function postSftpContent($file, $name, $pathToFile)
    {
        Storage::disk('gcs')->putFileAs("/", $file, "$pathToFile/$name");

        return 'done';

    }

    public function imagesRequestDataFill($id, $request, $entity,$model,$locked = true)
    {
//        $data = [
//            'file' => $image,
//            'id' => $id,
//            'host' => config('values.host'),
//            'format' => (new FormatChecker)->formatCheck($image),
//        ];
        $imageFunctions = new Images();

        if ($request->hasFile($model)) {
            $file = $request->file($model);
            $imageFunctions->pngConvertAdventures($id, $entity,  $file,$locked);

        }


        return 'done';
    }

    public function languageCreateOrUpdate($request, $iso, $id, $name)
    {
        if ($iso) {
            $data = [
                'display_name' => $request->display_name,
                'lcid' => $request->lcid,
                'is_displayed_to_user' => 0,
                'iso_639_1' => $request->iso_639_1,
                'iso_639_2' => $request->iso_639_2,
            ];
            Language::create($data);
        } else {
            if (\Str::contains($request->decodedPath(), 'main/menu')) {
                if ($name === 'none') {
                    if ($request->is_deleted == 1) {
                        $data = [
                            'name' => $request->name,
                            'learn' => $request->learn,
                            'my_language' => $request->my_language,
                            'flag' => null
                        ];
                    } else {
                        $data = [
                            'name' => $request->name,
                            'learn' => $request->learn,
                            'my_language' => $request->my_language,
                        ];
                    }
                } else {
                    $data = [
                        'name' => $request->name,
                        'learn' => $request->learn,
                        'my_language' => $request->my_language,
                        'flag' => $name
                    ];
                }

            } else {
                $data = [
                    'display_name' => $request->display_name,
                    'iso_639_1' => $request->iso_639_1,
                    'iso_639_2' => $request->iso_639_2,
                ];
            }

            Language::where('id', $id)->update($data);
        }
    }

    public function deleteMediaSftp($entity, $name)
    {
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));
        $array = [
            "$host/$entity/$name" . "_S.webp",
            "$host/$entity/$name" . "_M.webp",
            "$host/$entity/$name" . "_L.webp",

        ];
        foreach ($array as $item) {
            if (\Storage::disk('sftp')->exists($item)) {
                \Storage::disk('sftp')->delete($item);
            }
        }

    }

    public function deleteEntityMedia($entity, $id)
    {
        $hostCheck = new HostChecker();
        $host = $hostCheck->hostCheck(config('values.host'));
        Http::post(config('values.api_url') . $entity . $id . '/' . $host);
    }

    public function httpPostRequestToApiStorage($id, $request, $url, $model, $locked = true, $additional = false, $episode = false, $files = false, $name = false)
    {
        $apiRequest = new ApiRequest();
        if ($additional) {
            $apiRequest->imagesRequestDataFill($id, $request, $url, "additional-image", $locked);
        } else {
            if ($name) {
                $apiRequest->imagesRequestDataFill($name, $request, $url, 'image', $locked);
            } else {
                $apiRequest->imagesRequestDataFill($id, $request, $url, 'image', $locked);
            }
        }


            if ($locked) {
                if ($episode) {
                    if ($additional) {
                        $model->where('id', $id)->update([
                            'additional_image_locked' => config('values.api_url') . $url  . '/' . $id . '_locked',
                            'additional_image_unlocked' => config('values.api_url') . $url  . '/' . $id,
                        ]);
                    } else {
                        $model->where('id', $id)->update([
                            'image_locked' => config('values.api_url') . $url  . '/' . $id . '_locked',
                            'image_unlocked' => config('values.api_url') . $url  . '/' . $id,
                        ]);
                    }
                } else {
                    $model->where('id', $id)->update([
                        'image_locked' => config('values.api_url') . $url  . '/' . $id . '_locked',
                        'image_unlocked' => config('values.api_url') . $url  . '/' . $id,
                    ]);
                }
            } else {
                if ($additional) {
                    $model->where('id', $id)->update([
                        'theory' => json_encode([config('values.api_url') . $url  . '/' . $id]),
                    ]);
                } else {
                    if ($files) {
                        if ($name) {
                            if ($model->find($id)->url <> null) {
                                $img[] = json_decode($model->find($id)->url)[0];
                                $img[] = config('values.api_url') . $url . '/' . $name;
                                $model->where('id', $id)->update([
                                    'url' => json_encode($img),
                                ]);
                            } else {
                                $model->where('id', $id)->update([
                                    'url' => json_encode([config('values.api_url') . $url . '/' . $name]),
                                ]);
                            }
                        }
                    } else {
                        $model->where('id', $id)->update([
                            'mission_en' => json_encode([config('values.api_url') . $url  . '/' . $id]),
                        ]);
                    }
                }
            }

    }
}
