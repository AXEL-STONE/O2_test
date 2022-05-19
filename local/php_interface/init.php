<?php

function x($obj) {
    echo '-=<xmp>';
    print_r($obj);
    echo '</xmp>=-';
}

function xe($obj) {
    echo '-=<xmp>';
    print_r($obj);
    echo '</xmp>=-';
    die();
}

function sefFolderCatalog ($template) {
    $folder = '/catalog/';
    global $APPLICATION;
    $dir = $APPLICATION->GetCurDir();
    $dir = str_replace($folder, '', $dir);
    $expTempl = explode('/', $template);
    $expDir = explode('/', $dir);

    if(count($expDir) > count($expTempl)) { // появился цвет
        array_pop($expDir);
        array_pop($expDir);
        $folder = str_replace('//','/','/catalog/'.implode('/',$expDir).'/');
    }

    return $folder;
}
