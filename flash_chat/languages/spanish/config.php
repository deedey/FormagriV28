<?php
error_reporting(7);

/*   (Develooping flash Chat version 1.2)  */
/*   ____________________________________  */

/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*                    AJUSTES                        */
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Ajustes del chat    */
/*   ____________________*/

$url="http://www.yourdomain.com/chat/"; //url absoluta del directorio donde se encuentran los scripts

$text_order = "down"; //usa "down" o "up" para mostrar el texto hacia abajo o hacia arriba respectivamente

$review_text_order = "down"; //igual pero en la ventana de revision de mensajes

$delete_empty_room = "no"; //usa "yes" si deseas que se borren los textos de la sala al quedarse vacia

$show_without_time = "no"; //"no" muestra siempre la hora, "yes" solo la muestra en la entrada y salida del usuario

$password_system = "ip"; //usa "ip" o "password" segœn quieras usar la ip o un password para identificar usuarios

/*   NOTA:   El sistema de banning (inhabilitacion de usuarios) solo funciona con "ip" */
/*           Usa "password" preferiblemente solo en entornos donde haya usuarios conectados a travŽs de una misma ip*/


/*   Variables de Administracion */
/*   ___________________________ */

$admin_name = "admin"; //nombre del administrador (max. 12 caracteres)

$admin_password = "admin"; // clave del administrador (max. 12 caracteres)


/*   Variables numericas del chat    */
/*   _____________________________   */

$correct_time = 0; //diferencia en segundos con el tiempo en el servidor

$chat_lenght = 15; //numero de mensajes mostrados en la sala

$review_lenght = 500; //numero de mensajes mostrados al revisar mensajes

$total_lenght = 1000; //numero de mensajes almacenados por el sistema


/*   Palabras para filtrar   */
/*   ______________________  */

$worlds_to_filter = array("mierda", "joder", "follar", "jodan", "gilipollas", "capullo");//lista de palabrotas para filtar, pon las que quieras

$replace_by = "*@#!";//expresion para reemplazar a las palabrotas


/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/
/*                    TRADUCCION                     */
/*___________________________________________________*/
/*___________________________________________________*/
/*___________________________________________________*/


/*   Fases para traducir en la pagina intro  */
/*   ______________________________________  */

$intro_alert="Por favor:"; //

$alert_message_1="-El nombre debe tener al menos 4 caracteres"; //

$alert_message_2="-La contraseña ha de tener al menos 4 caracteres"; //

$alert_message_3="-No uses acentos, caracteres especiales o números en el nombre"; //

$alert_message_4="-Lo sentimos, pero no podemos obtener tu IP para darte acceso"; //

$alert_message_5="-No uses acentos ni caracteres especiales en el nombre"; //

$person_word="persona"; //

$plural_particle="s"; //
 
$now_in_the_chat= " ahora en la sala de chat";//

$require_sentence = "Este chat requiere Flash 4"; //

$name_word = "Nombre"; //

$password_word = "Contraseña"; //

$enter_button = "Entrar" ; //

$enter_sentence_1 = "Para entrar en la sala de chat, escribe tu nombre ";//

$enter_sentence_2 = "y contraseña";//

$enter_sentence_3 = " y pulsa el botón";//


/*   Frases para traducir en el chat   */
/*   ________________________________  */

$private_message_expression = "\(para (.*)\)"; // Expresion regular para indicar mensaje privado.

$before_name="(para "; // si cambias la expresion regular pon aqui lo que hay entre "\ y (.*)

$after_name=")"; // si cambias la expresion regular pon aqui lo que detras de (.*) 

$not_here_string = "-El destinatario no se encuentra-"; //el receptor del mensaje privado no esta en la sala

$bye_string = "Adiós. Vuelve cuando quieras";//despedida para el usuario

$enter_string = "(acaba de entrar en la sala)";//mensaje que avisa que un usuario entra en la sala.

$bye_user = "(acaba de salir de la sala)";//mensaje que avisa que un usuario abandona la sala.

$kicked_user = "---Has sido expulsado de la sala---";//mensaje mostrado en el chat al usuario expulsado.

$bye_kicked_user = "Si vuelves, procura comportarte";//despedida para el usuario expulsado

$bye_banned_user = "Te ha sido denegada la entrada a esta sala";//despedida para el usuario inhabilitado

$banned_user = "Lo siento, no eres bienvenido en este lugar";//mensaje mostrado en el chat al usuario inhabilitado


/*   frases para traducir en el interface flash   */
/*   ___________________________________________  */

$intro_text="Antes de entrar, lee las siguientes indicaciones.
";

$intro_text .="Si hay alguien en la sala con el nombre que has escogido, te será añadida una extensión numérica. ";

$intro_text .="Si entras con el nombre de 'Carlos' y hay ya alguien llamado 'Carlos' en la sala, tu nombre será 'Carlos1'.
";

$intro_text .="Podrás ver a los usuarios conectados y mandarles mensajes privados (icono 'humano'), activar y desactivar el sonido (icono 'altavoz') y revisar la conversación (icono 'flecha atrás').
";

$intro_text .="Eso es todo. Disfruta de la charla.";//

$conn="
Conectando con la sala de chat. Por favor, espera un momento..."; //

$you_are="tú eres"; //

$connected_users= "Usuarios conectados";//

$private_message_to= "mensaje privado a";//

$private_message_text="Los mensajes privados sólo pueden ser vistos por el emisor y el receptor.
";//
 
$private_message_text.="Escribe el nombre exacto del receptor o no podrá ver el mensaje.
";// 

$private_message_text.="recuerda que puedes copiar un texto seleccionado y pegarlo en otro campo usando el botón derecho en Windows o ctrl-click en Mac.";//


/*   Frases para traducir en la pagina de revision de mensajes  */
/*   _________________________________________________________  */

$review_title ="Chat. Últimos Mensajes";// titulo de la pagina


/*   Frases para traducir en las paginas de administracion   */
/*   ______________________________________________________  */

$link_to_admin ="Administración";// texto para vinculo a las paginas de administracion

$intro_admin_title = "Develooping flash chat. Administración";// titulo de la pagina intro de administracion

$intro_admin_name = "Nombre";// texto para el campo nombre de la pagina intro de administracion

$intro_admin_password = "Contraseña";// texto para el campo password de la pagina intro de administracion

$intro_admin_button= "Ok";//texto para el boton de la pagina intro de administracion

$no_users = "No hay usuarios en la sala";//no hay usuarios en la sala

$text_for_kick_button = "Expulsar";//texto para boton 'kicking'

$text_for_bann_button = "Inhabilitar";//texto para boton 'banning'

$no_ips = "No hay IPs inhabilitadas para entrar en la sala";//no hay IPs inhabilitadas

$text_for_pardon_button = "Perdonar";//texto para boton 'perdonar ip'

$ip_link = "Administración de IPs inhabilitadas";//texto para vinculo a IPs inhabilitadas

$no_ip_link = "El chat no está usando la IP para identificar usuarios, así que éstos no pueden ser inhabilitados";//texto si usas password en lugar de IP

$users_link = "Administración de usuarios";//texto del vinculo a usuarios conectados

?>