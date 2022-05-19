<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(!check_bitrix_sessid()) return;

echo CAdminMessage::ShowNote(Loc::getMessage("O2_TITLE_UNSTEP_BEFORE")." ".Loc::getMessage("O2_TITLE_AFTER"));
?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>">
	<input type="hidden" name="lang" value="<? echo LANGUAGE_ID; ?>" />
	<input type="submit" value="<? echo(Loc::getMessage("MOD_BACK")); ?>">
</form>