<!--
 /*---------------------------------------------------------------------------------------
QplayerScripts - javascript interfacing e-teach Q-player(TM) and LMS standard SCORM 1.2																																															 
version 1.0
SCORM 1.2 (www.adlnet.com) conformant
www-e-teach.ch

e-teach sàrl
Thierry Yersin
Lausanne - CH 

10.11.2004
-----------------------------------------------------------------------------------------
Information importante : 
Le nom de l'objet flash : Qplayer doit être inséré dans les balise object (ID) et embed(name)
nécessite le script dialogueAPIScorm.js pour la communication avec le LMS
-----------------------------------------------------------------------------------------
Ressources & thanx to :
http://www.mustardlab.com/developer/flash/jscommunication/
http://www.dithered.com/javascript/browser_detect/index.html
-----------------------------------------------------------------------------------------
Supporting Browsers:
PC:
IE 5.5 and higher
Netscape 7 and higher
Moz/Firebird all
Mac OSX:
Safari 1.2
Netscape 7 and higher
Moz/Firebird
----------------------------------------------------------------------------------------*/

//var setting use of SCORM for the web page suppotting Qplayer. true= SCORM communication / false= no SCORM communication
//some function are usefull for Qplayer(TM)
//*******************


var enviroSCORM=true;


//*******************
var ua = navigator.userAgent.toLowerCase(); 
var is_pc_ie = ( (ua.indexOf('msie') != -1 ) && ( ua.indexOf('win') != -1 ) && ( ua.indexOf('opera') == -1 ) && ( ua.indexOf('webtv') == -1 ) );
var is_pc_mozilla = ( (ua.indexOf('mozilla') != -1 ) && ( ua.indexOf('win') != -1 ) && ( ua.indexOf('opera') == -1 ) && ( ua.indexOf('webtv') == -1 ) && ( ua.indexOf('gecko') !== -1 ));

var score;
var temps="00:00:00";
var scoreOld;
var scoreMax;
var scoreMin;
var API;
var URLref;
var XMLpath;
var SCORMinit;
var quizAttempts;


//setting debug mode to true will trace debug information
var debug=false;


//display info if debug is set to false
if(debug){alert(ua);}
if(debug){alert("IE on PC ? "+is_pc_ie);}
if(debug){alert("MOZILLA on PC ? "+is_pc_mozilla);}

/* -------------------------------------------------------------------
function setFlashVariables(movieid, flashquery)
movieid: id of object tag, name of movieid passed in through FlashVars
flashquery: querystring of values to set. example( var1=foo&var2=bar )
method : depending on OS and browser :: special flash gateway.swf required for flash javascript com on MAC OS
----------------------------------------------------------------------*/
function setFlashVariables(movieid, flashquery){
	if(debug){alert(movieid);}
	var i,values;
	if(is_pc_ie || is_pc_mozilla){
		var chunk = flashquery.split("&");
		for(i in chunk){
			values = chunk[i].split("=");
			document[movieid].SetVariable(values[0],values[1]);
		}
	}else{
		var divcontainer = "flash_setvariables_"+movieid;
		if(!document.getElementById(divcontainer)){
			var divholder = document.createElement("div");
			divholder.id = divcontainer;
			document.body.appendChild(divholder);
		}
		if(debug){alert("gatewayStart");}
		document.getElementById(divcontainer).innerHTML = ""; 
		//attention au chemin d'accès du flash gateway.swf !!
		var divinfo = "<embed src='../../Qplayer_scripts/gateway.swf' FlashVars='lc="+movieid+"&fq="+escape(flashquery)+"' width='0' height='0' type='application/x-shockwave-flash'></embed>";
		document.getElementById(divcontainer).innerHTML = divinfo;
		if(debug){alert("gatewayFinish");}
	}
}
 

/*----------------------------------------------------------------
initialistion of SCORM communication requiered dialogueAPIScorm.js
standart use : on event <body onload=''>
----------------------------------------------------------------*/
  function initLMS() {
	 if(enviroSCORM){
	    if(debug){alert("init");}
	    SCORMinit=doLMSInitialize();
	    SCORMinit=eval(SCORMinit);
	    if(debug){alert("result SCORM INIT : "+ SCORMinit);}
	 	//query for old values (score/suspend_data)
	 	getValue();
	  }
  }  
/*----------------------------------------------------------------
interuption of SCORM communication requiered dialogueAPIScorm.js
standart use : on event <body onunload=''>
-----------------------------------------------------------------*/
  function finishLMS() {
    if(SCORMinit){
    	//query for Qplayer variables (score/time/quiz attempts/...)
		getQplayerVar();
		if(score=="" || score==null || score==" " || score=="undefined"){
		   	if(debug){alert("do nothing");}
		   	doLMSFinish();
		   	}
	    else{
		   	if(debug){alert("score: " + score + " scoreOld: "+ scoreOld);}
			score=parseInt(score);
			scoreOld=parseInt(scoreOld);
			scoreMax=parseInt(scoreMax);
			scoreMin=parseInt(scoreMin);
			
			doLMSSetValue("cmi.core.score.raw",score);
		    doLMSSetValue("cmi.core.session_time",temps);		   
			   
			if(score>scoreMax){
				if(debug){alert("stockScoreMax "+"score :"+score+"\rscoreMax :"+scoreMax);}
				doLMSSetValue("cmi.core.score.max",score);
			}
			if (score<=scoreMin){
			  	if(debug){alert("stockScoreMin "+"score :"+score+"\rscoreMin :"+scoreMin);}
			  	doLMSSetValue("cmi.core.score.min",score);
			}
			if(debug){alert("quizAttempts "+quizAttempts);}
			if(quizAttempts!="" && quizAttempts!=null && quizAttempts!=0){
				if(debug){alert("store quiz attempts");}
				doLMSSetValue("cmi.suspend_data","QA"+quizAttempts+";");
			}
		 	 doLMSFinish();
		  	if(debug){alert("LMS->FINISH");}
		}
	}
  }

  //fscommand communication between flash and javascript
  //not actually used -> non conformant with MAC OS         
  function Qplayer_DoFSCommand(command, args) {
  	//alert("command");
	if (command == "closeWindow"){
		window.close();
	}
  }
/*--------------------------------------------------------------------------------------------------------------
getting old score in database with SCORM communication and sending them to flash : requiered dialogueAPIScorm.js
--------------------------------------------------------------------------------------------------------------*/  
 	function getValue(){
		if(SCORMinit){
			scoreOld=doLMSGetValue("cmi.core.score.raw");
			scoreMax=doLMSGetValue("cmi.core.score.max");
			scoreMin=doLMSGetValue("cmi.core.score.min");
	  		var suspend_data=doLMSGetValue("cmi.suspend_data");
	  		if(suspend_data==""){
	  			quizAttempts=0;
	  		}else{
	  			//ATTENTION " DIGIT QA10; à verifier en prod GAB
	  			var delimiter1=suspend_data.indexOf("QA");
	  			var delimiter2=suspend_data.length;
	  			suspend_data=suspend_data.substr(delimiter1+2,delimiter2);
	  			var delimiter3=suspend_data.indexOf(";");
	  			quizAttempts=suspend_data.substr(0,delimiter3);
	  			
	  			if(debug){alert("quizAttempts après traitement suspend_data -> "+ quizAttempts);}
	  		}
	  		if(debug){alert("quizAttempts -> "+quizAttempts);}
	  		if(scoreOld==""){
	  			scoreOld="undefined";
	  		}
	  		if(scoreMax==""){
		   		scoreMax=0;
			}
	  		if(scoreMin==""){
		   		scoreMin=100;
			}
			
	  		setFlashVariables('Qplayer','scoreOld='+scoreOld+'&SCORMinit='+SCORMinit+'&quizAttempts='+quizAttempts);
			if(debug){alert("dernierScore: "+scoreOld+"\r"+"scoreMax: "+scoreMax+"\r"+"scoreMin: "+scoreMin);}
  		}else{
  			setFlashVariables('Qplayer','SCORMinit='+SCORMinit);
  		}
  	}
/*--------------------------------------------------------------------------------------------------------------
getting score and session time in flash object
supported by PC IE et MOZILLA 
not supported by MAC OS/BROWSER
--------------------------------------------------------------------------------------------------------------*/ 	
	function getQplayerVar(){
		if(SCORMinit){
			if(is_pc_ie || is_pc_mozilla){
				if(debug){alert("getVAR pour IE/MOZILLA sur PC");}
				score = window.document.Qplayer.GetVariable("skin.navigation.scoreSCORM");
				temps = window.document.Qplayer.GetVariable("temps");
				quizAttempts = window.document.Qplayer.GetVariable("quizAttempts");
				
				if(debug){alert("score: " + score);}
				if(debug){alert("temps: " + temps);}
				if(debug){alert("quizAttempts "+quizAttempts);}
				}
		}
	}
	//appelee par l universel-compatible getURL flash
	function closeWindow(param,param2,param3){
			score=param;
			temps=param2;
			quizAttempts=param3;
			if(debug){alert("closeWindow() score : "+score+" temps :"+temps);}
			alert("Si la fenêtre ne se ferme pas et que vous disposez d'un bouton de validation, \ncliquez sur le bouton valider afin de prendre en compte votre travail"); 
			window.close();
	}
	//get temp variables for unload of HTML page supproting Q-player(TM).
	//called by universal actionscript getURL, conformant with MAC OS
	function setVarTemp(param,param2,param3){
		score=param;
		temps=param2;
		quizAttempts=param3;
		//alert("setVarTemp score:"+score+" temps:"+temps+" quizAttempts:"+quizAttempts);
		}
-->
