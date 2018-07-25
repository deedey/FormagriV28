<?php

if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if ($lg == ""){
  include ('deconnexion-fr.txt');
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbconnect();
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}

echo "<html xmlns:IE><head>";
echo "<title>$votre_config</title>";

//include 'click_droit.txt';

?>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">

<STYLE>

@media all {
     IE\:clientCaps {behavior:url(#default#clientcaps)}
}

BODY { font-family: arial; font-size: 12px; color: #333333 }
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color=#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

</STYLE>

<SCRIPT LANGUAGE="JavaScript">

// var javaVMinstalle = 0;

 var javawsInstalled = 0;

 var javaws12Installed = 0;

 var javaws142Installed=0;

  isIE = "false";

  if (navigator.mimeTypes && navigator.mimeTypes.length) {

    x = navigator.mimeTypes['application/x-java-jnlp-file'];

    if (x) {

      javawsInstalled = 1;

      javaws12Installed=1;

      javaws142Installed=1;

    }

 }else {

   isIE = "true";

 }

</SCRIPT>

<SCRIPT LANGUAGE="VBScript">// Pour tester sous IE et windows

 on error resume next

 If isIE = "true" Then

  If Not(IsObject(CreateObject("JavaWebStart.isInstalled"))) Then

     javawsInstalled = 0

  Else

     javawsInstalled = 1

  End If

  If Not(IsObject(CreateObject("JavaWebStart.isInstalled.2"))) Then

     javaws12Installed = 0

  Else

     javaws12Installed = 1

  End If

  If Not(IsObject(CreateObject("JavaWebStart.isInstalled.1.4.2.0"))) Then

     javaws142Installed = 0

  Else

     javaws142Installed = 1

  End If

 End If

</SCRIPT>



<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">

    // convert all characters to lowercase to simplify testing

    var agt=navigator.userAgent.toLowerCase();

    var is_major = parseInt(navigator.appVersion);

    var is_minor = parseFloat(navigator.appVersion);

    var is_nav  = ((agt.indexOf('mozilla')!=-1) && (agt.indexOf('spoofer')==-1)

                && (agt.indexOf('compatible') == -1) && (agt.indexOf('opera')==-1)

                && (agt.indexOf('webtv')==-1) && (agt.indexOf('hotjava')==-1));

    var is_nav2 = (is_nav && (is_major == 2));

    var is_nav3 = (is_nav && (is_major == 3));

    var is_nav4 = (is_nav && (is_major == 4));

    var is_nav4up = (is_nav && (is_major >= 4));

    var is_navonly      = (is_nav && ((agt.indexOf(";nav") != -1) ||

                          (agt.indexOf("; nav") != -1)) );

    var is_nav6 = (is_nav && (is_major == 5));

    var is_nav6up = (is_nav && (is_major >= 5));

    var is_gecko = (agt.indexOf('gecko') != -1);

        if (is_gecko) {

                var gecko_version = 0;

                var rvStart = agt.indexOf('rv:');

                var rvEnd   = agt.indexOf(')', rvStart);

                var rv      = agt.substring(rvStart+3, rvEnd);

                var rvParts = rv.split('.');

                var exp     = 1;

                for (var i = 0; i < rvParts.length; i++)

                {

                  var val = parseInt(rvParts[i]);

                  gecko_version += val / exp;

                  exp *= 10;

                }

        }

        var is_mozilla = (is_nav6up && is_gecko && (agt.indexOf('mozilla')!=-1) && (agt.indexOf('netscape')==-1));



    var is_ie     = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));

    var is_ie3    = (is_ie && (is_major < 4));

    var is_ie4    = (is_ie && (is_major == 4) && (agt.indexOf("msie 4")!=-1) );

    var is_ie4up  = (is_ie && (is_major >= 4));

    var is_ie5    = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.0")!=-1) );

    var is_ie5_5  = (is_ie && (is_major == 4) && (agt.indexOf("msie 5.5") !=-1));

    var is_ie5up  = (is_ie && !is_ie3 && !is_ie4);

    var is_ie5_5up =(is_ie && !is_ie3 && !is_ie4 && !is_ie5);

    var is_ie6    = (is_ie && (is_major == 4) && (agt.indexOf("msie 6.")!=-1) );

    var is_ie6up  = (is_ie && !is_ie3 && !is_ie4 && !is_ie5 && !is_ie5_5);



    // KNOWN BUG: On AOL4, returns false if IE3 is embedded browser

    // or if this is the first browser window opened.  Thus the

    // variables is_aol, is_aol3, and is_aol4 aren't 100% reliable.

    var is_aol   = (agt.indexOf("aol") != -1);

    var is_aol3  = (is_aol && is_ie3);

    var is_aol4  = (is_aol && is_ie4);

    var is_aol5  = (agt.indexOf("aol 5") != -1);

    var is_aol6  = (agt.indexOf("aol 6") != -1);



    var is_opera = (agt.indexOf("opera") != -1);

    var is_opera2 = (agt.indexOf("opera 2") != -1 || agt.indexOf("opera/2") != -1);

    var is_opera3 = (agt.indexOf("opera 3") != -1 || agt.indexOf("opera/3") != -1);

    var is_opera4 = (agt.indexOf("opera 4") != -1 || agt.indexOf("opera/4") != -1);

    var is_opera5 = (agt.indexOf("opera 5") != -1 || agt.indexOf("opera/5") != -1);

    var is_opera5up = (is_opera && !is_opera2 && !is_opera3 && !is_opera4);



    var is_webtv = (agt.indexOf("webtv") != -1);



    var is_TVNavigator = ((agt.indexOf("navio") != -1) || (agt.indexOf("navio_aoltv") != -1));

    var is_AOLTV = is_TVNavigator;



    var is_hotjava = (agt.indexOf("hotjava") != -1);

    var is_hotjava3 = (is_hotjava && (is_major == 3));

    var is_hotjava3up = (is_hotjava && (is_major >= 3));



    // *** JAVASCRIPT VERSION CHECK ***

    var is_js;

    if (is_nav2 || is_ie3) is_js = 1.0;

    else if (is_nav3) is_js = 1.1;

    else if (is_opera5up) is_js = 1.3;

    else if (is_opera) is_js = 1.1;

    else if ((is_nav4 && (is_minor <= 4.05)) || is_ie4) is_js = 1.2;

    else if ((is_nav4 && (is_minor > 4.05)) || is_ie5) is_js = 1.3;

    else if (is_hotjava3up) is_js = 1.4;

    else if (is_nav6 || is_gecko) is_js = 1.5;

    // NOTE: In the future, update this code when newer versions of JS

    // are released. For now, we try to provide some upward compatibility

    // so that future versions of Nav and IE will show they are at

    // *least* JS 1.x capable. Always check for JS version compatibility

    // with > or >=.

    else if (is_nav6up) is_js = 1.5;

    // NOTE: ie5up on mac is 1.4

    else if (is_ie5up) is_js = 1.3



    // HACK: no idea for other browsers; always check for JS version with > or >=

    else is_js = 0.0;





    // *** PLATFORM ***

    var is_win   = ( (agt.indexOf("win")!=-1) || (agt.indexOf("16bit")!=-1) );

    // NOTE: On Opera 3.0, the userAgent string includes "Windows 95/NT4" on all

    //        Win32, so you can't distinguish between Win95 and WinNT.

    var is_win95 = ((agt.indexOf("win95")!=-1) || (agt.indexOf("windows 95")!=-1));



    // is this a 16 bit compiled version?

    var is_win16 = ((agt.indexOf("win16")!=-1) ||

               (agt.indexOf("16bit")!=-1) || (agt.indexOf("windows 3.1")!=-1) ||

               (agt.indexOf("windows 16-bit")!=-1) );



    var is_win31 = ((agt.indexOf("windows 3.1")!=-1) || (agt.indexOf("win16")!=-1) ||

                    (agt.indexOf("windows 16-bit")!=-1));



    var is_winme = ((agt.indexOf("win 9x 4.90")!=-1));

    var is_win2k = ((agt.indexOf("windows nt 5.0")!=-1));

        var is_winxp = ((agt.indexOf("windows nt 5.1")!=-1));



        // NOTE: Reliable detection of Win98 may not be possible. It appears that:

    //       - On Nav 4.x and before you'll get plain "Windows" in userAgent.

    //       - On Mercury client, the 32-bit version will return "Win98", but

    //         the 16-bit version running on Win98 will still return "Win95".

    var is_win98 = ((agt.indexOf("win98")!=-1) || (agt.indexOf("windows 98")!=-1));

    var is_winnt = ((agt.indexOf("winnt")!=-1) || (agt.indexOf("windows nt")!=-1));

    var is_win32 = (is_win95 || is_winnt || is_win98 ||

                    ((is_major >= 4) && (navigator.platform == "Win32")) ||

                    (agt.indexOf("win32")!=-1) || (agt.indexOf("32bit")!=-1));



    var is_os2   = ((agt.indexOf("os/2")!=-1) ||

                    (navigator.appVersion.indexOf("OS/2")!=-1) ||

                    (agt.indexOf("ibm-webexplorer")!=-1));



    var is_mac    = (agt.indexOf("mac")!=-1);

    // hack ie5 js version for mac

    if (is_mac && is_ie5up) is_js = 1.4;

    var is_mac68k = (is_mac && ((agt.indexOf("68k")!=-1) ||

                               (agt.indexOf("68000")!=-1)));

    var is_macppc = (is_mac && ((agt.indexOf("ppc")!=-1) ||

                                (agt.indexOf("powerpc")!=-1)));



    var is_sun   = (agt.indexOf("sunos")!=-1);

    var is_sun4  = (agt.indexOf("sunos 4")!=-1);

    var is_sun5  = (agt.indexOf("sunos 5")!=-1);

    var is_suni86= (is_sun && (agt.indexOf("i86")!=-1));

    var is_irix  = (agt.indexOf("irix") !=-1);    // SGI

    var is_irix5 = (agt.indexOf("irix 5") !=-1);

    var is_irix6 = ((agt.indexOf("irix 6") !=-1) || (agt.indexOf("irix6") !=-1));

    var is_hpux  = (agt.indexOf("hp-ux")!=-1);

    var is_hpux9 = (is_hpux && (agt.indexOf("09.")!=-1));

    var is_hpux10= (is_hpux && (agt.indexOf("10.")!=-1));

    var is_aix   = (agt.indexOf("aix") !=-1);      // IBM

    var is_aix1  = (agt.indexOf("aix 1") !=-1);

    var is_aix2  = (agt.indexOf("aix 2") !=-1);

    var is_aix3  = (agt.indexOf("aix 3") !=-1);

    var is_aix4  = (agt.indexOf("aix 4") !=-1);

    var is_linux = (agt.indexOf("inux")!=-1);

    var is_sco   = (agt.indexOf("sco")!=-1) || (agt.indexOf("unix_sv")!=-1);

    var is_unixware = (agt.indexOf("unix_system_v")!=-1);

    var is_mpras    = (agt.indexOf("ncr")!=-1);

    var is_reliant  = (agt.indexOf("reliantunix")!=-1);

    var is_dec   = ((agt.indexOf("dec")!=-1) || (agt.indexOf("osf1")!=-1) ||

           (agt.indexOf("dec_alpha")!=-1) || (agt.indexOf("alphaserver")!=-1) ||

           (agt.indexOf("ultrix")!=-1) || (agt.indexOf("alphastation")!=-1));

    var is_sinix = (agt.indexOf("sinix")!=-1);

    var is_freebsd = (agt.indexOf("freebsd")!=-1);

    var is_bsd = (agt.indexOf("bsd")!=-1);

    var is_unix  = ((agt.indexOf("x11")!=-1) || is_sun || is_irix || is_hpux ||

                 is_sco ||is_unixware || is_mpras || is_reliant ||

                 is_dec || is_sinix || is_aix || is_linux || is_bsd || is_freebsd);



    var is_vms   = ((agt.indexOf("vax")!=-1) || (agt.indexOf("openvms")!=-1));



//-->

</script>

<script language="JavaScript" type="text/javascript">

<!--



function AcrobatReaderNC()

{

var i;

var trouve;



trouve=false;

        if (document.layers)

        {

                for(i=0;i<navigator.plugins.length;i++)

                        if (navigator.plugins[i].name=="Adobe Acrobat"){

                                trouve=true;

                        }





        if (trouve)

                document.write("<font face=arial size=2>Acrobat Reader <?php echo $pdf_ok;?></font>")

        else

                document.write("<font face=arial size=2 color=red>Acrobat Reader <?php echo $pdf_no_ok;?></font>")

        }

}



function DetectNavigateur(icone)

{

var navigateur;

var version;

var img;



if (navigator.appName=="Netscape"){

        navigateur="Netscape";

        version=navigator.appVersion.substring(0,3);

        img="images/config/netscape.gif";

}

else{

        navigateur="Internet Explorer";

        version=navigator.appVersion.substring(navigator.appVersion.indexOf("MSIE")+5,navigator.appVersion.indexOf(";",navigator.appVersion.indexOf("MSIE")));

        img="images/config/ie_single.gif";

}



if (icone)

        document.write("<img src=" + img + " alt='' border='0'>");

else

        document.write("<font face=arial size=2>" + navigateur + " " + version + "<?php echo $nav_ok;?></font>");



}







//-->

</SCRIPT>






</head>
<?php
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
?>
<BODY BGCOLOR="<?php echo $bkg;?>" TEXT="#000000" LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0">

<IE:clientCaps ID="oClientCaps" />


<SCRIPT Language="JScript">

  if ( "Microsoft Internet Explorer" == navigator.appName ){

       //Running in Internet Explorer.

       if ((sMSvmVersion = oClientCaps.getComponentVersion("{08B0E5C0-4FCB-11CF-AAA5-00401C608500}", "ComponentID"))){

            //Microsoft VM is present.

            javaVMinstalle = 1

       }else{

            //Microsoft VM is not present.

            javaVMinstalle = 0

       }

  }else{

      //Not running in Internet Explorer.

  }

</SCRIPT>


<?php

    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD width='100%'>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$votre_config</B></FONT></TD></TR>";
    echo "<TR><TD align='middle'>";

?>



<table border="0" cellpadding=4 cellspacing=1 width='100%'>

  <tr>

      <script language="JavaScript" type="text/javascript">

      <!--

        var is_badConfig=false;

        var strConfigInfosOK="";

        var strConfigInfosBad="";



        if (is_win) { // OS=Windows

                var strWinVersion="";

                var imgFile=null;

                if (is_win95 || is_win98) {

                        strWinVersion="95/98/Me";

                        imgFile="W2000.gif";

                } else if (is_winnt && !is_win2k && !is_winxp) {

                        strWinVersion="NT";

                        imgFile="W2000.gif";

                } else if (is_winme) {

                        strWinVersion="Me";

                        imgFile="W2000.gif";

                } else if (is_win2k) {

                        strWinVersion="2000";

                        imgFile="W2000.gif";

                } else if (is_winxp) {

                        strWinVersion="XP";

                        imgFile="Xp.gif";

                }

                document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                        if (imgFile) {

                                document.write("<img src=\"images/config/"+ imgFile +"\" alt=\"Windows "+ strWinVersion +"\" border=\"0\">");

                        }

                document.write("</td>");

                document.write("<td bgcolor=\"#EFEFEF\">");

                strConfigInfosOK+="<?php  echo $syst_exp;?>Windows "+ strWinVersion +"<br>";

                document.write(strConfigInfosOK +"</td>");

        } else if (is_mac) {

                var strWinVersion="";

                var imgFile=null;

                        strWinVersion="Mac Intosh";

                        imgFile="apple.gif";

                document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                        if (imgFile) {

                                document.write("<img src=\"images/config/"+ imgFile +"\" alt=\"Apple "+ strWinVersion +"\" border=\"0\">");

                        }

                document.write("</td>");

                document.write("<td bgcolor=\"#EFEFEF\">");

                        strConfigInfosOK+="<?php  echo $syst_exp;?> Mac<br>";

                document.write(strConfigInfosOK +"</td>");

        } else if (is_linux) {

                        document.write("Linux");

                        strConfigInfosBad+="<?php  echo $syst_exp;?>Linux<?php  echo $linux_avert;?><br>";

        } else {

                //document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"><img src=\"images/avertissement.gif\" alt=\"Attention !\"></td>");

                is_badConfig=true;

                document.write("<td bgcolor=\"#EFEFEF\">");

                        document.write("OS");

                        strConfigInfosBad+="<?php  echo $syst_bad;?><br>";

                document.write("</td>");

        }

      //-->

                /**************************************************************

                        DETECTION FLASH PLAYER (source Macromedia)

                ***************************************************************/

      </SCRIPT>

  </TR>

  <tr>

      <script language="JavaScript" type="text/javascript">

function DetectNavigateur(icone)

{

var navigateur;

var version;

var img;



  if (navigator.appName=="Netscape"){

        navigateur="Netscape";

        version=navigator.appVersion.substring(0,3);

        img="images/config/netscape.gif";

  }

  else{

        navigateur="Internet Explorer";

        version=navigator.appVersion.substring(navigator.appVersion.indexOf("MSIE")+5,navigator.appVersion.indexOf(";",navigator.appVersion.indexOf("MSIE")));

        img="images/config/ie_single.gif";

  }

        document.write(version);

}

</SCRIPT>

      <script language="JavaScript" type="text/javascript">

        if (is_nav) { // Netscape et assimilés

                if (is_nav6 || is_nav6up || is_gecko) { // si c'est une version supérieure ou égale à Netscape 6

                        //document.write(gecko_version);

                        if (is_gecko && (gecko_version>=0.94)) {

                                /*

                                Netscape 6.1         0.9.2

                                Netscape 6.2         0.9.4

                                Netscape 6.2.1         0.9.4

                                Netscape 6.2.2         0.9.4.1

                                Netscape 6.2.3         0.9.4.1

                                CompuServe 7         0.9.4.2

                                Netscape 7.0         1.0.1

                                Netscape 7.01         1.0.2

                                */

                                       document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                                        if (is_mozilla) {

                                                document.write("<img src=\"images/config/mozilla.gif\" alt=\"Mozilla\" border=\"0\">");

                                                document.write("</td><td bgcolor=\"#EFEFEF\">");

                                                strConfigInfosOK="Mozilla : <?php  echo $version_support;?> ";

                                                document.write(strConfigInfosOK);

                                                DetectNavigateur();

                                                document.write(")</td>");

                                        } else {

                                                document.write("<img src=\"images/config/netscape.gif\" alt=\"Netscape\" border=\"0\">");

                                                document.write("</td><td bgcolor=\"#EFEFEF\">");

                                                strConfigInfosOK="Netscape : <?php  echo $version_support;?> ";

                                                document.write(strConfigInfosOK);

                                                DetectNavigateur();

                                                document.write(")</td>");

                                        }

                        } else {

                                document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                                document.write("<img src=\"images/config/netscape.gif\" alt=\"Netscape\" border=\"0\">");

                                document.write("</td><td bgcolor=\"#EFEFEF\">");

                                is_badConfig=true;

                                strConfigInfosBad="<?php  echo $version_N6;?><br>";

                                document.write(strConfigInfosBad);

                                document.write("</td>");

                        }

                   }

           } else if (is_ie) { // IE et assimilés

                if(is_ie5up) { // si c'est une version supérieure ou égale à IE 5

                        document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                        document.write("<img src=\"images/config/ie_single.gif\" alt=\"Internet Explorer\" border=\"0\">");

                        document.write("</td><td bgcolor=\"#EFEFEF\">");

                        strConfigInfosOK="Internet Explorer : <?php  echo $version_support;?> ";

                        document.write(strConfigInfosOK);

                        DetectNavigateur();

                        document.write(")</td>");

                } else {

                        document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

                        document.write("<img src=\"images/config/ie_single.gif\" alt=\"Internet Explorer\" border=\"0\">");

                        document.write("</td><td bgcolor=\"#EFEFEF\">");

                        is_badConfig=true;

                        strConfigInfosBad="Internet Explorer : <?php  echo $version_obsol;?><br>";

                        document.write(strConfigInfosBad);

                        document.write("</td>");

                }

          } else {



          }





</script>

  </tr>

  <tr>

<script language="JavaScript" type="text/javascript">



var MM_contentVersion = 5;



var MM_PluginVersionDetails="";

var plugin = (navigator.mimeTypes && navigator.mimeTypes["application/x-shockwave-flash"]) ? navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin : 0;

if ( plugin ) {

        var words = navigator.plugins["Shockwave Flash"].description.split(" ");

        for (var i = 0; i < words.length; ++i) {

                if (isNaN(parseInt(words[i])))

                continue;

                var MM_PluginVersion = words[i];

                var re=new RegExp("r(\\d+)", "gi");

                var arr=re.exec(navigator.plugins["Shockwave Flash"].description);

                if(arr) {

                        var MM_PluginRelease=arr[1];

                }

        }

        var MM_FlashCanPlay = MM_PluginVersion >= MM_contentVersion;



        if (MM_PluginVersion) {

                MM_PluginVersionDetails+=" (version: ";

                MM_PluginVersionDetails+=MM_PluginVersion;

                MM_PluginVersionDetails+=")";

        }

}else if (navigator.userAgent && navigator.userAgent.indexOf("MSIE")>=0 && (navigator.appVersion.indexOf("Win") != -1)) {

        document.write('<SCR' + 'IPT LANGUAGE=VBScript\> \n'); //FS hide this from IE4.5 Mac by splitting the tag

        document.write('on error resume next \n');

        document.write('MM_FlashCanPlay = ( IsObject(CreateObject("ShockwaveFlash.ShockwaveFlash." & MM_contentVersion)))\n');

        document.write('</SCR' + 'IPT\> \n');

}



if        (MM_FlashCanPlay && is_gecko) {

        if (gecko_version>=0.94) {



                MM_FlashCanPlay=!((MM_PluginVersion<6.0) || ((MM_PluginVersion==6.0) && MM_PluginRelease<40));

        } else {

                MM_FlashCanPlay=false;

        }

}

</script>
<?php
/*

<script language="JavaScript" type="text/javascript">



                                        Detection JAVA



if (navigator.appName != "Microsoft Internet Explorer"){

  if (navigator.javaEnabled()){

    document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"> <img src=\"images/config/java.gif\" alt=\"Java\" border=\"0\"></td>  <td bgcolor=\"#EFEFEF\">Java <?php  echo "$pdf_ok $java_ok";?></td></TR><TR>");

  }else{

    document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"><A HREF=\"http://www.java.com/fr/\" target=\"_blank\" title=\"<?php  echo $mess_telecharger;?>\">  <img src=\"images/config/java.gif\" alt=\"Java\" border=\"0\"></A></td><TD bgcolor=\"#EFEFEF\">Java <?php  echo "$pdf_no_ok $java_ok";?></TD></TR><TR>");

  }

}

</SCRIPT>

<script language="JavaScript" type="text/javascript">

if (navigator.appName == "Microsoft Internet Explorer"){

  if (javawsInstalled || javaws12Installed || javaws142Installed){

    document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"><img src=\"images/config/java.gif\" alt=\"Java\" border=\"0\"></td>  <td bgcolor=\"#EFEFEF\">Java <?php  echo "$pdf_ok $java_ok";?></td></TR><TR>");

  }else if (javaVMinstalle == 1){

    document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"><img src=\"images/config/java.gif\" alt=\"Java\" border=\"0\"></td>  <td bgcolor=\"#EFEFEF\">Java <?php  echo "$pdf_ok $java_ok";?></td></TR><TR>");

  }else{

    document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\"><A HREF=\"http://ef-dev.educagri.fr/java/JavaVM3186.exe\" target=\"_blank\" title=\"<?php  echo $mess_telecharger;?>\">  <img src=\"images/config/java.gif\" alt=\"Java\" border=\"0\"></A></td><TD bgcolor=\"#EFEFEF\">Java <?php  echo "$pdf_no_ok $java_ok";?></TD></TR><TR>");

  }

}

</SCRIPT>
*/
?>
<script language="JavaScript" type="text/javascript">

if ( MM_FlashCanPlay ) {

        document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

        document.write("<img src=\"images/config/flash.gif\" alt=\"Flash Player\" border=\"0\">");

        document.write("</td><td bgcolor=\"#EFEFEF\">");

        strConfigInfosOK="Macromedia Flash Player <?php echo $pdf_ok ;?>"+ MM_PluginVersionDetails;

        document.write(strConfigInfosOK);

        document.write("</td>");

}else{

        document.write("<td bgcolor=\"#dee3e7\" align=center height=\"47\" valign=\"center\">");

        document.write("<img src=\"images/config/flash.gif\" alt=\"Flash Player\" border=\"0\">");

        is_badConfig=true;

        document.write("</td><td bgcolor=\"#EFEFEF\">");

        strConfigInfosBad="Macromedia Flash Player : <?php  echo $version_obsol;?>"+ MM_PluginVersionDetails;

        document.write(strConfigInfosBad);

        document.write("</td>");

}

//-->

    </script>

      </tr>
        <tr valign="top">
          <td height="47" align=center bgcolor="#dee3e7"><IMG SRC="images/config/logecran.gif" border='0'></td>
          <td bgcolor="#EFEFEF" valign='center'>
            <script>
                  document.write("<?php echo $mess_resol_ecran;?> : "+screen.width+"*"+screen.height );
                  </script>
            <script>
                  if (screen.width==640)
               document.write("<BR><?php echo $mess_gene1_ecran;?>" );
                    else if (screen.width == 800)
            document.write("<BR><?php echo $mess_gene2_ecran;?>" );
                else if (screen.width >= 800)
            document.write("<BR><?php echo $mess_gene3_ecran;?>");
                  </script>
            </td>
        </tr>


   <?php /*
   <TR><TD colspan=2 valign='center'><BR>

   if (strstr($HTTP_USER_AGENT,"MSIE")){?>

     <APPLET CODE="whatever" WIDTH="400" HEIGHT="2">

       <TABLE BORDER="0" CELLSPACING="4" WIDTH="400" HEIGHT="10" BGCOLOR="#FFFFCC">

         <TR><TD><A HREF="http://www.java.com/fr/" target="_blank" title="<?php  echo $mess_telecharger;?>">  <img src="images/java.gif" alt="Java" width="30" height="30" border="0"></A></TD><TD align="left">Java <?php  echo $pdf_no_ok ;?></TD></TR>

       </TABLE>

     </APPLET></TD></TR>

   <?php }
*/
   ?>
</table></TD></TR></TABLE></TD></TR></TABLE>
</body>
</html>