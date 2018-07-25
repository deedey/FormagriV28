<?php 

// Avec la date du lundi, il est plus simple apres de savoir quels sont les rdv pris dans la semaine

//On passe en parametre le mois de la date courante

function Date_Lundi ($date,$firstday,$bissextile)
{
    $ch_date = explode ("/",$date);
    switch ($firstday) {
    case 'Monday' :
                     break;
    case 'Tuesday' : switch ($ch_date[1]) {
         //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=1;

                                         } break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2])
                             {

                                         case 1 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=1;
                             }

                              break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12:switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=30;$ch_date[1]--;//$ch_date[0]++;
                                                  break;
                                         default : $ch_date[2]-=1;}
                                  break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=1;}

                             }

                  break;

    case 'Wednesday' :switch ($ch_date[1]) {

          //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=30;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 2 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=2;} break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=2;}



                                  break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=2;}

                                      break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=28;$ch_date[1]--;}

                                                    else {$ch_date[2]=27;$ch_date[1]--; }

                                                    break;

                                         case 2 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=2;}

                               }

                  break;

    case 'Thursday' : switch ($ch_date[1]) {

          //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=29;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 2 : $ch_date[2]=30;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 3 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=3;} break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=3;}

                                      break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=3;}

                                      break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=27;$ch_date[1]--;}

                                                    else {$ch_date[2]=26;$ch_date[1]--; }

                                                    break;

                                         case 2 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                         case 3 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=3;}

                               }

                  break;

    case 'Friday' :    switch ($ch_date[1]) {

          //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=28;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 2 : $ch_date[2]=29;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 3 : $ch_date[2]=30;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 4 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=4;} break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=4;}

                                      break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=27;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=4;}

                                      break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=26;$ch_date[1]--;}

                                                    else {$ch_date[2]=25;$ch_date[1]--; }

                                                    break;

                                         case 2 :   if ($bissextile == 1) {$ch_date[2]=27;$ch_date[1]--;}

                                                    else {$ch_date[2]=26;$ch_date[1]--; }

                                                    break;

                                         case 3 :   if ($bissextile == 1) {$ch_date[2]=28;$ch_date[1]--;}

                                                    else {$ch_date[2]=27;$ch_date[1]--; }

                                                    break;

                                         case 4 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=4;}

                               }

                   break;

    case 'Saturday' :  switch ($ch_date[1]) {

          //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=27;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 2 : $ch_date[2]=28;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 3 : $ch_date[2]=29;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 4 : $ch_date[2]=30;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 5 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=5;} break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=27;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         case 5 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=5;}

                                      break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12:  switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=26;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=27;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 5 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=5;}

                                      break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=25;$ch_date[1]--;}

                                                    else {$ch_date[2]=24;$ch_date[1]--; }

                                                    break;

                                         case 2 :   if ($bissextile == 1) {$ch_date[2]=26;$ch_date[1]--;}

                                                    else {$ch_date[2]=25;$ch_date[1]--; }

                                                    break;

                                         case 3 :   if ($bissextile == 1) {$ch_date[2]=27;$ch_date[1]--;}

                                                    else {$ch_date[2]=26;$ch_date[1]--; }

                                                    break;

                                         case 4 :   if ($bissextile == 1) {$ch_date[2]=28;$ch_date[1]--;}

                                                    else {$ch_date[2]=27;$ch_date[1]--; }

                                                    break;

                                         case 5 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=5;}

                                 }

                  break;

    case 'Sunday' :    switch ($ch_date[1]) {

          //mois suivant des mois de 31 jours

                             case 1 : switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=26;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 2 : $ch_date[2]=27;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 3 : $ch_date[2]=28;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 4 : $ch_date[2]=29;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 5 : $ch_date[2]=30;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         case 6 : $ch_date[2]=31;$ch_date[1]=12;$ch_date[0]--;

                                                  break;

                                         default : $ch_date[2]-=6;} break;

                             case 2 :

                             case 4 :

                             case 6 :

                             case 8 :

                             case 9 :

                             case 11: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=26;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=27;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 5 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         case 6 : $ch_date[2]=31;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=6;}

                                      break;

           //mois suivant des mois de 30 jours

                             case 5 :

                             case 7 :

                             case 10:

                             case 12: switch ($ch_date[2]) {

                                         case 1 : $ch_date[2]=25;$ch_date[1]--;

                                                  break;

                                         case 2 : $ch_date[2]=26;$ch_date[1]--;

                                                  break;

                                         case 3 : $ch_date[2]=27;$ch_date[1]--;

                                                  break;

                                         case 4 : $ch_date[2]=28;$ch_date[1]--;

                                                  break;

                                         case 5 : $ch_date[2]=29;$ch_date[1]--;

                                                  break;

                                         case 6 : $ch_date[2]=30;$ch_date[1]--;

                                                  break;

                                         default : $ch_date[2]-=6;}

                                      break;

           //mois suivant fevrier

                             case 3 : switch ($ch_date[2]) {

                                         case 1 :   if ($bissextile == 1) {$ch_date[2]=24;$ch_date[1]--;}

                                                    else {$ch_date[2]=23;$ch_date[1]--; }

                                                    break;

                                         case 2 :   if ($bissextile == 1) {$ch_date[2]=25;$ch_date[1]--;}

                                                    else {$ch_date[2]=24;$ch_date[1]--; }

                                                    break;

                                         case 3 :   if ($bissextile == 1) {$ch_date[2]=26;$ch_date[1]--;}

                                                    else {$ch_date[2]=25;$ch_date[1]--; }

                                                    break;

                                         case 4 :   if ($bissextile == 1) {$ch_date[2]=27;$ch_date[1]--;}

                                                    else {$ch_date[2]=26;$ch_date[1]--; }

                                                    break;

                                         case 5 :   if ($bissextile == 1) {$ch_date[2]=28;$ch_date[1]--;}

                                                    else {$ch_date[2]=27;$ch_date[1]--; }

                                                    break;

                                         case 6 :   if ($bissextile == 1) {$ch_date[2]=29;$ch_date[1]--;}

                                                    else {$ch_date[2]=28;$ch_date[1]--; }

                                                    break;

                                      default : $ch_date[2]-=6;}

                              }

                  break;

       }



$date = "$ch_date[0]/$ch_date[1]/$ch_date[2]";
return $date;

}











function ParcoursMois ($sem_der,$sem_pro,$date,$Prem,$rech,$rdv,$flag)

{

 $bissextile = date ("L");  //$bissextile = 1 => annee bissextile sinon annee normale

 if ($Prem == 0 && $rdv != 1)
 {

    $firstday = date("l");

 }

 else

 {

     $firstday_query = mysql_query ("select dayname('$date')");

     $firstday = mysql_result ($firstday_query,0);

 }
 $date = Date_Lundi ($date,$firstday,$bissextile); //On obtient ainsi date du lundi de la semaine en cours
 $champ_date = explode("/",$date);



 switch ($champ_date[1]) {



 //Mois a 31 jours

         //Traitemant special pour mars : fevrier avant

         case 3 :    switch ($champ_date[2]) {

                          case 1 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=23;

                                       else $champ_date[2]=22;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 8;

                                       break;

                          case 2 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=24;

                                       else $champ_date[2]=23;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 9;

                                       break;

                          case 3 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=25;

                                       else $champ_date[2]=24;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 10;

                                       break;

                          case 4 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=26;

                                       else $champ_date[2]=25;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 11;

                                       break;

                          case 5 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=27;

                                       else $champ_date[2]=26;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 12;

                                       break;

                          case 6 : if ($sem_der == 1) {

                                       if ($bissextile == 1) $champ_date[2]=28;

                                       else $champ_date[2]=27;

                                       $champ_date[1]--;}

                                   if ($sem_pro == 1) $champ_date[2] = 13;

                                       break;

                          case 7 : if ($sem_der == 1) {

                                       if ($bissextile == 1) {$champ_date[2]=29;$champ_date[1]--;}

                                       else $champ_date[2]=28;

                                       $champ_date[1]--;}

                                       if ($sem_pro == 1) $champ_date[2] = 14;

                                       break;



                          case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                          case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                          case 27 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                          case 28 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                          case 29 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                          case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                          case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                          default : if ($sem_der == 1) $champ_date[2]-=7;

                                    if ($sem_pro == 1) $champ_date[2]+=7;



                          } //fin switch ($champ_date[2])

                break;



         //traitement special; pour janvier : revenir au mois de decembre  et decrementer de 1 l'annee

         case 1 :     switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 31;$champ_date[1]=12;$champ_date[0]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                          case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                          case 27 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                          case 28 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                          case 29 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                          case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                          case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                    if ($sem_pro == 1) $champ_date[2]+=7;



                           } //fin switch ($champ_date[2])

                  break;

         case 7 :       switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 24;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                          case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                          case 27 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                          case 28 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                          case 29 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                          case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                          case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                    if ($sem_pro == 1) $champ_date[2]+=7;



                           } //fin switch ($champ_date[2])

                  break;

         case 8 :     switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 31;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                           case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                           case 27: if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                           case 28: if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                           case 29: if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                           case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                           case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                     if ($sem_pro == 1) {$champ_date[2]+=7;}





                           } //fin switch ($champ_date[2])

                      break;

         //traitement special pour decembre: aller a janvier apres  et incrementer de 1 l'annee

         case 12:   switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 24;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                           case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                           case 27 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                           case 28 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                           case 29 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                           case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                           case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]=1;$champ_date[0]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                    if ($sem_pro == 1) $champ_date[2]+=7;



                           } //fin switch ($champ_date[2])

                    break;

         case 5 :

         case 10:       switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 24;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 25 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                     break;

                          case 26 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                          case 27 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                          case 28 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                     break;

                          case 29 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                          case 30 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                          case 31 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=24;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                    if ($sem_pro == 1) $champ_date[2]+=7;



                           } //fin switch ($champ_date[2])

                  break;

 // Mois a 30 jours

         case 4 :

         case 6 :

         case 9 :

         case 11:     switch ($champ_date[2]) {

                           case 1 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 31;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                           case 24 : if ($sem_pro == 1) {$champ_date[2]=1;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=17;

                                     break;

                           case 25 : if ($sem_pro == 1) {$champ_date[2]=2;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                    break;

                           case 26 : if ($sem_pro == 1) {$champ_date[2]=3;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                           case 27 : if ($sem_pro == 1) {$champ_date[2]=4;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                     break;

                           case 28 : if ($sem_pro == 1) {$champ_date[2]=5;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                    break;

                           case 29 : if ($sem_pro == 1) {$champ_date[2]=6;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=22;

                                    break;

                           case 30 : if ($sem_pro == 1) {$champ_date[2]=7;$champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=23;

                                    break;

                           default : if ($sem_der == 1) $champ_date[2]-=7;

                                     if ($sem_pro == 1) $champ_date[2]+=7;





                           } //fin switch ($champ_date[2])



                     break;



        //mois a nombre de jours "variables"

         case 2 : switch ($champ_date[2]) {

                          case 1 : if ($sem_der == 1) {$champ_date[2] = 25;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=8;

                                    break;

                           case 2 : if ($sem_der == 1) {$champ_date[2] = 26;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=9;

                                    break;

                           case 3 : if ($sem_der == 1) {$champ_date[2] = 27;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=10;

                                    break;

                           case 4 : if ($sem_der == 1) {$champ_date[2] = 28;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=11;

                                    break;

                           case 5 : if ($sem_der == 1) {$champ_date[2] = 29;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=12;

                                    break;

                           case 6 : if ($sem_der == 1) {$champ_date[2] = 30;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=13;

                                    break;

                           case 7 : if ($sem_der == 1) {$champ_date[2] = 31;$champ_date[1]--;}

                                    if ($sem_pro == 1) $champ_date[2]=14;

                                    break;



                          case 22 : if ($sem_pro == 1){

                                        if ($bissextile == 0) {

                                            $champ_date[2] = 1; $champ_date[1]++; }

                                        else $champ_date[2] = 29;}

                                    if ($sem_der == 1) $champ_date[2]=15;

                                    break;

                          case 23 : if ($sem_pro == 1){

                                        if ($bissextile == 1)

                                            $champ_date[2] = 1;

                                        else $champ_date[2] = 2;

                                        $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=16;

                                    break;

                          case 24 : if ($sem_pro == 1){

                                        if ($bissextile == 1)

                                            $champ_date[2] = 2;

                                        else $champ_date[2] = 3;

                                        $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=17;

                                    break;

                          case 25 : if ($sem_pro == 1){

                                        if ($bissextile == 1)

                                            $champ_date[2] = 3;

                                        else $champ_date[2] = 4;

                                        $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=18;

                                    break;

                          case 26 : if ($sem_pro == 1){

                                        if ($bissextile == 1)

                                            $champ_date[2] = 4;

                                        else $champ_date[2] = 5;

                                        $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=19;

                                    break;

                          case 27 : if ($sem_pro == 1){

                                        if ($bissextile == 1)

                                            $champ_date[2] = 5;

                                        else $champ_date[2] = 6;

                                        $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=20;

                                    break;

                          case 28 : if ($sem_pro == 1){

                                       if ($bissextile == 1)

                                            $champ_date[2] = 6;

                                       else $champ_date[2] = 7;

                                       $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2]=21;

                                    break;

                          case 29 : if ($sem_pro == 1) {

                                        $champ_date[2] = 7; $champ_date[1]++;}

                                    if ($sem_der == 1) $champ_date[2] = 22;

                                    break;

                         default : if ($sem_der == 1) $champ_date[2]-=7;

                                   if ($sem_pro == 1) $champ_date[2]+=7;



                          }  //fin switch ($champ_date[2])

                break;









 }            // fin switch ($champ_date[1])

$date = "$champ_date[0]/$champ_date[1]/$champ_date[2]";

if (isset($flg) && $flg == 1)

  echo " $champ_date[2]/$champ_date[1]/$champ_date[0]";

return $date;



} // fin fonction ParcoursMois



//fonction pour faire correspondre les creneaux et horaires

function Horaire ($creneau)

{

 switch ($creneau) {

         case 1 : $horaire = "8h-9h";

              break;

         case 2 : $horaire = "9h-10h";

              break;

         case 3 : $horaire = "10h-11h";

              break;

         case 4 : $horaire = "11h-12h";

              break;

         case 5 : $horaire = "12h-13h";

              break;

         case 6 : $horaire = "13h-14h";

              break;

         case 7 : $horaire = "14h-15h";

              break;

         case 8 : $horaire = "15h-16h";

              break;

         case 9 : $horaire = "16h-17h";

              break;

         case 10: $horaire = "17h-18h";

              break;

         case 11: $horaire = "18h-19h";

              break;

         case 12: $horaire = "19h-20h";

              break;

         case 13: $horaire = "20h-21h";

              break;

         case 14: $horaire = "21h-22h";

              break;

         case 15: $horaire = "22h-23h";

              break;

         case 16: $horaire = "23h-24h";

              break;

           }

return $horaire;

} //fin fonction
