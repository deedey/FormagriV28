<?php
if (!isset($_SESSION)) session_start();
require "admin.inc.php";
error_reporting(7);

$url=$adresse_http."/flash_chat/"; //absolute url to scripts directory

$text_order = "down"; //use "down" o "up" to show messages downwars or upwards

$review_text_order = "down"; //the same with review messages windos

$delete_empty_room = "no"; //use "yes" to delete messagees when the room is empty

$show_without_time = "no"; //"no" shows always hour, "yes" shows hour only whe the user enters or leaves the room

$password_system = "password"; //"ip" o "password" to use ip or password to identify users

/*   NOTE:   the banning system only works with "ip" "ip" */
/*           Use "password" only when users come from the same ip*/


/*   Administration variables  */
/*   _______________________   */

$admin_name = "dey"; //user name for admin (max. 12 characters)

$admin_password = "safia"; // password for admin (max. 12 characters)


/*   Chat numeric variables    */
/*   _______________________   */

$correct_time = 0;//difference in seconds with time in server

$chat_lenght = 15;//number of messages displayed in chat room

$review_lenght = 500;//number of messages displayed in review messages

$total_lenght = 1000;//number of messages stored in chat file


/*   Words to filter   */
/*   _________________  */

$words_to_filter = array("merde", "&nbsp;cul&nbsp;", "&nbsp;con&nbsp;", "&nbsp;pd&nbsp;", "&nbsp;pede&nbsp;", "&nbsp;pédé&nbsp;");//list of bad words to replace (add more if you want)

$replace_by = "(Mot grossier)";//expression to replace bad words


/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*--------------------------------TRANSLATION à partir d'ici---------------------------------------------------------*/
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Expressions to translate in intro page  */
/*   ______________________________________  */

$intro_alert="S'il vous plaît:"; //

$alert_message_1="-Votre login doit être égal ou supérieur à 4 caractères"; //

$alert_message_2="-Votre mot de passe doit être égal ou supérieur à 4 caractères"; //

$alert_message_3="-Ne pas utiliser un caractère spécial ou un chiffre pour le login"; //

$alert_message_4="-Désolé, mais on doit accéder à votre adresse IP pour vous loguer"; //

$alert_message_5="-Ne pas utiliser un caractère spécial pour le mot de passe"; //

$person_word="personne"; //

$plural_particle="s"; //

$now_in_the_chat= " maintenant dans le salon de discussion";//

$require_sentence = "Ce Chat requiert Flash 4 au moins"; //

$name_word = "Nom"; //

$password_word = "Mot de passe"; //

$enter_button = "Entrée" ; //

$enter_sentence_1 = "Pour entrer dans le salon, saisissez votre nom  ";//

$enter_sentence_2 = "et Mot de passe";//

$enter_sentence_3 = " Puis cliquez sur le bouton";//


/*   Expressions to translate in chat room   */
/*   ______________________________________  */

$private_message_expression = "\(à (.*)\)"; //Regular expression for beginning of private message

$before_name="(à "; // if you change the regular expression write here the text between "\ and (.*)

$after_name=")"; // if you change the regular expression write here the text after (.*)

$not_here_string = "-Votre interlocuteur n'est pas dans le salon-"; //receiver of private message is not in the room

$bye_string = "A bientôt,";//message showed to dimissed user

$enter_string = "nous rejoint";//message showed when a new user enters.

$bye_user = "nous quitte";//message showed when a user exits.

$kicked_user = "---Vous avez été déconnecté par l'administrateur---";//message showed in the chat room to kicked user.

$bye_kicked_user = "La prochaine fois, respectez les autres";//bye for kicked users

$bye_banned_user = "Adieu, vous avez été banni par l'administrateur";//bye for banned users

$banned_user = "Désolé, mais vous ne pouvez entrer dans ce salon";//message for banned user trying to enter


/*   Expressions to translate in chat interface   */
/*   ___________________________________________  */

$intro_text="Avant d'entrer, lisez les instructions suivantes:\n";

$intro_text .=" Si quelqu'un portant votre nom est déjà dans ce salon, le système vous affectera votre nom avec un numéro.\n";

$intro_text .=" Si vous vous loguez en tant que François et que quelqu'un nommé François est dans le chat, le système vous nommera François1.\n";

$intro_text .=" Vous pouvez voir les autres utilisateurs connectés et envoyer un message à l'un d'entre eux en particulier (' icône: personnage'), activer ou  désactiver le son ('icône: parleur') et revoir la conversation conversation ('icône: retour).\n";

$intro_text .="Fin des instructions.";//

$conn="Connection avec le Chat en cours, veuillez patienter..."; //

$you_are="Vous êtes"; //

$connected_users= "Utilisateurs connectés";//

$private_message_to= "message privé pour";//

$private_message_text="Les messages privés ne peuvent être lus que par leur destinataire.\n";//

$private_message_text.=" Ecrivez le pseudo exact du destinataire sinon le message ne sera lu par personne.\n";//

$private_message_text.=" Souvenez-vous que vous pouvez copier un message pour le coller dans une autre application.";//


/*   Expressions to translate in review messages page  */
/*   ________________________________________________  */

$review_title ="Chat. Dermiers Messages";// title for review messages page


/*   Expressions to translate in administration pages   */
/*   ________________________________________________  */

$link_to_admin ="Administration";// text for link to administration pages

$intro_admin_title = "Chat Form@grI";// title for administration intro page

$intro_admin_name = "Nom";//Text for name field in administration intro page

$intro_admin_password = "Mot de passe";//Text for name field in administration intro page

$intro_admin_button= "Ok";//Text for button in administration intro page

$no_users = "Il n'y a aucun utilisateur dans le salon";//no users in the room

$text_for_kick_button = "Deconnecter";//text fof kick button

$text_for_bann_button = "Bannir";//text for button for bannig ips

$no_ips = "Il n'y a aucun banni dans le Chat";//no banned IPs in the room

$text_for_pardon_button = "Pardon";//text for button to pardon ips

$ip_link = "Administration des IPs bannies";//text for link to banned IPs

$no_ip_link = "Le Chat n'utilise pas l'adresse IP en tant qu'identificateur d'un utilisateur et ne peut donc pas bannir";//text if you use password instead de IP

$users_link = "Administration de liste des utilisateurs";//text for link to connected users

?>
