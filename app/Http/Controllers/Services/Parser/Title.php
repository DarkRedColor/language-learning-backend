<?php

namespace App\Http\Controllers\Services\Parser;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Text;
use App\Models\TheoryPage;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class Title extends Controller
{
    public function theoryTitleDataGet($id, $lcid)
    {
        $textFunctions = new Text();
        $theoryPage = TheoryPage::find($id);
        $html = null;
        if (isset($theoryPage)) {
            $html = json_decode($textFunctions->onlyLocalisationGet($lcid, 'text', $theoryPage));
        }

        $titleMessage = [];
        foreach ($html as $item) {
            $crawler = new Crawler($item);
            $message = $crawler->filterXPath('//body/p')->text();
            $titleMessage [] = $message;
        }

        $value = implode('\n', $titleMessage);

        return $value;
    }
}
