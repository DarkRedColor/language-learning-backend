<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CrudText extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($request, $model)
    {
        $request->validate([
            'name_en' => 'required',
        ]);

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }


        $key = $request->name_en;
        $key = Str::replace(' ', '_', $key);
        if (strlen($key) > 30) {
            $key = substr($key, 0, 30);
        }

        $texts = $model->get('key');

        $keyData['key'] = (new Text())->unicalCheck($texts, $key, 1, 2,'key');

        $model->create($keyData);

        $text = $model->orderBy('created_at', 'desc')->first();

        $id = $text->id;

        return $id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return int
     */
    public function update(Request $request, $id, $model)
    {
        $request->validate([
            'name_en' => 'required',
        ]);

        [$keys, $values] = Arr::divide($request->all());
        $keyData = [];
        for ($i = 1; $i < count($keys); $i++) {
            $keyData [$keys[$i]] = $values[$i];
        }

        $model->find($id)
            ->update($keyData);
        return $id;
    }

}
