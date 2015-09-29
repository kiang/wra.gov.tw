<?php

/*
 * rar files downloaded from http://www.dprc.ncku.edu.tw/download/index2.html
 * 
 * ref: http://fhy.wra.gov.tw/PUB_WEB_2011/Page/Frame_MenuLeft.aspx?sid=27
 */
$tmpPath = __DIR__ . '/tmp';
$target = __DIR__ . '/geojson';

foreach (glob(__DIR__ . '/rar/*.rar') AS $rarCity) {
    $path = pathinfo($rarCity);
    $city = substr($path['filename'], strpos($path['filename'], '_') + 1);
    $cityTmp = $tmpPath . '/' . $city;
    if (!file_exists($cityTmp)) {
        mkdir($cityTmp, 0777, true);
    }
    $cityTarget = $target . '/' . $city;
    if (!file_exists($cityTarget)) {
        mkdir($cityTarget, 0777, true);
    }
    //system("/usr/bin/unrar e -y {$rarCity} {$cityTmp}");
    foreach (glob($cityTmp . '/*') AS $subFile) {
        $subPath = pathinfo($subFile);
        switch ($subPath['extension']) {
            case 'rar':
                //system("/usr/bin/unrar e -y {$subFile} {$cityTmp}");
                break;
            case 'mxd':
            case 'shp':
                if (!file_exists("{$cityTarget}/{$subPath['filename']}.json")) {
                    system("mapshaper  -i encoding=big5 {$subFile} -o format=geojson {$cityTmp}/{$subPath['filename']}.json");
                    system("ogr2ogr -t_srs EPSG:4326 -s_srs EPSG:3826 -f 'GeoJSON' -lco ENCODING=UTF-8 {$cityTarget}/{$subPath['filename']}.json {$cityTmp}/{$subPath['filename']}.json");
                }
                break;
        }
    }
}