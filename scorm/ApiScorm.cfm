<script type="text/javascript">
        var chaine_data = "";
        var init_total_time = "<CFOUTPUT> #url.sco_total_time#</CFOUTPUT>";
        var id_util = '';
        var scormid = '';
        <cfset sco_interactions_children = "id,time,type,correct_responses,weighting,student_response,result,latency,correct_response_text,student_response_text">;
        <cfset sco_children = "student_id,student_name,lesson_location,credit,lesson_status,lesson_mode,entry,score,total_time,exit,session_time">;
        <cfset sco_objectives_children = "id,score.raw,score.min,score.max,score.scaled,status,completion_status,success_status">;
        // ====================================================
        // API Class Constructor
        var debug_ = false;
        function APIClass() {

                //SCORM 1.2

                // Execution State
                this.LMSInitialize = LMSInitialize;
                this.LMSFinish = LMSFinish;

                // Data Transfer
                this.LMSGetValue = LMSGetValue;
                this.LMSSetValue = LMSSetValue;
                this.LMSCommit = LMSCommit;

                // State Management
                this.LMSGetLastError = LMSGetLastError;
                this.LMSGetErrorString = LMSGetErrorString;
                this.LMSGetDiagnostic = LMSGetDiagnostic;

                // Private
                this.APIError = APIError;

                // SCORM 2004

                // Execution
                this.Initialize = LMSInitialize;
                this.Terminate = LMSFinish;

                // Transfert des données
                this.GetValue = LMSGetValue;
                this.SetValue = LMSSetValue;
                this.Commit = LMSCommit;

                // Gestion des erreurs
                this.GetLastError = LMSGetLastError;
                this.GetErrorString = LMSGetErrorString;
                this.GetDiagnostic = LMSGetDiagnostic;

        }


        // ====================================================
        // Execution des fonctions de l'API

        // Initialisation
        // According to SCORM 1.2 reference :
        //    - arg must be "" (empty string)
        //    - return value : "true" or "false"
        function LMSInitialize(arg) {
                if(debug_) alert("initialize");
                if ( arg!="" ) {
                        this.APIError("201");
                        return "false";
                }
                this.APIError("0");
                APIInitialized = true;

                if ( this.LMSGetValue("cmi.core.lesson_status") == "not_started" ) {
                        this.LMSSetValue("cmi.core.lesson_status","started");
                }
                return "true";
        }
        // Finish
        // According to SCORM 1.2 reference
        //    - arg must be "" (empty string)
        //    - return value : "true" or "false"
        function LMSFinish(arg) {
                if(debug_) alert("LMSfinish");
                if ( APIInitialized ) {
                        if ( arg!="" ) {
                                this.APIError("201");
                                return "false";
                        }
                        this.APIError("0");

                        setTimeout("do_commit()",1000);

                        APIInitialized = false; //
                        return "true";
                } else {
                        this.APIError("301");   // not initialized
                        return "false";
                }
        }


        // ====================================================
        // Data Transfer
        //
        var VObjectif = "";
        var jj = "";
        var jjob = "";
        var chaine_inter= "";
        var chaine_objectif= "";
        var chaine_q= "";
        var suite = "" ;
        var suite1 = "";
        var pointeur = 0;
        var pointeur2 = 0;
        var ajout = "";
        var chaine_data = "";
        var debut = "";
        var count_objectives = 0;
        var count = "";
        var valeurObj = new Array() ;
        var cetobj = new Array() ;
        var compObj = 0;
        valeur = new Array() ;
        function LMSGetValue(ele) {
                if(debug_) alert("LMSGetValue : \n" + ele);
                if ( APIInitialized ){
                        tabobj = ele.split('.');
                        if ( ele.substr(0,14) == 'cmi.objectives' && ele != 'cmi.objectives._count'){
                              jjob = tabobj[2];
                              suite = ele.search(/id/);
                              if (suite != -1){
                                 cetobj[1] = "cmi.objectives."+jjob+".id";
                                 if (valeur[ele] != undefined){
                                    valeurObj[1] = valeur[ele];
                                 }
                                 suite = -1;
                               }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/raw/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[2] ="cmi.objectives."+jjob+".score.raw";
                                 if (valeur[ele] != undefined){
                                    valeurObj[2] = valeur[ele];
                                 }
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/max/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[3] ="cmi.objectives."+jjob+".score.max";
                                 if (valeur[ele] != undefined){
                                    valeurObj[3] = valeur[ele];
                                 }
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/min/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[4] ="cmi.objectives."+jjob+".score.min";
                                 if (valeur[ele] != undefined){
                                    valeurObj[4] = valeur[ele];
                                 }
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/scaled/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[5] ="cmi.objectives."+jjob+".score.scaled";
                                 if (valeur[ele] != undefined){
                                    valeurObj[5] = valeur[ele];
                                 }
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/status/);
                            if (suite != -1){
                                cetobj[6] ="cmi.objectives."+jjob+".status";
                                 if (valeur[ele] != undefined){
                                    valeurObj[6] = valeur[ele];
                                 }
                                 suite = -1;
                            }
                            suite = ele.search(/completion_status/);
                            if (suite != -1){
                                cetobj[7] ="cmi.objectives."+jjob+".completion_status";
                                 if (valeur[ele] != undefined){
                                    valeurObj[7] = valeur[ele];
                                 }
                                suite = -1;
                            }
                            suite = ele.search(/success_status/);
                            if (suite != -1){
                                cetobj[8] ="cmi.objectives."+jjob+".success_status";
                                 if (valeur[ele] != undefined){
                                    valeurObj[8] = valeur[ele];
                                 }
                                suite = -1;
                            }
                            switch (ele){
                                  case cetobj[1] : APIError("0"); return valeurObj[1];break;
                                  case cetobj[2] : APIError("0"); return valeurObj[2];break;
                                  case cetobj[3] : APIError("0"); return valeurObj[3];break;
                                  case cetobj[4] : APIError("0"); return valeurObj[4];break;
                                  case cetobj[5] : APIError("0"); return valeurObj[5];break;
                                  case cetobj[6] : APIError("0"); return valeurObj[6];break;
                                  case cetobj[7] : APIError("0"); return valeurObj[7];break;
                                  case cetobj[8] : APIError("0"); return valeurObj[8];break;
                            }
                       }else{
                          var i = array_indexOf(elements,ele);
                          if (i != -1 )  // ele is implemented -> handle it
                          {
                           switch (ele)
                           {
                                case 'cmi.core._children' : APIError("0"); return values[i]; break;
                                case 'cmi.core.student_id' : APIError("0"); return values[i]; break;
                                case 'cmi.core.student_name' : APIError("0"); return values[i]; break;
                                case 'cmi.core.lesson_location' : APIError("0"); return values[i]; break;
                                case 'cmi.core.credit' : APIError("0"); return values[i]; break;
                                case 'cmi.core.lesson_status' : APIError("0"); return values[i]; break;
                                case 'cmi.core.lesson_mode' : APIError("0"); return values[i]; break;

                                //-----------------------------------
                                //Pour accorder avec Scorm 1.3 ou 2004
                                //-----------------------------------

                                case 'cmi.completion_status' : APIError("0"); ele = 'cmi.core.lesson_status'; return values[i]; break;
                                case 'cmi.success_status' : APIError("0"); ele = 'cmi.core.lesson_status'; return values[i]; break;

                                //-----------------------------------

                                case 'cmi.core.entry' :  APIError("0"); return values[i]; break;
                                case 'cmi.core.score._children' : APIError("0"); return values[i]; break;
                                case 'cmi.interactions._children' : APIError("0"); return values[i]; break;
                                case 'cmi.objectives._children' : APIError("0"); return values[i]; break;
                                case 'cmi.core.score.raw' : APIError("0"); return values[i]; break;
                                case 'cmi.core.score.min' : APIError("0"); return values[i]; break;
                                case 'cmi.core.score.max' : APIError("0"); return values[i]; break;
                                case 'cmi.core.total_time' : APIError("0"); return values[i]; break;
                                case 'cmi.core.exit' : APIError("404"); return ""; break;// write only
                                case 'cmi.exit' : APIError("404");ele = 'cmi.core.exit'; return values[i]; return ""; break;// write only
                                case 'cmi.core.session_time' : APIError("404"); return ""; break;// write only
                                case 'cmi.suspend_data' : APIError("0"); return values[i]; break;
                                case 'cmi.comments' : APIError("0"); return values[i]; break;
                                case 'cmi.comments_from_lms' : APIError("0"); return values[i]; break;
                                case 'cmi.launch_data' : APIError("0"); return values[i]; break;
                                case 'cmi.interactions._count' : APIError("0"); return count++; break;
                                case 'cmi.objectives._count' : APIError("0"); return count_objectives++; break;
                                //-----------------------------------

                                //Pour intégrer les données de tracking des QCM
                                //-----------------------------------
                              }
                             }else{
                                 // Erreur d'implementation au cas ou un élément n'est pas déclaré
                                 APIError("401");
                                 return "";
                             }
                           }
                      }else{
                        // Erreur dans l'initialisation
                        this.APIError("301");
                        return "false";
                }
        }
        var new_ele = new Array();
        function LMSSetValue(ele,val) {
                if(debug_)alert ("LMSSetValue : \n" + ele +" "+ val);

                if ( APIInitialized ){
                      tab = ele.split('.');
                      if ( ele.substr(0,16) == 'cmi.interactions'){
                            jj = tab[2];
                            suite = ele.search(/id/);
                            if (suite != -1){
                                new_ele[1] ="cmi.interactions."+jj+".id";
                                suite = -1;
                                pointeur++;
                            }
                            suite = ele.search(/time/);
                            if (suite != -1){
                                new_ele[2] ="cmi.interactions."+jj+".time";
                                suite = -1;
                            }
                            suite = ele.search(/type/);
                            if (suite != -1){
                                new_ele[3] ="cmi.interactions."+jj+".type";
                                suite = -1;
                            }
                            suite = ele.search(/correct_responses.+/);
                            suite1 = ele.search(/.pattern/);
                            if (suite != -1 && suite1 != -1){
                                new_ele[5] ="cmi.interactions."+jj+".correct_responses.0.pattern";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/correct_response_text/);
                            if (suite != -1){
                                new_ele[5] ="cmi.interactions."+jj+".correct_response_text";
                                suite = -1;
                            }
                            suite = ele.search(/correct_responses.+/);
                            suite1 = ele.search(/_count/);
                            if (suite != -1 && suite1 != -1){
                                new_ele[4] ="cmi.interactions."+jj+".correct_responses._count";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/weighting/);
                            if (suite != -1){
                                new_ele[6] ="cmi.interactions."+jj+".weighting";
                                suite = -1;
                            }
                            suite = ele.search(/student_response/);
                            if (suite != -1){
                                new_ele[7] ="cmi.interactions."+jj+".student_response";
                                suite = -1;
                            }
                            suite = ele.search(/student_response_text/);
                            if (suite != -1){
                                new_ele[7] ="cmi.interactions."+jj+".student_response_text";
                                suite = -1;
                            }
                            suite = ele.search(/result/);
                            if (suite != -1){
                                new_ele[8] = "cmi.interactions."+jj+".result";
                                suite = -1;
                            }
                            suite = ele.search(/latency/);
                            if (suite != -1){
                                new_ele[9] = "cmi.interactions."+jj+".latency";
                                suite = -1;
                            }
                            suite = ele.search(/objectives./);
                            if (suite != -1){
                                new_ele[10] = "cmi.interactions."+jj+".objectives.0.id";
                                suite = -1;
                            }
                            if (chaine_inter != "")
                                ajout = "*";
                            else
                                ajout = "";
                           switch (ele){
                                  case new_ele[1] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[2] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[3] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[4] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[5] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[6] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[7] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[8] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[9] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case new_ele[10] : chaine_q = ajout +ele+"="+val;chaine_inter +=  chaine_q;return ("true");break;
                                  case 'cmi.interactions._count' : chaine_q = ajout +ele+"="+count;chaine_inter +=  chaine_q; return "true"; break;
                            }

                       }else if ( ele.substr(0,14) == 'cmi.objectives'){

                          tabobj = ele.split('.');
                          jjo = tabobj[2];
                          suite = ele.search(/id/);
                          if (jjo != '_count'){
                            if (suite != -1 ){
                                cetobj[1] ="cmi.objectives."+jjo+".id";
                                suite = -1;
                                pointeur2++;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/raw/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[2] ="cmi.objectives."+jjo+".score.raw";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/max/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[3] ="cmi.objectives."+jjo+".score.max";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/min/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[4] ="cmi.objectives."+jjo+".score.min";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/score/);
                            suite1 = ele.search(/scaled/);
                            if (suite != -1 && suite1 != -1){
                                cetobj[5] ="cmi.objectives."+jjo+".score.scaled";
                                suite = -1;
                                suite1 = -1;
                            }
                            suite = ele.search(/status/);
                            if (suite != -1){
                                cetobj[6] ="cmi.objectives."+jjo+".status";
                                suite = -1;
                            }
                            suite = ele.search(/completion_status/);
                            if (suite != -1){
                                cetobj[7] ="cmi.objectives."+jjo+".completion_status";
                                suite = -1;
                            }
                            suite = ele.search(/success_status/);
                            if (suite != -1){
                                cetobj[8] ="cmi.objectives."+jjo+".success_status";
                                suite = -1;
                            }
                            if (chaine_objectif != "")
                                ajout = "*";
                            else
                                ajout = "";
                         }// fin jjo != count
                            switch (ele){
                                  case cetobj[1] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[2] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[3] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[4] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[5] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[6] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[7] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case cetobj[8] : chaine_q = ajout +ele+"="+val;valeur[ele]=val;chaine_objectif +=  chaine_q;return ("true");break;
                                  case 'cmi.objectives._count' : chaine_q = ajout +ele+"="+count_objectives;chaine_objectif +=  chaine_q; return "true"; break;
                            }
                       }else{
                          var i = array_indexOf(elements,ele);
                          if (i != -1 ){  // ele is implemented -> handle it
                             switch (ele){

                                case 'cmi.core._children' : APIError("402");return "false"; break; // invalid set value, element is a keyword
                                case 'cmi.core.student_id' : APIError("403");return "false";break; // read only
                                case 'cmi.core.student_name' :APIError("403"); return "false"; break;// read only
                                case 'cmi.core.lesson_location' :
                                     if ( val.length > 255 ){
                                        APIError("405");return "false";
                                     }
                                     values[i] = val;APIError("0");return "true";break;
                                case 'cmi.core.lesson_status' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                           && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                           && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" ){
                                         APIError("405");
                                         return "false";
                                      }

                                      values[i] = val;APIError("0");return "true";break;
                                case 'cmi.core.lesson_mode' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "BROWSE" && upperCaseVal != "NORMAL"
                                           && upperCaseVal != "REVIEW" ){
                                         APIError("405");
                                         return "false";
                                      }
                                      values[i] = val; APIError("0"); return "true"; break;

                                //-------------------------------
                                // Concordance avec les éléments de SCORM 2004 :
                                // completion_status et success_status sont de nouveaux éléments
                                //-------------------------------

                                case 'cmi.completion_status' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                           && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                           && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      ele = 'cmi.core.lesson_status';
                                      values[4] = val;  // deal with lesson_status element from scorm 1.2 instead
                                      APIError("0");
                                      return "true";
                                      break;

                                case 'cmi.success_status' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "PASSED" && upperCaseVal != "FAILED"
                                           && upperCaseVal != "COMPLETED" && upperCaseVal != "INCOMPLETE"
                                           && upperCaseVal != "BROWSED" && upperCaseVal != "NOT ATTEMPTED" && upperCaseVal != "UNKNOWN" )
                                      {
                                           APIError("405");
                                           return "false";
                                      }

                                      ele = 'cmi.core.lesson_status';
                                      values[4] = val;  // deal with lesson_status element from scorm 1.2 instead
                                      APIError("0");
                                      return "true";
                                      break;

                                //-------------------------------


                                case 'cmi.core.credit' :
                                      APIError("403"); // read only
                                      return "false";
                                      break;
                                case 'cmi.core.entry' :
                                      APIError("403"); // read only
                                      return "false";
                                      break;
                                case 'cmi.core.score._children' :
                                      APIError("402");  // invalid set value, element is a keyword
                                      return "false";
                                      break;
                                case 'cmi.core.score.raw' :
                                      if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.score.scaled' :
                                      if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[20] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.core.score.min' :
                                      if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.core.score.max' :
                                      if( isNaN(parseInt(val)) || (val < 0) || (val > 100) )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.core.total_time' :
                                      APIError("403"); //read only
                                      return "false";
                                      break;
                                case 'cmi.core.exit' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
                                           && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.exit' :
                                      var upperCaseVal = val.toUpperCase();
                                      if ( upperCaseVal != "TIME-OUT" && upperCaseVal != "SUSPEND"
                                           && upperCaseVal != "LOGOUT" && upperCaseVal != "" )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                case 'cmi.core.session_time' :
                                      // regexp to check format
                                      // hhhh:mm:ss.ss
                                      var re = /^[0-9]{2,4}:[0-9]{2}:[0-9]{2}(.)?[0-9]?[0-9]?$/;
                                      if ( !re.test(val) )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      // check that minuts and second are 0 <= x < 60
                                      var splitted_val = val.split(":");
                                      if( splitted_val[1] < 0 || splitted_val[1] >= 60 || splitted_val[2] < 0 || splitted_val[2] >= 60 )
                                      {
                                           APIError("405");
                                           return "false";
                                      }
                                      values[i] = val;
                                      APIError("0");
                                      return "true";
                                      break;
                                   case 'cmi.suspend_data' : values[i] = val; APIError("0"); return "true";  break;
                                   case 'cmi.comments' : values[i] = val; APIError("0"); return "true";  break;
                                   case 'cmi.comments_from_lms' : values[i] = val; APIError("0"); return "true";  break;
                                   case 'cmi.launch_data' : APIError("403"); return "false";  break;//read only
                          }

                       }else{ // ele not implemented
                            // not implemented error
                            APIError("401");
                            return "";
                       }
                   }// else interactions
                }else{
                   // not initialized error
                   this.APIError("301");
                   return "false";
                }
        }

        function LMSCommit(arg)
        {
               if(debug_) alert("LMScommit");
               if ( APIInitialized ) {
                        if ( arg!="" ) {
                                this.APIError("201");
                                return "false";
                        } else {
                                this.APIError("0");
                               //setTimeout('do_commit();',1000);
                               do_commit();
                                return "true";
                        }
                } else {
                        this.APIError("301");
                        return "false";
                }
        }


        // ====================================================
        // Gestion des erreurs
        //
        function LMSGetLastError() {
                if(debug_) alert ("LMSGetLastError : " + APILastError);

                return APILastError;
        }

        function LMSGetErrorString(num) {
                if(debug_) alert ("LMSGetErrorString(" + num +") = " + errCodes[num] );

                return errCodes[num];

        }

        function LMSGetDiagnostic(num) {
                if(debug_) alert ("LMSGetDiagnostic("+num+") = " + errDiagn[num] );

                if ( num=="" ) num = APILastError;
                return errDiagn[num];
        }


        // ====================================================
        // Private
        //
        function APIError(num) {
                APILastError = num;
        }

       // ====================================================
        // Liste des erreur de codes et de diagnostics
        //
        var errCodes = new Array();
        errCodes["0"]   = "Aucune erreur";
        errCodes["101"] = "General Exception";
        errCodes["102"] = "Le serveur est Out";
        errCodes["201"] = "Argument non valable";
        errCodes["202"] = "Cet element ne peut pas avoir d\'enfants";
        errCodes["203"] = "Cet élement n\'est pas un tableau. Ne supporte pas l\'argument: count";
        errCodes["301"] = "Non initialisé";
        errCodes["401"] = "Erreur non implémentée";
        errCodes["402"] = "Valeur attribuée non valable, cet élement est une clé";
        errCodes["403"] = "Element valable en lecture seule";
        errCodes["404"] = "Element valable en écriture seule";
        errCodes["405"] = "Type de donnée incorrecte";

        var errDiagn = new Array();
        errDiagn["0"]   = "Aucune erreur";
        errDiagn["101"] = "Possible erreur du serveur. Contactez  votre administrateur système";
        errDiagn["102"] = "Le serveur a un problème et ne peut répondre à cette requête. Essayez de nouveau";
        errDiagn["201"] = "Ce cours contient un mauvais appel à une fonction. Contactez votre fournisseur ou votre administrateur système";
        errDiagn["202"] = "The course made an incorrect data request. Contactez votre fournisseur ou votre administrateur système";
        errDiagn["203"] = "The course made an incorrect data request. Contactez votre fournisseur ou votre administrateur système";
        errDiagn["301"] = "Le système n\'a pas été correctement initialisé. Contactez  votre administrateur système";
        errDiagn["401"] = "Le cours a appelé des données non supportées par les questions.";
        errDiagn["402"] = "Le cours a émis des données non supportées lors de la sauvegarde. Contactez votre fournisseur ou votre administrateur système";
        errDiagn["403"] = "Le cours a tenté d\'écrire là où il y a un read only. Contactez votre fournisseur";
        errDiagn["404"] = "Le cours a tenté de lire là où il y a un xrite only. Contactez votre fournisseur";
        errDiagn["405"] = "Le cours a généré un type de donnée incorrect. Contactez votre fournisseur";

        // ====================================================
        // CMI Elements and Values
        //
        var elements = new Array(); var values = new Array();
        elements[0]  = "cmi.core._children"; values[0]  = "<CFOUTPUT> #sco_children#</CFOUTPUT>";
        elements[1]  = "cmi.core.student_id"; values[1]  = "<CFOUTPUT> #sco_student_id#</CFOUTPUT>";
        elements[2]  = "cmi.core.student_name"; values[2]  = "<CFOUTPUT> #sco_student_name#</CFOUTPUT>";
        elements[3]  = "cmi.core.lesson_location"; values[3]  = "<CFOUTPUT> #sco_lesson_location#</CFOUTPUT>";
        elements[4]  = "cmi.core.lesson_status"; values[4]  = "<CFOUTPUT> #sco_lesson_status#</CFOUTPUT>";
        elements[5]  = "cmi.core.credit"; values[5]  = "<CFOUTPUT> #sco_credit#</CFOUTPUT>";
        elements[6]  = "cmi.core.entry"; values[6]  = "<CFOUTPUT> #sco_entry#</CFOUTPUT>";
        elements[7]  = "cmi.core.score._children"; values[7]  = "<CFOUTPUT> #sco_score_children#</CFOUTPUT>";
        elements[8]  = "cmi.core.score.raw"; values[8]  = "<CFOUTPUT> #sco_raw#</CFOUTPUT>";
        elements[9]  = "cmi.core.total_time"; values[9]  = "<CFOUTPUT> #sco_total_time#</CFOUTPUT>";
        elements[10] = "cmi.core.exit"; values[10] = "<CFOUTPUT> #sco_exit#</CFOUTPUT>";
        elements[11] = "cmi.core.session_time"; values[11] = "<CFOUTPUT> #sco_session_time#</CFOUTPUT>";
        elements[12] = "cmi.suspend_data"; values[12] = "<CFOUTPUT> #sco_suspend_data#</CFOUTPUT>";
        elements[13] = "cmi.launch_data"; values[13] = "<CFOUTPUT> #sco_launch_data#</CFOUTPUT>";
        elements[14] = "cmi.core.score.min"; values[14] = "<CFOUTPUT> #sco_scoreMin#</CFOUTPUT>";
        elements[15] = "cmi.core.score.max"; values[15] = "<CFOUTPUT> #sco_scoreMax#</CFOUTPUT>";
        elements[16] = "cmi.completion_status"; values[16] = "<CFOUTPUT> #sco_lesson_status#</CFOUTPUT>";//2004
        elements[17] = "cmi.success_status"; values[17] = "<CFOUTPUT> #sco_lesson_status#</CFOUTPUT>";//2004
        elements[18] = "cmi.core.lesson_mode"; values[18] = "<CFOUTPUT> #sco_lesson_mode#</CFOUTPUT>";
        elements[19] = "cmi.interactions._count";values[19] = "";
        elements[20] = "cmi.score.scaled";values[20] = "<CFOUTPUT> #sco_score_scaled#</CFOUTPUT>";
        elements[21] = "cmi.exit";values[21] = "<CFOUTPUT> #sco_exit2#</CFOUTPUT>";
        elements[22] = "cmi.objectives._children";values[22]  = "<CFOUTPUT> #sco_objectives_children#</CFOUTPUT>";
        elements[23] = "cmi.objectives._count";values[23] = "";
        elements[24] = "cmi.comments";values[24] = "<CFOUTPUT> #sco_comments#</CFOUTPUT>";
        elements[25] = "cmi.comments_from_lms";values[25] = "<CFOUTPUT> #sco_comments_from_lms#</CFOUTPUT>";
        elements[26] = "cmi.interactions._children";values[26] = "<CFOUTPUT> #sco_interactions_children#</CFOUTPUT>";
       // ====================================================
        //
        //
        function do_commit()
        {
            chaine_data="&lesson_location="+values[3]+"&lesson_status="+values[4]+"&lesson_mode="+values[18]+"&credit="+values[5]+"&entry="+values[6]+"&raw="+values[8]+"&total_time="+values[9]+"&session_time="+values[11]+"&suspend_data="+values[12]+"&scoreMin="+values[14]+ "&scoreMax="+values[15]+"&comments="+values[24]+"&comments_from_lms="+values[25]+"&inter_nb="+pointeur+"&chaine_inter="+chaine_inter+"&objectives_nb="+pointeur2+"&chaine_objectives="+chaine_objectif;

            parent.parent.parent.idsFrame.location.href='<CFOUTPUT>#domaine#</CFOUTPUT>/scorm/update_scoX.php?id_util=<CFOUTPUT>#id_user#</CFOUTPUT>&scormid=<CFOUTPUT>#scormid#</CFOUTPUT>&id_seq=<CFOUTPUT>#id_seq#</CFOUTPUT>&chaine_data='+chaine_data;
        }

        function array_indexOf(arr,val) {
                for ( var i=0; i<arr.length; i++ ) {
                        if ( arr[i] == val ) {
                                return i;
                        }
                }
                return -1;
        }

        // ====================================================
        // Final Setup
        //


        APIInitialized = false;
        APILastError = "301";

        // Declare Scorm API object for 1.2

        API = new APIClass();
        api = new APIClass();

        // Declare Scorm API object for 2004

        API_1484_11 = new APIClass();
        api_1484_11 = new APIClass();



</script>
