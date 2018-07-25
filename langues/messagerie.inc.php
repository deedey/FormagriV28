<?php
if ($_SESSION['lg'] == 'fr')
{
   $msg_mess_lu = "Message considéré comme lu";
   $msg_mess_nonlu = "Message considéré comme non lu";
   $msg_mess_rest = "Restauration : message remis dan la boîte de réception";
}
elseif ($_SESSION['lg'] == 'en')
{
   $msg_mess_lu = "Message considéré comme lu";
   $msg_mess_nonlu = "Message considéré comme non lu";
   $msg_mess_rest = "Restauration : message remis dan la boîte de réception";
}
elseif ($_SESSION['lg'] == 'ru')
{
   $msg_mess_nonlu = "Message considéré comme non lu";
   $msg_mess_lu = "Message considéré comme lu";
   $msg_mess_rest = "Restauration : message remis dan la boîte de réception";
}
?>
