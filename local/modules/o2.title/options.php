<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);


$aTabs = [
    [
        "DIV" => "edit",
        "TAB" => Loc::getMessage("O2_TITLE_OPTIONS_TAB_NAME"),
        "TITLE" => Loc::getMessage("O2_TITLE_OPTIONS_TAB_NAME"),
        "OPTIONS" => [
            [
                "h1_template",
                Loc::getMessage("O2_TITLE_OPTIONS_NAME_H1"),
                Loc::getMessage("O2_TITLE_OPTIONS_TEMPLATE_H1"),
                ["text", 100],
            ],
            [
                "title_template",
                Loc::getMessage("O2_TITLE_OPTIONS_NAME_TITLE"),
                Loc::getMessage("O2_TITLE_OPTIONS_TEMPLATE_TITLE"),
                ["text", 100],
            ],
        ],
    ],
];

if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {

        foreach ($aTab["OPTIONS"] as $arOption) {

            if (!is_array($arOption)) {

                continue;
            }

            if ($arOption["note"]) {

                continue;
            }

            if ($request["apply"]) {
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0],
                    is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            } elseif ($request["default"]) {
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }
    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . $module_id . "&lang=" . LANG);
}

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

$tabControl->Begin(); ?>
    <form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>"
          method="post">

        <?
        foreach ($aTabs as $aTab) {

            if ($aTab["OPTIONS"]) {

                $tabControl->BeginNextTab();

                __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
            }
        }

        $tabControl->Buttons();
        ?>

        <input type="submit" name="apply" value="<? echo(Loc::GetMessage("O2_TITLE_OPTIONS_INPUT_APPLY")); ?>"
               class="adm-btn-save"/>
        <input type="submit" name="default" value="<? echo(Loc::GetMessage("O2_TITLE_OPTIONS_INPUT_DEFAULT")); ?>"/>

        <?
        echo(bitrix_sessid_post());
        ?>

    </form>
<? $tabControl->End();
