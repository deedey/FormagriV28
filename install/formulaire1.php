<FORM id="monform" class="input" name="monform" action="" method="POST">
  <TABLE cellspacing="0" cellspadding="0" align="left" valign="MIDDLE" border=0 width=98%>
  <TR><TD style="font-size: 11px;font-family: arial, tahoma, verdana;border:1px solid #D45211;background-color: #F4F4F4;">
  <TABLE cellspacing="2" cellspadding="1" align="MIDDLE" valign="MIDDLE" border=0 width=100%>
   <TR>
      <INPUT TYPE="HIDDEN"  class="input" name="envoi" value="1">
      <INPUT TYPE="HIDDEN"  class="input" name="passe_ftp" value="">
      <INPUT TYPE="HIDDEN"  class="input" name="mon_hote" value="<?php echo $mon_localhost;?>">
      <INPUT TYPE="HIDDEN"  class="input" name="repertoire" value="<?php echo $mon_repertoire;?>">
      <TD nowrap><small>Nom de domaine ou Adresse IP  *</small></TD>
      <TD nowrap > <span style="width:60px;heigh:20px;margin-top:2px;padding:3px;font-size: 11px;font-family: arial, tahoma, verdana;border:1px solid #24677A;background-color: #FFFFFF;heigh:20px;"><?php echo $mon_localhost;?></span></TD>
      <TD ><small>Cette configuration est la vôtre actuellement.
   </TR>
   <TR>
      <TD nowrap><small>Répertoire dans lequel se trouve votre page de lancement  *</small></TD>
      <TD nowrap > <span style="width:60px;heigh:20px;padding:3px;font-size: 11px;font-family: arial, tahoma, verdana;border:1px solid #24677A;background-color: #FFFFFF;heigh:20px;"><?php echo $mon_repertoire;?></span></TD>
      <TD ><small> Ceci est le repertoire actuel de votre plateforme </small></TD>
   </TR>
   <?php
   /*
   <TR>
      <TD nowrap><small>Mot de passe FTP  *</small></TD>
      <TD nowrap><INPUT TYPE="PASSWORD" class="input" name="passe_ftp"  id="passe_ftp" value="" align="middle"></TD>
      <TD ><small>Servira pour uploader en mode FTP <small></TD>
   </TR>
   */
   ?>
   <TR>
      <TD nowrap><small>Adresse de la base de données MYSQL  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="adresse_base"  id="adresse_base" value="localhost" size="40" align="middle"></TD>
      <TD ><small>Localhost en général sauf si serveur différent<small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Nom de la base de données MYSQL  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="nom_base"  id="nom_base" value="formagri" align="middle"></TD>
      <TD ><small>Intitulé de la base de données qui sera créée<small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Login pour accéder à la base de données MYSQL  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="login_base"  id="login_base" value="root" align="middle"></TD>
      <TD><small>Votre login pour y accéder ("root" est prédéfini avec EasyPhp ou Wamp)<small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Mot de passe pour accéder à la base de données  </small></TD>
      <TD nowrap><INPUT TYPE="PASSWORD"  class="input" name="passe_base"   id="passe_base" value="" align="middle"></TD>
      <TD ><small>Vide avec EasyPhp ou Wamp en localhost</small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Login en tant qu'utilisateur  *</small></TD>
      <TD nowrap style="width=60px;font-size: 11px;font-family: arial, tahoma, verdana;border:1px solid #24677A;background-color: #FFFFFF;"> super</TD>
      <TD><small> Pour utiliser votre profil Administrateur</small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Mot de passe utilisateur  *</small></TD>
      <TD nowrap style="width=60px;font-size: 11px;font-family: arial, tahoma, verdana;border:1px solid #24677A;background-color: #FFFFFF;"> super</TD>
      <TD><small>Votre mot de passe Administrateur</small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Mot de passe pour l'administration des forums  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="passe_forum"   id="passe_forum" value="" align="middle"></TD>
      <TD><small>Afin de gérér en dur les différents forums</small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Adresse email pour l'administration des forums  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT" class="input" name="mon_email"  id="mon_email" value="moi@chez_untel.fr" size="40" align="middle"></TD>
      <TD><small></small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Adresse email pour la modération des forums  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="email_mod"  id="email_mod" value="moi@chez_untel.fr" size="40" align="middle"></TD>
      <TD><small>Pour approuver les messages à afficher</small></TD>
   </TR>
   <TR>
      <TD nowrap><small>Mot de passe pour la modération des forums  *</small></TD>
      <TD nowrap><INPUT TYPE="TEXT"  class="input" name="passe_mod"  id="passe_mod" value="" align="middle"></TD>
      <TD><small>Le mot de passe pour accéder à la modération</small></TD>
   </TR>
   <TR>
      <TD></TD><TD align="left" colspan = "2">&nbsp;<BR>
        <input type='SUBMIT' name= 'submit' value="Envoyer" />
      </TD>
   </TR>
   <TR>
      <TD colspan = "2"
      style="font-size:12px;padding:4px;font-family: arial, tahoma, verdana;font-weight:bold;color:#D45211; border:2px dotted #D45211;background-color: #FFFFFF;">
      <?php echo "<small>$mess_insc_symb<BR>Les valeurs qui sont données sont les valeurs
      pour une configuration en localhost sous Easyphp ou encore Wamp et autres..<br />
      Vous pourrez par ailleurs modifier votre mot de passe plus tard</small>";?></TD><TD></TD>
   </TR>
  </TABLE></TD></TR></TABLE>
 </FORM>