  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.sujet)==true)
        ErrMsg += ' - <?php echo $mess_mail_sujet;?>\n';
      if (isEmpty(frm.contenu)==true)
        ErrMsg += ' - <?php echo addslashes($mess_alert);?>\n';

      if (ErrMsg.length > lenInit)
       alert(ErrMsg);
      else
        frm.submit();
    }
    function isEmpty(elm) {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
   }
  </SCRIPT>
<?php 
  echo "<FORM NAME=\"MForm\" ACTION=\"forum_module.php?insert_post=1&modif=$modif&modif_num=$modif_num&module=$module&parent=$num&grandPa=$grandPa&new=$new\" METHOD='post'>";
?> 
  
  <TR>
      <TD nowrap valign ='top'>
          <B> <?php echo $mess_mail_sujet;?> </B>      
      </TD>
      <TD nowrap valign ='top'>
          <?php if ($new == 1) $objet = ""; elseif ($modif == 1) $objet = "$sujet"; else $objet = "Re:  $sujet";?>
          <input type='text' class='INPUT' name= 'sujet' value='<?php echo "$objet";?>' size='74'>
      </TD>
   </TR>
  <TR>
      <TD nowrap valign ='top'>
          <B> <?php echo $mess_mail_mess;?> </B>
      </TD>
      <TD nowrap valign ='center'>
         <TEXTAREA class='TEXTAREA'  name="contenu" rows="8" cols="90" align="middle">
         <?php 
         if (isset($_GET['modif']) && $_GET['modif'] == 1)
         {
           $contenu = GetDataField ($connect,"select fm_body_lb from forums_modules where fm_cdn = $num","fm_body_lb");        
           echo html_entity_decode($contenu,ENT_QUOTES,'iso-8859-1');
         }
         ?>
         </TEXTAREA>
      </TD>
   </TR>
   <?php
   echo "<tr><td></td><td align='left'><A HREF=\"javascript:checkForm(document.MForm);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";

