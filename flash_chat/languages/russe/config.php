<?php
if (!isset($_SESSION)) session_start();
error_reporting(7);

$url="http://ef-dev.educagri.fr/flash_chat/"; //absolute url to scripts directory

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

$words_to_filter = array("жопа", "&nbsp;педик&nbsp;",);//list of bad words to replace (add more if you want)

$replace_by = "(Грубое выражение)";//expression to replace bad words


/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*--------------------------------TRANSLATION ? partir d'ici---------------------------------------------------------*/
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Expressions to translate in intro page  */
/*   ______________________________________  */

$intro_alert="Пожалуйста :"; //

$alert_message_1="-Ваш логин не должен превышать 4 символа"; //

$alert_message_2="-Ваш пароль не должен превышать 4 символа "; //

$alert_message_3="-Нельзя использовать специальные символы и цифры для логина"; //

$alert_message_4="-К сожалению, мы обязаны получить доступ к Вашему IP адресу, чтобы Вас подсоединить "; //

$alert_message_5="-Нельзя использовать специальные символы для пароля"; //

$person_word="человек(а)"; //

$plural_particle=" "; //

$now_in_the_chat= " в настоящее время в дискуссионном салоне ";//

$require_sentence = "Необходимо иметь по меньшей мере Flash 4 для данного чата"; //

$name_word = "Имя"; //

$password_word = "Пароль"; //

$enter_button = "Вход" ; //

$enter_sentence_1 = "Для входа в салон, введите, пожалуйста, Ваше имя  ";//

$enter_sentence_2 = "и пароль";//

$enter_sentence_3 = " Затем щелкните на кнопку ";//


/*   Expressions to translate in chat room   */
/*   ______________________________________  */

$private_message_expression = "\(кому (.*)\)"; //Regular expression for beginning of private message

$before_name="(? "; // if you change the regular expression write here the text between "\ and (.*)

$after_name=")"; // if you change the regular expression write here the text after (.*)

$not_here_string = "-Ваш собеседник не находится в салоне-"; //receiver of private message is not in the room

$bye_string = "До скорой встречи,";//message showed to dimissed user

$enter_string = "присоединяется к нам";//message showed when a new user enters.

$bye_user = "нас покидает";//message showed when a user exits.

$kicked_user = "---Вы были отсоединены администратором ---";//message showed in the chat room to kicked user.

$bye_kicked_user = "В следующий раз относитесь уважительнее к собеседникам ";//bye for kicked users

$bye_banned_user = "Прощайте, вы были отсоединены администратором";//bye for banned users

$banned_user = "К сожалению, Вы не можете войти в салон";//message for banned user trying to enter


/*   Expressions to translate in chat interface   */
/*   ___________________________________________  */

$intro_text="Перед входом, внимательно прочитайте следующие инструкции :\n";

$intro_text .=" Если кто-то, имеющий Ваше имя, уже находится в салоне, система Вам присвоит имя с номером.\n";

$intro_text .=" Если Вы входите в чат с именем Николай, система Вам присвоит имя Николай1. \n";

$intro_text .=" Вы можете видеть список других подсоединенных пользователей и отправить сообщение одному из них лично ('персонаж'), включить звук ('звук') и вернуться к беседе ('возврат').\n";

$intro_text .="Окончание инстукций.";//

$conn="Подождите пожалуйста, происходит соединение с чатом..."; //

$you_are="Вы "; //

$connected_users= "Подсоединенные пользователи";//

$private_message_to= "частное сообщение для";//

$private_message_text="Частные сообщения могут быть просмотрены лишь адресатами.\n";//

$private_message_text.=" Введите точный псевдоним адресата, иначе сообщение не будет никем прочитано.\n";//

$private_message_text.=" Помните, что Вы можете копировать сообщения для его использования в другом приложении.";//


/*   Expressions to translate in review messages page  */
/*   ________________________________________________  */

$review_title ="Чат. Последние сообщения";// title for review messages page


/*   Expressions to translate in administration pages   */
/*   ________________________________________________  */

$link_to_admin ="Администрирование";// text for link to administration pages

$intro_admin_title = "Чат Form@grI";// title for administration intro page

$intro_admin_name = "Имя";//Text for name field in administration intro page

$intro_admin_password = "Пароль";//Text for name field in administration intro page

$intro_admin_button= "Ok";//Text for button in administration intro page

$no_users = "В салоне нет пользователей";//no users in the room

$text_for_kick_button = "Отсоединиться";//text fof kick button

$text_for_bann_button = "Запретить";//text for button for bannig ips

$no_ips = "В чате нет недопущенных пользователей";//no banned IPs in the room

$text_for_pardon_button = "Извините";//text for button to pardon ips

$ip_link = "Администрирование недопущенных IPs";//text for link to banned IPs

$no_ip_link = "Чат не использует адрес IP в качестве идентификатора пользователя и, таким образом, не может его автоматически удалять ";//text if you use password instead de IP

$users_link = "Администрирование списка пользователей ";//text for link to connected users

?>
