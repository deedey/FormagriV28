<html>
<head>
<meta http-equiv="Content-Language" content="fr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Tester la configuration de ef-concours</title>
<link rel="stylesheet" href="css/base.css" type="text/css" />

<script language="JavaScript" type="text/javascript">

//***************************************************************************
//Détermination de la plateforme et du système installé
//***************************************************************************
var systeme = "";
if ( navigator.userAgent.indexOf('95') != -1 ) { systeme = 'Windows 95'; }
else if ( navigator.userAgent.indexOf('98') != -1 ) { systeme = 'Windows 98'; }
else if ( navigator.userAgent.indexOf('NT 4.0') != -1 ) { systeme = 'Windows NT '; }
else if ( navigator.userAgent.indexOf('NT 5.0') != -1 ) { systeme = 'Windows 2000 '; }
else if ( navigator.userAgent.indexOf('NT 5.1') != -1 ) { systeme = 'Windows XP '; }
else if ( navigator.userAgent.indexOf('Mac') != -1 ) { systeme = 'Mac OS'; }
else if ( navigator.userAgent.indexOf('Unix') != -1 ) { systeme = 'Unix'; }
else if ( navigator.userAgent.indexOf('Linux') != -1 ) { systeme = 'Linux'; }
else { systeme = 'Inconnu'; }

var diag = "";
if ( navigator.userAgent.indexOf('Win') != -1 ) { diag = 'Pas de problème particulier en perspective avec ce type de configuration.'; }
else if ( navigator.userAgent.indexOf('Mac') != -1 ) { diag = 'Vous pouvez rencontrer des problèmes avec ce type de configuration.'; }
else if ( navigator.userAgent.indexOf('Unix') != -1 ) { diag = 'Vous pouvez rencontrer des problèmes avec ce type de configuration.'; }
else if ( navigator.userAgent.indexOf('Linux') != -1 ) { diag = 'Vous pouvez rencontrer des problèmes avec ce type de configuration.'; }
else { diag = 'Vous pouvez rencontrer des problèmes avec ce type de configuration.'; }


//***************************************************************************
// Détermination du navigateur
//***************************************************************************
var navigateur = navigator.userAgent;
	
	// recherche du type de navigateur
	this.name = navigator.appName;
	this.version = navigator.appVersion;			        
	this.useragent=navigator.userAgent;
	this.dom=document.getElementById?1:0			                
	op7=(this.useragent.indexOf("Opera 7") > -1)?1:0;
	op6=(this.useragent.indexOf("Opera 6") > -1)?1:0;
	op5=(this.useragent.indexOf("Opera 5") > -1)?1:0;
	ie6=(this.useragent.indexOf("MSIE 6") > -1 && !op7)?1:0;
	ie5=(this.useragent.indexOf("MSIE 5") > -1 && !op6 && !op5)?1:0;
	ie4=(this.useragent.indexOf("MSIE 4") > -1)?1:0;
	ie3=(this.useragent.indexOf("MSIE 3") > -1)?1:0;
	moz=(this.useragent.indexOf("Gecko") > -1 && this.useragent.indexOf("Firefox") == -1 && this.useragent.indexOf("Konqueror") == -1 && this.useragent.indexOf("Netscape") == -1)?1:0;
	ffx=(this.useragent.indexOf("Firefox") > -1)?1:0;
	ns7=(this.useragent.indexOf("Netscape/7") > -1)?1:0;
	ns6=(this.useragent.indexOf("Netscape6") > -1)?1:0;
	ns4=(document.layers && !this.dom)?1:0
	ns3=(this.useragent.indexOf("Mozilla/3") > -1)?1:0;
	kon3=(this.useragent.indexOf("Konqueror/3") > -1)?1:0;
	
	// attribution d'une valeur à la vraiable navname
	if (ie3==1) navname="Microsoft Internet Explorer 3";
	else if (ns3==1) navname="Netscape 3";
	else if (ie4==1) navname="Microsoft Internet Explorer 4.x";
	else if (ie5==1) navname="Microsoft Internet Explorer 5.x";
	else if (ie6==1) navname="Microsoft Internet Explorer 6.x";
	else if (op5==1) navname="Opera 5.x";
	else if (op6==1) navname="Opera 6.x";
	else if (op7==1) navname="Opera 7.x";
	else if (moz==1) navname="Mozilla";
	else if (ffx==1) navname="Firefox";
	else if (ns4==1) navname="Netscape 4.x";
	else if (ns6==1) navname="Netscape 6.x";
	else if (ns7==1) navname="Netscape 7.x";
	else if (kon3==1) navname="Konqueror 3.x";
	else navname="non reconnu";
	
	//Attribution d'une image
	if (ie3==1) imgnav='<img id="img_nav" src="images/explorer.jpg">';
	else if (ns3==1) navname="Netscape 3";
	else if (ie4==1) imgnav='<img id="img_nav" src="images/explorer.jpg">';
	else if (ie5==1) imgnav='<img id="img_nav" src="images/explorer.jpg">';
	else if (ie6==1) imgnav='<img id="img_nav" src="images/explorer.jpg">';
	else if (op5==1) imgnav='<img id="img_nav" src="images/opera.jpg">';
	else if (op6==1) imgnav='<img id="img_nav" src="images/opera.jpg">';
	else if (op7==1) imgnav='<img id="img_nav" src="images/opera.jpg">';
	else if (moz==1) imgnav='<img id="img_nav" src="images/mozilla.png">';
	else if (ffx==1) imgnav='<img id="img_nav" src="images/firefox.jpg">';
	else if (ns4==1) imgnav='<img id="img_nav" src="images/netscape.jpg">';
	else if (ns6==1) imgnav='<img id="img_nav" src="images/netscape.jpg">';
	else if (ns7==1) imgnav='<img id="img_nav" src="images/netscape.jpg">';
	else if (kon3==1) imgnav='<img id="img_nav" src="images/konqueror.jpg">';
	else imgnav='<img id="img_nav" src="images/autre.jpg">';
	
	// Commentaires
	if(ie3==1 || ns3==1 || ie4==1 || op5==1)
	nav = "La version de navigateur (<i><b>"+navname+"</b></i>) n'assure pas un fonctionnement optimal de la plate-forme";
	else if (ie5==1 || ie6==1 || ns4==1 || ns6==1 || ns7==1 || moz==1 || ffx==1 )
	nav = "Votre navigateur (<i><b>"+navname+"</b></i>) permet d'utiliser la plate-forme dans de bonnes conditions";
	else if (op6==1 || op7==1 || kon3==1)
	nav = "Aucun test avec <i><b>"+navname+"</b></i> n'a pour le moment été fait pour valider la plate-forme.";
	else
	nav = "Aucun test n'a pour le moment été fait pour valider la plate-forme sur votre navigateur.";
				
//***************************************************************************
// Détermination de la résolution de l'écran
//***************************************************************************
var resecran = screen.width+"*"+screen.height+"\navec une palette de couleurs de "+screen.colorDepth+" bits";
		 
	if (screen.width==640)
    ecran = "Vous risquez d'être très gêné(e) pour afficher certaines pages. Pour un meilleur confort de travail, nous vous conseillons donc de modifier votre résolution d'écran." ;
   	else if (screen.width == 800)
    ecran = "Cette résolution d'écran ne vous empêchera pas de travailler mais vous devrez utiliser davantage les barres de défilement de votre navigateur." ;	
	else if (screen.width >= 800)
    ecran = "Cette résolution vous offre un confort de lecture optimum.";

//***************************************************************************
// Présence et version du plug-in Acrobat
//***************************************************************************
var acrobat=new Object();
	acrobat.installed=false;
	acrobat.version='0.0';

if (navigator.plugins && navigator.plugins.length) {
for (x=0; x<navigator.plugins.length; x++) {
 if (navigator.plugins[x].description.indexOf('Adobe Acrobat') != -1)  {
  acrobat.version=parseFloat(navigator.plugins[x].description.split('Version ')[1]);
  if (acrobat.version.toString().length == 1) acrobat.version+='.0';
  acrobat.installed=true;
  break;
 }
}
} else if (window.ActiveXObject) {
for (x=2; x<10; x++)
{  try  {
  oAcro=eval("new ActiveXObject('PDF.PdfCtrl."+x+"');");
  if (oAcro)  {
   acrobat.installed=true;
   acrobat.version=x+'.0';
  }
 } catch(e) {}
} try {
 oAcro4=new ActiveXObject('PDF.PdfCtrl.1');
 if (oAcro4)  {
  acrobat.installed=true;
  acrobat.version='4.0';
 }
} catch(e) {} }
acrobat.ver4=(acrobat.installed && parseInt(acrobat.version) >= 4) ? true:false;
acrobat.ver5=(acrobat.installed && parseInt(acrobat.version) >= 5) ? true:false;
acrobat.ver6=(acrobat.installed && parseInt(acrobat.version) >= 6) ? true:false;
acrobat.ver7=(acrobat.installed && parseInt(acrobat.version) >= 7) ? true:false;
acrobat.ver8=(acrobat.installed && parseInt(acrobat.version) >= 8) ? true:false;
acrobat.ver9=(acrobat.installed && parseInt(acrobat.version) >= 9) ? true:false; 
</script>

<OBJECT CLASSID="clsid:CA8A9780-280D-11CF-A24D-444553540000"
 name="PDF" width="1" height="1"
 id="PDF"
 ViewAsText>
</OBJECT>

<script type="text/javascript">
//document.write ( "<font color=red>Votre navigateur est </font><br>" + navigator.appName );
//document.write('Version ');
var navigateur = navigator.appName;

if ( navigateur == "Netscape"|| navigateur == "Opera" ) {
if (acrobat.installed) {
 	pacrobat="Version "+acrobat.version+" installée";
	acrobat="Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
} else {
 	pacrobat="Acrobat Reader n'est pas installé";
	acrobat="Attention, vous ne pourrez pas lire certaines ressources, téléchargez et installez l'application Acrobat Reader";
}
} else if ( navigateur == "Microsoft Internet Explorer" ) {
var flux = PDF.GetVersions();
if(flux == '') {
 	pacrobat="Version "+acrobat.version+" installée";
	acrobat="Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
} else {
var indice= flux.indexOf("AcroForm");
indice = indice + 9;
//document.write(indice);
var version_acrobat =flux.substring(indice, indice + 5)
	pacrobat="Version "+version_acrobat+" installée";
	acrobat="Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
}
} else if (navigator.userAgent.indexOf('Mac') != -1 ) {
  	pacrobat=navigator.plugins["PDFViewer"].description;
 	acrobat="Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
} else {
	pacrobat="Détection de l'application impossible pour ce navigateur";
  	acrobat="Attention, vous ne pourrez peut-être pas lire certaines ressources, téléchargez et installez l'application Acrobat Reader";
}

/*******************************************************
JAVA DETECT (NETSCAPE)
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
*******************************************************/
var java=new Object();
java.installed=navigator.javaEnabled() ? true:false;
java.version='0.0';

var numPlugs=navigator.plugins.length;
if (numPlugs)
{
	for (var x=0; x<numPlugs; x++)
	{
		var pluginjava = navigator.plugins[x];

		if (pluginjava.name.toLowerCase().indexOf('java plug-in') != -1)
		{
			java.version=pluginjava.description.toLowerCase().split('java plug-in ')[1].split(' for')[0];
			break;
		}
	}
}
if (java.installed)
{
  pjava="Application installée"; 
  java="Pas de problème particulier en perspective."
} else	{ 
  pjava="Application non installée"; 
  java="Demandez à votre responsable informatique d'installer l'application Java.";
}

//***************************************************************************
// Détermination de l'affichage des popups
//***************************************************************************
	var resultatTest = false; 
	function probleme() {
  	resultatTest = false;
	}
	window.onerror = probleme;
	var monPopup = window.open("", "poptest", "width=100, height=100, left=50, top=50", true);
	monPopup.blur(); 
	monPopup.close(); 
	resultatTest = true;
	
	if (resultatTest) 
  	fpopup="Le navigateur autorise l'ouverture de fenêtres";
	else 
  	fpopup="Il semble que le navigateur dispose d'un bloqueur de fenêtres";
  
	if (resultatTest) 
	popup = "C'est parfait !"
	else 
  	popup = "Vous risquez de ne pouvoir d'afficher certaines pages. Veuillez autoriser les pop-up pour ce site et réactualiser la page."

</script>

<script language="VBScript">

//***************************************************************************
// Présence et version du plug-in Flash
//***************************************************************************

<!-- // Visual basic helper required to detect Flash Player ActiveX control version information
      Function VBGetSwfVer(i)
        on error resume next
        Dim swControl, swVersion
        swVersion = 0
        
        set swControl = CreateObject("ShockwaveFlash.ShockwaveFlash." + CStr(i))
        if (IsObject(swControl)) then
          swVersion = swControl.GetVariable("$version")
        end if
        VBGetSwfVer = swVersion
      End Function
// -->
</script>

<SCRIPT LANGUAGE="JavaScript" type="text/javascript">

<!-- // Detect Client Browser type
var isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;

// JavaScript helper required to detect Flash Player PlugIn version information
function JSGetSwfVer(i){
      // NS/Opera version >= 3 check for Flash plugin in plugin array
      if (navigator.plugins != null && navigator.plugins.length > 0) {
            if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
                  var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
                        var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
                        descArray = flashDescription.split(" ");
                        tempArrayMajor = descArray[2].split(".");
                        versionMajor = tempArrayMajor[0];
                  if ( descArray[3] != "" ) {
                        tempArrayMinor = descArray[3].split("r");
                  } else {
                        tempArrayMinor = descArray[4].split("r");
                  }
                        versionMinor = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
                        flashVer = parseFloat(versionMajor + "." + versionMinor);
            } else {
                  flashVer = -1;
            }
      }
      // MSN/WebTV 2.6 supports Flash 4
      else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
      // WebTV 2.5 supports Flash 3
      else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
      // older WebTV supports Flash 2
      else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
      // Can't detect in all other cases
      else {
            
            flashVer = -1;
      }
      return flashVer;
}
function detectflash (){
  for (i=25;i>0;i--) {
    versionStr = JSGetSwfVer(i);
    return versionStr;
 }
}
// -->
</script>

<script language="vbscript">
for i=25 to 1 step -1
  if(VBGetSwfVer(i)<>"0") then
    pflash=VBGetSwfVer(i)
	  pflash=Replace(pflash,"WIN ","")
	  pflash=Replace(pflash,",",".")
		pflash="Version "+pflash+" installée"
		flash = "Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application."
	  exit for
  else
  	pflash="Flash non activé ou non installé"
   	flash = "Attention, vous ne pourrez pas lire certaines ressources, téléchargez et installez l'application Shockwave Flash"
  end if
next
</script>

<script language="javascript">
var test=detectflash();
browsername=navigator.appName;
//si c'est microsoft on cache, quid de ie mac ??
if(browsername.indexOf("Microsoft")==-1){
  if(test==-1){
	  pflash="Flash non activé ou non installé";
   	  flash = "Attention, vous ne pourrez pas lire certaines ressources, téléchargez et installez l'application Shockwave Flash";
	} else {
    pflash="Version "+test+" installée";
	flash = "Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
	}
} else if (navigator.userAgent.indexOf('Mac') != -1 ) { 
  pflash="Version "+test+" installée";
  flash = "Si vous avez au moins une version 6, c'est parfait ! Sinon, mettez à jour cette application.";
}

</script>
</head>

<body>
<center>

	<table width="98%" border="0" cellspacing="2" cellpadding="0">
  		<tr>
    		<td height="1" valign="middle" bgcolor="#407EAD"></td>
  		</tr>
  		<tr> 
    		<td height="63" valign="middle" bgcolor="#037285" class="bandeau"> &nbsp;&nbsp;Test
      de configuration de votre poste de travail </td>
  		</tr>
  		<tr>
    		<td height="1" valign="middle" bgcolor="#407EAD"></td>
  		</tr>
  		<tr><td height="10" valign="top"></td></tr>
	</table>
	
	<table width="98%" border="0" cellspacing="0" cellpadding="5" align="center">
    <tr valign="top">
          <td rowspan="2" valign="middle" class="normal"><div align="center"><img src="images/gus01.gif" width="104" height="98"> 
      </div></td>
          <td colspan="2" class="normal"><strong>Notez bien les r&eacute;sultats de ces tests.
              Vous devrez en faire part &agrave; votre &quot;responsable informatique&quot; si, par
              hasard, vous rencontrez des probl&egrave;mes techniques.</strong></td>
      </tr>
        <tr valign="top" > 
          <td valign="middle" class="normal"><img src="images/important.jpg" width="30" height="26"></td>
          <td valign="middle" class="normal"><em>Si aucun r&eacute;sultat ne
              s'affiche dans le tableau ci-dessous, vous devrez activer l'option &quot;JavaScript&quot; dans
              votre navigateur... Pour cela, prenez contact avec votre responsable informatique. </em></td>
        </tr>
	</table>
	
	<table width="98%" border="0" cellspacing="0" cellpadding="5" align="center">
  		<tr><td colspan="4" height="10"></td></tr>
  		<tr class="tabcell">
    		<td colspan="2" class="textegras">Configuration</td>
    		<td class="textegras">R&eacute;sultat(s)</td>
    		<td class="textegras">Commentaire(s)</td>
    	</tr>
  		<tr bgcolor="#F4F4F4">
	  		<td width="5%"><img src="images/Computer.jpg" alt="ordinateur" width="38" height="38"></td>
    		<td width="20%"><strong> Syst&egrave;me d'exploitation </strong></td>
    		<td width="37%"><script>document.write(systeme)</script></td>
    		<td width="37%"><em><script>document.write(diag)</script></em></td>
	  </tr>

  		<tr>
    		<td><script>document.write(imgnav);</script></td>
    		<td><strong> Navigateur utilis&eacute;</strong></td>
    		<td><script>document.write(navigateur);</script></td>
    		<td><em><script>document.write(nav);</script></em></td>
    	</tr>
  		<tr bgcolor="#F4F4F4">
    		<td><img src="images/screen.jpg" alt="ecran" width="38" height="38"></td>
    		<td><strong> R&eacute;solution d'affichage</strong></td>
    		<td><script>document.write(resecran);</script></td>
    		<td><em><script>document.write(ecran);</script></em></td>
    	</tr>
  		<tr>
    		<td><img src="images/popup.jpg" alt="popup" width="38" height="38"></td>
    		<td><strong> Bloqueur de fen&ecirc;tres</strong></td>
    		<td><script>document.write(fpopup);</script></td>
    		<td><em><script>document.write(popup);</script></em></td>
    	</tr>
  		<tr bgcolor="#F4F4F4">
    		<td><img src="images/flash.gif" alt="flash" width="38" height="39"></td>
    		<td><strong> Plug-in Flash Player </strong></td>
		    <td><script>document.writeln(pflash);</script></td>
		    <td><em><script>document.writeln(flash);</script></em></td>
    	</tr>
  		<tr>
    		<td><img src="images/acroread.jpg" alt="acrobat" width="38" height="38"></td>
    		<td><strong> Plug-in Acrobat Reader </strong></td>
    		<td><script>document.writeln(pacrobat);</script></td>
    		<td><em><script>document.writeln(acrobat);</script></em></td>
    	</tr>
  		<tr bgcolor="#F4F4F4">
    		<td><img src="images/java.jpg" alt="java" width="38" height="38"></td>
    		<td><strong> Plug-in Java</strong></td>
    		<td><script>document.write(pjava);</script></td>
    		<td><em><script>document.write(java);</script></em></td>
    	</tr>
  		<tr>
  		  <td colspan="4" class="lien">&copy; Formagri 2007</td>
	    </tr>
  	</table>
</center>
</body>
</html>
