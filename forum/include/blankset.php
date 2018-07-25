<?php

// Blank out potentially dangerous per-forum values

$aryUnset[] = 'ForumId';
$aryUnset[] = 'ForumActive';
$aryUnset[] = 'ForumName';
$aryUnset[] = 'ForumDescription';
$aryUnset[] = 'ForumConfigSuffix';
$aryUnset[] = 'ForumFolder';
$aryUnset[] = 'ForumParent';
$aryUnset[] = 'ForumLang';
$aryUnset[] = 'ForumDisplay';
$aryUnset[] = 'ForumTableName';
$aryUnset[] = 'ForumModeration';
$aryUnset[] = 'ForumModEmail';
$aryUnset[] = 'ForumModPass';
$aryUnset[] = 'ForumEmailList';
$aryUnset[] = 'ForumEmailReturnList';
$aryUnset[] = 'ForumEmailTag';
$aryUnset[] = 'ForumCheckDup';
$aryUnset[] = 'ForumMultiLevel';
$aryUnset[] = 'ForumCollapse';
$aryUnset[] = 'ForumFlat';
$aryUnset[] = 'ForumStaffHost';
$aryUnset[] = 'ForumAllowHTML';
$aryUnset[] = 'ForumAllowUploads';
$aryUnset[] = 'ForumTableBodyColor2';
$aryUnset[] = 'ForumTableBodyFontColor2';
$aryUnset[] = 'ForumTableWidth';
$aryUnset[] = 'ForumNavColor';
$aryUnset[] = 'ForumNavFontColor';
$aryUnset[] = 'ForumTableHeaderColor';
$aryUnset[] = 'ForumTableHeaderFontColor';
$aryUnset[] = 'ForumTableBodyColor1';
$aryUnset[] = 'ForumTableBodyFontColor1';

reset($aryUnset);
while (list($key, $value) = each($aryUnset)) {
  if(isset($$value)) {
    unset($$value);
  }
}

initvar("ForumConfigSuffix");
initvar("ForumLang");
initvar("ForumModEmail");
initvar("ForumName");

?>