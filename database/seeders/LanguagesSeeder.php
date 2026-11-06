<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                "display_name" => 'Arabic - Saudi Arabia',
                "lcid" => 'ar-SA',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'German - Germany',
                "lcid" => 'de-DE',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Spain',
                "lcid" => 'es-ES',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'French - France ',
                "lcid" => 'fr-FR',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Italian - Italy',
                "name" => 'Italy',
                "lcid" => 'it-IT',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Japanese',
                "lcid" => 'ja-JP',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Korean',
                "lcid" => 'ko-KR',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Dutch - Netherlands',
                "lcid" => 'nl-NL',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Portuguese - Brazil',
                "lcid" => 'pt-BR',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Russian',
                "lcid" => 'ru-RU',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Thai',
                "lcid" => 'th-TH',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Chinese - Mainland China',
                "lcid" => 'zh-CN',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Mexico',
                "lcid" => 'es-MX',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Portuguese - Portugal',
                "lcid" => 'pt-PT',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Chinese - Hong Kong',
                "lcid" => 'zh-HK',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'French - Canada ',
                "lcid" => 'fr-CA',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Chinese - Singapore',
                "lcid" => 'zh-SG',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Guatemala',
                "lcid" => 'es-GT',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Costa Rica',
                "lcid" => 'es-CR',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Panama',
                "lcid" => 'es-PA',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Dominican',
                "lcid" => 'es-DO',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Venezuela',
                "lcid" => 'es-VE',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Colombia',
                "lcid" => 'es-CO',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Peru',
                "lcid" => 'es-PE',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - Argentina',
                "lcid" => 'es-AR',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Arabic - U.A.E.',
                "lcid" => 'ar-AE',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Spanish - United States',
                "lcid" => 'es-US',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
            [
                "display_name" => 'Chinese - Taiwan',
                "lcid" => 'zh-TW',
                "iso_639_1" => "",
                "iso_639_2" => "",
            ],
        ];

        foreach ($data as $item){
            Language::create($item);
        }
    }
}
