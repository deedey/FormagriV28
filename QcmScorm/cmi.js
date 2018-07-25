// Hook pour Internet Explorer
if (navigator.appName && navigator.appName.indexOf("Microsoft") != -1 && navigator.userAgent.indexOf("Windows") != -1 && navigator.userAgent.indexOf("Windows 3.1") == -1) {
	document.write('<script language=\"VBScript\"\>\n');
	document.write('On Error Resume Next\n');
	document.write('Sub qcm_FSCommand(ByVal command, ByVal args)\n');
	document.write('	Call qcm_DoFSCommand(command, args)\n');
	document.write('End Sub\n');
	document.write('</script\>\n');
}

// Modifiez ces valeurs pr?d?finies selon vos besoins et vos pr?f?rences.
var g_bShowApiErrors = false; 	// D?finissez la valeur sur true pour afficher les messages d'erreur
var g_strAPINotFound = "Management system interface not found.";
var g_strAPITooDeep = "Cannot find API - too deeply nested.";
var g_strAPIInitFailed = "Found API but LMSInitialize failed.";
var g_strAPISetError = "Trying to set value but API not available.";
var g_strFSAPIError = 'LMS API adapter returned error code: "%1"\nWhen FScommand called API.%2\nwith "%3"';
var g_strDisableErrorMsgs = "Select cancel to disable future warnings.";

var g_nFindAPITries = 0;
var g_oAPI = null;
var g_bInitDone = false;
var g_bFinishDone = false;

function AlertUserOfAPIError(strText) {
	if (g_bShowApiErrors) {
		var s = strText + "\n\n" + g_strDisableErrorMsgs;
		if (!confirm(s)){
			g_bShowApiErrors = false
		}
	}
}
function ExpandString(s){
	var re = new RegExp("%","g")
	for (i = arguments.length-1; i > 0; i--){
		s2 = "%" + i;
		if (s.indexOf(s2) > -1){
			re.compile(s2,"g")
			s = s.replace(re, arguments[i]);
		}
	}
	return s
}
function FindAPI(win) {
	while ((win.API == null) && (win.parent != null) && (win.parent != win)) {
		g_nFindAPITries ++;
		if (g_nFindAPITries > 500) {
			AlertUserOfAPIError(g_strAPITooDeep);
			return null;
		}
		win = win.parent;
	}
	return win.API;
}
function APIOK() {
	return ((typeof(g_oAPI)!= "undefined") && (g_oAPI != null))
}
function SCOInitialize() {
	var err = true;
	if (!g_bInitDone) {
		if ((window.parent) && (window.parent != window)){
			g_oAPI = FindAPI(window.parent)
		}
		if ((g_oAPI == null) && (window.opener != null))	{
			g_oAPI = FindAPI(window.opener)
		}
		if (!APIOK()) {
			AlertUserOfAPIError(g_strAPINotFound);
			err = false
		} else {
			err = g_oAPI.LMSInitialize("");
			if (err == "true") {
				initializeFlash();
			} else {
				AlertUserOfAPIError(g_strAPIInitFailed)
			}
		}
		startFlash();
	}
	g_bInitDone = true;
	return (err)
}

function initializeFlash() {
	var oFlash = getFlashMovieObject("qcm");

	// instruction ineffective si aucun API SCORM n'est disponible
	if (!APIOK()) return;
	
	// Envoie le nom de l'?tudiant
	oFlash.SetVariable("studentName", g_oAPI.LMSGetValue("cmi.core.student_name")+"");

	// Envoie le statut
	oFlash.SetVariable("lessonStatus", g_oAPI.LMSGetValue("cmi.core.lesson_status")+"");
	
	// Envoie l'index de la page lors de la derni?re session
	oFlash.SetVariable("lessonLocation", g_oAPI.LMSGetValue("cmi.core.lesson_location")+"");
	
	// Envoie les donn?es enregistr?es lors de la derni?re session
	oFlash.SetVariable("suspendData", g_oAPI.LMSGetValue("cmi.suspend_data")+"");
}

function startFlash() {
	var oFlash = getFlashMovieObject("qcm");
	// Donne le signal ? flash pour commencer
	oFlash.SetVariable("isFlashInit", "true");
}

function SCOFinish() {
	if ((APIOK()) && (g_bFinishDone == false)) {
		g_bFinishDone = (g_oAPI.LMSFinish("") == "true");
	}
	return (g_bFinishDone);
}


// Traiter les messages fscommand depuis une animation Flash
function qcm_DoFSCommand(command, args){
	var err = true;
	var oFlash = getFlashMovieObject("qcm");

	// instruction ineffective si aucun API SCORM n'est disponible
	if (!APIOK()) return;
	switch (command){
		case "CMICommit":
			err = g_oAPI.LMSCommit("");
			break;
		case "CMIFinish":
			// Trait?e automatiquement par le page, mais l'animation peut l'appeler avant.
			err = SCOFinish(); 
			break;
		case "CMISetLessonStatus":
			err = g_oAPI.LMSSetValue("cmi.core.lesson_status", args);
			break;
		case "CMISetLocation":
			err = g_oAPI.LMSSetValue("cmi.core.lesson_location", args);
			break;
		case "CMISetTime":
			err = g_oAPI.LMSSetValue("cmi.core.session_time", args);
			break;
		case "CMISetScore":
			err = g_oAPI.LMSSetValue("cmi.core.score.raw", args);
			break;
		case "CMISetSuspendData":
			err = g_oAPI.LMSSetValue("cmi.suspend_data", args);
			break;
		
		case "CMI_sendInteraction":
			var index = g_oAPI.LMSGetValue("cmi.interactions._count");
			var arrKeys = new Array("id", "time", "type", "correct_responses.0.pattern", "weighting", "student_response", "result", "latency");
			var arrDatas = args.split(";"); 
			var i = -1;
			while (++i<arrDatas.length){
				var sInfo = arrDatas[i];
				if ((sInfo == null) || (sInfo == "")) continue
				err = g_oAPI.LMSSetValue("cmi.interactions." + index + "." + arrKeys[i], sInfo)
			}
			break;
	}

	// Fin de traduction et de traitement de la commande
	// traiter les erreurs d?tect?es, les renvois d'erreur LMS par exemple
	if ((g_bShowApiErrors) && (err != true) ) {
		AlertUserOfAPIError(ExpandString(g_strFSAPIError, err, command, args))
	}
	return err
}

function getFlashMovieObject(movieName) {
	if (navigator.appName.indexOf("Microsoft Internet")==-1) {
		if (document.embeds && document.embeds[movieName])
			return document.embeds[movieName]; 
	}
	else {
		return document.getElementById(movieName);
	} 
	if (window.document[movieName]) {
		return window.document[movieName];
	}
	
}