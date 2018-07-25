<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require "graphique/admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
$agent=getenv("HTTP_USER_AGENT");
$nom= GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");;
$prenom=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");;
//$laphoto=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num'","util_photo_lb");
if (isset($_POST['charger']) && $_POST['charger'] == 1)
{
//echo "<pre>";print_r($_POST);echo "</pre>";
    $userfile = $_FILES["image"]["tmp_name"];
     $nom_fichier = $_FILES['image']['name'];
     $taille_file = $_FILES['image']['size'];
     $longueur = strlen($_FILES['image']['name']);
     $extension = substr($_FILES['image']['name'],$longueur-4,4);
    if (strtolower($extension) == ".gif" || strtolower($extension) == ".png"  || strtolower($extension) == ".jpg")
       $type_image = 1;
    else
       $type_image = 0;
    $le_nom = modif_nom($nom_fichier);
    $nom = modif_nom($nom);
    $prenom = modif_nom($prenom);
    $nom_inter = str_replace($extension,".jpg",$le_nom);
    $nom_final = "galerie/".$nom."_".$prenom."_".$num."_".$nom_inter;
    $dir = $repertoire."/images/galerie";
    $image_new = $nom."_".$prenom."_".$num."_".str_replace($extension,"",$le_nom);
    $FILENAME= $dir."/".$image_new;
    $RESIZEWIDTH = 120;
    $RESIZEHEIGHT = 150;

    // DO NOT EDIT BELOW HERE -----------------------------------------
        if($_FILES['image']['type'] == "image/pjpeg" || $_FILES['image']['type'] == "image/jpeg" || $_FILES['image']['type'] == "image/jpg"){
                $im = imagecreatefromjpeg($_FILES['image']['tmp_name']);
        }elseif($_FILES['image']['type'] == "image/x-png" || $_FILES['image']['type'] == "image/png"){
                $im = imagecreatefrompng($_FILES['image']['tmp_name']);
        }elseif($_FILES['image']['type'] == "image/gif"){
                $im = imagecreatefromgif($_FILES['image']['tmp_name']);
        }
        if($im){
                if(file_exists("$FILENAME.jpg")){
                        unlink("$FILENAME.jpg");
                }
            ResizeImage($im,$RESIZEWIDTH,$RESIZEHEIGHT,$FILENAME);
            ImageDestroy ($im);
        }
    if ($nom_fichier == "")
       $message = $mess_tel_img;
    else{
       if ($taille_file < 20000 && $type_image == 1)
       {
          $lien= "admin/modifiche.php?modifier=1&num=$num&annu=$annu&mafiche=$mafiche&complement=$complement&full=$full&id_grp=$id_grp&vient_de_menu=$vient_de_menu&lien_nom=$nom_final";
          $lien=urlencode($lien);
          echo "<SCRIPT Language=\"Javascript\">";
              echo "window.opener.location.replace(\"trace.php?link=$lien\")";
          echo "</SCRIPT>";
         ?>
         <SCRIPT language=javascript>
            setTimeout("Quit()",500);
             function Quit() 
             {
                 self.opener=null;self.close();return false;
             }
          </SCRIPT>
          <?php
          exit();
        }
        if (($type_image == 0 || $taille_file > 20000) && $nom_fichier != "")
           $message = "* <small>".strip_tags($insc_pds_foto)."<small>";
   }
}
  //de quel type est l'utilisateur (apprenant, formateur, administrateur)
include ('style.inc.php');
?>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
     if (isEmpty(frm.image)==true)
       ErrMsg += ' - <?php echo $mess_admin_photo;?>\n';
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
if (isset($photog) && $photog == 1)
   $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn = $num","util_photo_lb");
else
  $photo = "ecran_profil/ombre.jpg";
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD width='100%'>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='2' cellpadding ='8' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_ins_mod_photo</B></FONT></TD></TR>";
if (isset($message) && $message != "")
   echo "<TR><TD colspan='2' align='left'><b>$message</b></TD></TR>";
echo "<FORM NAME='form1' METHOD='POST' ENCTYPE=\"multipart/form-data\" action=''>";
echo "<TR><TD>";
echo "<input type='hidden' name='photo' value = '$photo'>";
echo "<input type='hidden' name='id_grp' value = '$id_grp'>";
echo "<input type='hidden' name='num' value = '$num'>";
echo "<input type='hidden' name='annu' value = '$annu'>";
echo "<input type='hidden' name='charger' value = '1'>";
echo "<input type='hidden' name='mafiche' value = '$mafiche'>";
echo "<input type='hidden' name='complement' value = '$complement'>";
echo "<input type='hidden' name='full' value = '$full'>";
echo "<input type='hidden' name='vient_de_menu' value = '$vient_de_menu'>";
echo "<INPUT TYPE='file' class='INPUT' name='image' size='35'>";
echo "</TD><TD><A href=\"javascript:checkForm(document.form1);\" onmouseover=\"img4.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img4.src='images/fiche_identite/boutvalid.gif'\">";
echo "<IMG NAME=\"img4\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</TD></TR></FORM></TABLE></TD></TR></TABLE></body></html>";
exit;
?>