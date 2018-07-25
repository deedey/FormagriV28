<?php
if (!isset($_SESSION)) session_start();
error_reporting(7);
require ("../../admin.inc.php");
 
/*   (Develooping flash Chat version 1.2)  */
/*___________________________________________________*/
/*                    SETTINGS                       */
/*___________________________________________________*/


/*   Chat settings    */
/*   _________________*/

$url= $adresse_http."/flash_chat/"; //absolute url to scripts directory

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

$words_to_filter = array("хуй", "хуе", "бля", "пизд", "охуе", "жопа", "нахуй", "ебан", "ебать", "отъебать", "пидор", "педик",);//list of bad words to replace (add more if you want)

$replace_by = "(Некультурное выражение)";//expression to replace bad words


/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*                    TRANSLATION                    */
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Expressions to translate in intro page  */
/*   ______________________________________  */

$intro_alert="Пожалуйста:"; //

$alert_message_1="-Ваше имя пользователя должен содержать как минимум 4 символа"; //

$alert_message_2="-Ваш пароль должен содержать как минимкм 4 символа"; //

$alert_message_3="-Не использовать специальных символов для имени пользователя"; //

$alert_message_4="-Извините, но для осуществления соединения нам необходимо иметь доступ к IP адресу вашего компьютера"; //

$alert_message_5="-Не использовать специальных символов для пароля"; //

$person_word="пользователя"; //

$plural_particle=""; //

$now_in_the_chat= " уже находятся в секции дискуссий";//

$require_sentence = "Для нормального функционирования чата требуется как минимум Flash 4"; //

$name_word = "Имя"; //

$password_word = "Пароль"; //

$enter_button = "Вход" ; //

$enter_sentence_1 = "Введите ваше имя для входа в секцию дискуссий  ";//

$enter_sentence_2 = "и пароль";//

$enter_sentence_3 = " Затем нажмите на кнопку";//


/*   Expressions to translate in chat room   */
/*   ______________________________________  */

$private_message_expression = "\(кому (.*)\)"; //Regular expression for beginning of private message

$before_name="(? "; // if you change the regular expression write here the text between "\ and (.*)

$after_name=")"; // if you change the regular expression write here the text after (.*)

$not_here_string = "-Ваш собеседник находится вне секции-"; //receiver of private message is not in the room

$bye_string = "До скорой встречи,";//message showed to dimissed user

$enter_string = "(Только что подсоединился к секции)";//message showed when a new user enters.

$bye_user = "(Только что покинул секцию)";//message showed when a user exits.

$kicked_user = "---Вы были отсоединены администратором---";//message showed in the chat room to kicked user.

$bye_kicked_user = "В следующий раз относитесь уважительно к другим пользователям";//bye for kicked users

$bye_banned_user = "Прощайте, ваш доступ был заблокирован администратором";//bye for banned users

$banned_user = "Извините, но у вас нет доступа в эту секцию";//message for banned user trying to enter


/*   Expressions to translate in chat interface   */
/*   ___________________________________________  */

$intro_text="Перед тем как войти прочитайте следующие инструкции:\n";

$intro_text .=" Если кто-то с подобным именем уже находиться в секции, то система добавит к вашему имени номер.\n";

$intro_text .=" Если вы входите под именем Иван и кто-то по имени Иван уже находиться в чате, то система присвоит вам имя Иван1.\n";

$intro_text .=" Вы можете просмотреть список других подсоединенных пользователей и отправить кому-либо из них послание личного характера (' пиктограмма: персонаж'), включить/выключить звук ('пиктограмма: громкоговоритель') и вернуться к общей дискуссии ('пиктограмма: возврат).\n";

$intro_text .="Инструктаж закончен.";//

$conn="Осуществляется подсоединение к чату, подождите пожалуста..."; //

$you_are="Вы"; //

$connected_users= "Подсоединенные пользователи";//

$private_message_to= "личное сообщение для";//

$private_message_text="Личные сообщения могут быть прочитаны только адресатом.\n";//

$private_message_text.=" Введите точный псевдоним адресата, иначе сообщение не будет никем прочитано.\n";//

$private_message_text.=" Вы можете копировать сообщения в другие программные приложения.";//


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

$no_users = "В секции нет пользователей";//no users in the room

$text_for_kick_button = "Отсоединиться";//text fof kick button

$text_for_bann_button = "Запретить";//text for button for bannig ips

$no_ips = "В чате нет заблокированных пользователей";//no banned IPs in the room

$text_for_pardon_button = "Извините";//text for button to pardon ips

$ip_link = "Администрирование заблокированных IP адресов";//text for link to banned IPs

$no_ip_link = "Чат не использует IP адрес в качестве идентификатора пользователя и не может поэтому производить блокировку";//text if you use password instead de IP

$users_link = "Aдминистрирование списка пользователей";//text for link to connected users

?>
