<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$this->setFrameMode(true);

$isElement = false;
if($arResult['FOLDER'] != '/catalog/' && isset($arResult['VARIABLES']['SECTION_CODE'])) {
    $path = $arResult['FOLDER'];
    $expPath = explode('/', $path);
    array_pop($expPath);
    $elementCode = array_pop($expPath);
    $sectionCode = array_pop($expPath);
    $color = $arResult['VARIABLES']['SECTION_CODE'];
    if(CIBlockElement::GetList([],['IBLOCK_ID' => $arParams['IBLOCK_ID'], '=CODE' => $elementCode], false, false, ['ID'])->Fetch()) {
        $isElement = true;
        $arResult['FOLDER'] = '/catalog/';
        $arResult['VARIABLES']['SECTION_CODE'] = $sectionCode;
        $arResult['VARIABLES']['ELEMENT_CODE'] = $elementCode;
        include_once 'element.php';
    }
}

if(!$isElement) {
    if (!isset($arParams['FILTER_VIEW_MODE']) || (string)$arParams['FILTER_VIEW_MODE'] == '') {
        $arParams['FILTER_VIEW_MODE'] = 'VERTICAL';
    }
    $arParams['USE_FILTER'] = (isset($arParams['USE_FILTER']) && $arParams['USE_FILTER'] == 'Y' ? 'Y' : 'N');

    $isVerticalFilter = ('Y' == $arParams['USE_FILTER'] && $arParams["FILTER_VIEW_MODE"] == "VERTICAL");
    $isSidebar = ($arParams["SIDEBAR_SECTION_SHOW"] == "Y" && isset($arParams["SIDEBAR_PATH"]) && !empty($arParams["SIDEBAR_PATH"]));
    $isSidebarLeft = isset($arParams['SIDEBAR_SECTION_POSITION']) && $arParams['SIDEBAR_SECTION_POSITION'] === 'left';
    $isFilter = ($arParams['USE_FILTER'] == 'Y');

    if ($isFilter) {
        $arFilter = [
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ACTIVE" => "Y",
            "GLOBAL_ACTIVE" => "Y",
        ];
        if (0 < intval($arResult["VARIABLES"]["SECTION_ID"])) {
            $arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
        } elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"]) {
            $arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
        }

        $obCache = new CPHPCache();
        if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog")) {
            $arCurSection = $obCache->GetVars();
        } elseif ($obCache->StartDataCache()) {
            $arCurSection = [];
            if (Loader::includeModule("iblock")) {
                $dbRes = CIBlockSection::GetList([], $arFilter, false, ["ID"]);

                if (defined("BX_COMP_MANAGED_CACHE")) {
                    global $CACHE_MANAGER;
                    $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                    if ($arCurSection = $dbRes->Fetch()) {
                        $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
                    }

                    $CACHE_MANAGER->EndTagCache();
                } else {
                    if (!$arCurSection = $dbRes->Fetch()) {
                        $arCurSection = [];
                    }
                }
            }
            $obCache->EndDataCache($arCurSection);
        }
        if (!isset($arCurSection)) {
            $arCurSection = [];
        }
    }

    if ($isVerticalFilter) {
        include($_SERVER["DOCUMENT_ROOT"] . "/" . $this->GetFolder() . "/section_vertical.php");
    } else {
        include($_SERVER["DOCUMENT_ROOT"] . "/" . $this->GetFolder() . "/section_horizontal.php");
    }
}