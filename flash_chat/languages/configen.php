<?php
if (!isset($_SESSION)) session_start();
require '../chat/admin.inc.php';
//require '../chat/fonction.inc.php';
error_reporting(7);

/*   (Develooping flash Chat version 1.2)  */
/*   ____________________________________  */

/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*                    SETTINGS                       */
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Chat settings    */
/*   _________________*/

$url=$adresse_http."/flash_chat/"; //absolute url to scripts directory

$text_order = "down"; //use "down" o "up" to show messages downwars or upwards

$review_text_order = "down"; //the same with review messages windos

$delete_empty_room = "no"; //use "yes" to delete messagees when the room is empty

$show_without_time = "no"; //"no" shows always hour, "yes" shows hour only whe the user enters or leaves the room

$password_system = "ip"; //"ip" o "password" to use ip or password to identify users

/*   NOTE:   the banning system only works with "ip" "ip" */
/*           Use "password" only when users come from the same ip*/


/*   Administration variables  */
/*   _______________________   */

$admin_name = "admin"; //user name for admin (max. 12 characters)

$admin_password = "admin"; // password for admin (max. 12 characters)


/*   Chat numeric variables    */
/*   _______________________   */

$correct_time = 2850;//difference in seconds with time in server

$chat_lenght = 15;//number of messages displayed in chat room

$review_lenght = 500;//number of messages displayed in review messages

$total_lenght = 1000;//number of messages stored in chat file


/*   Words to filter   */
/*   _________________  */

$words_to_filter = array("fucking", "fuck", "shit", "cunt", "piss");//list of bad words to replace (add more if you want)

$replace_by = "*@#!";//expression to replace bad words


/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*                    TRANSLATION                    */
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Expressions to translate in intro page  */
/*   ______________________________________  */

$intro_alert="Please:"; //

$alert_message_1="-The name length must be at least 4 characters"; //

$alert_message_2="-The password length must be at least 4 characters"; //

$alert_message_3="-Don't use special characters or numbers in the name"; //

$alert_message_4="-Sorry, we need to get your IP to allow the access"; //

$alert_message_5="-Don't use special characters in the password"; //

$person_word="person"; //

$plural_particle="s"; //

$now_in_the_chat= " now in the chat room";//

$require_sentence = "This chat requires Flash 4"; //

$name_word = "Name"; //

$password_word = "Password"; //

$enter_button = "Enter" ; //

$enter_sentence_1 = "To enter in the chat room, input your name ";//

$enter_sentence_2 = "and password";//

$enter_sentence_3 = " and click the button";//


/*   Expressions to translate in chat room   */
/*   ______________________________________  */

$private_message_expression = "\(for (.*)\)"; //Regular expression for beginning of private message

$before_name="(for "; // if you change the regular expression write here the text between "\ and (.*)

$after_name=")"; // if you change the regular expression write here the text after (.*)

$not_here_string = "-The receiver is not in the room-"; //receiver of private message is not in the room

$bye_string = "Bye. We hope see you soon,";//message showed to dimissed user

$enter_string = "(just entered in the room)";//message showed when a new user enters.

$bye_user = "(just left the room)";//message showed when a user exits.

$kicked_user = "---You have been kicked from this room---";//message showed in the chat room to kicked user.

$bye_kicked_user = "The next time, try to be polite";//bye for kicked users

$bye_banned_user = "Bye, your entrance to this room have been prohibited";//bye for banned users

$banned_user = "Sorry, you can not enter in this room";//message for banned user trying to enter


/*   Expressions to translate in chat interface   */
/*   ___________________________________________  */

$intro_text="Before entering, read the following indications.
";

$intro_text .="If there is somebody in the room with the name that you have chosen, a numerical extension will be added to your name. ";

$intro_text .="If you enter with the name of Carlos and there is someone called Carlos in the room, your name will become Carlos1.
";

$intro_text .="You can see the connected users and send private messages to them ('human' icon), activate and deactivate the sound ('speaker' icon) and review the conversation ('back arrow' icon).
";

$intro_text .="That's all. Enjoy the chat.";//

$conn="
Connecting with the chat room. Please, wait a moment..."; //

$you_are="you are"; //

$connected_users= "Connected users";//

$private_message_to= "private message to";//

$private_message_text="The private messages only can be seen by the sender and the receiver.
";//

$private_message_text.="Write the exact name of the receiver or he will not be able to see the message.
";//

$private_message_text.="Remember that you can copy a selected text and paste it in any other field using the right button in Windows or ctrl-click in Mac.";//


/*   Expressions to translate in review messages page  */
/*   ________________________________________________  */

$review_title ="Chat. Last Messages";// title for review messages page


/*   Expressions to translate in administration pages   */
/*   ________________________________________________  */

$link_to_admin ="Administration";// text for link to administration pages

$intro_admin_title = "Develooping Chat Admin";// title for administration intro page

$intro_admin_name = "Name";//Text for name field in administration intro page

$intro_admin_password = "Password";//Text for name field in administration intro page

$intro_admin_button= "Ok";//Text for button in administration intro page

$no_users = "There aren't users in the room";//no users in the room

$text_for_kick_button = "Kick";//text fof kick button

$text_for_bann_button = "Bann";//text for button for bannig ips

$no_ips = "There aren't banned IPs in the room";//no banned IPs in the room

$text_for_pardon_button = "Pardon";//text for button to pardon ips

$ip_link = "Administration for banned IPs";//text for link to banned IPs

$no_ip_link = "The chat is not using the IP to identify users, so they cannot be banned";//text if you use password instead de IP

$users_link = "Administration for user list";//text for link to connected users

?>
