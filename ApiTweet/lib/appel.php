<?php
if (!isset($_SESSION)) session_start();
// On charge la config et les librairies
require ('../../admin.inc.php');
require ('../../fonction.inc.php');
include('config.php');
//$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
if (isset($_GET['lance']) && $_GET['lance'] == 'ajout')
{
          $mymessage = '#'.$_GET['Seq'].' '.$_GET['Qui'].': '.mb_convert_encoding($_GET['textearea'],'UTF-8','iso-8859-1');
          $parameters = array('status' => stripslashes($mymessage));
          $status = $connection->post('https://api.twitter.com/1.1/statuses/update.json', $parameters);
        return $status;
}
elseif (isset($_GET['lance']) && $_GET['lance'] == 'charge')
{
       $TabTweets = $connection->get('https://api.twitter.com/1.1/search/tweets.json?q=%23'.$_GET['Seq'].'&include_entities=true&result_type=recent&count=100');
       $NbrTweets = count($TabTweets->statuses);
       //echo "<pre>";print_r($TabTweets);echo "<pre>";
       $i=0;
       $n=1;
       if (!strstr($_SERVER["SERVER_NAME"],'educagri.fr'))
          $Renvoi = '<div><div class="entete">'.
                 '<img src="assets/formagri.gif" class="imgNote"></div>'.
                 '<div class="note">Pour suivre exclusivement cette discussion sur un autre support tel que smartphone, tablette, PC, '.
                 'utilisez le HASHTAG <span class="diese">#'.$Seq.'</span> pour votre recherche sur Twitter</div></div>';
       else
          $Renvoi= "";
       while ($i< $NbrTweets)
       {
              $depuis=diffDate(strtotime($TabTweets->statuses[$i]->created_at));
              if (strstr($TabTweets->statuses[$i]->text,'#'.$Seq))
              {
                  $tabTwt = explode(" ",$TabTweets->statuses[$i]->text);
                  $NbrMots = count($tabTwt);
                  $TexteAffiche = '';
                  $j=0;
                  while ($j< $NbrMots)
                  {
                     $mot = $tabTwt[$j];
                     if ($tabTwt[$j][0] == '@') $mot = '<span class="arobase">'.$tabTwt[$j].'</span>';
                     if ($tabTwt[$j][0] == '#') $mot = '<span class="diese">'.$tabTwt[$j].'</span>';
                     if (substr($tabTwt[$j],0,4) == 't.co' || substr($tabTwt[$j],0,7) == 'http://') $mot = '<a href="'.$tabTwt[$j].'" target="_blank">'.$tabTwt[$j].'</a>';
                     $TexteAffiche .= ' '.$mot;
                     $j++;
                  }
                  $NomSuiveur = ($TabTweets->statuses[$i]->user->screen_name == 'formagri') ? '' : 'title="'. $TabTweets->statuses[$i]->user->name.'  alias  @'.$TabTweets->statuses[$i]->user->screen_name.'"';
                  $Renvoi .= '<div id="TTtwit"><div id="MonImg" '.$NomSuiveur.'>'.
                             '<img class="LogTwit" src="'.$TabTweets->statuses[$i]->user->profile_image_url.'"></div>'.
                             '<div class="depuis">';
                  if (!strstr($TabTweets->statuses[$i]->source,'Formagri'))
                       $Renvoi.= str_replace('for','pour',strip_tags($TabTweets->statuses[$i]->source)).' - ';
                  else
                       $Renvoi.= '&nbsp;&nbsp;&nbsp;&nbsp;';
                  $Renvoi.= $depuis.'</div><div id="tweet" class="twit" onMouseOver="$(this).addClass(\'twitOn\');" '.
                             'onMouseOut="$(this).removeClass(\'twitOn\');$(this).addClass(\'twit\');">'.
                             $TexteAffiche. '</div></div>';
                  $n++;
              }
          $i++;
       }
       echo $Renvoi;
}
function diffDate($date)
{
   $moisFrench=array(1=>'Jan','Fev','Mar','Avr','Mai','Jui','Juil','Aou','Sep','Oct','Nov','Dec');
   $Now = time();
   $NowR=date("d",$Now).' '.$moisFrench[date("n",$Now)].' '.date("Y",$Now)."--- ".date("H",$Now)."h";
   $result=date("d",$date).' '.$moisFrench[date("n",$date)].' '.date("Y",$date)."--- ".date("H",$date)."h";
   //return $result;
   if (date("s",$Now) != date("s",$date) && date("i",$Now) == date("i",$date) && date("H",$Now) == date("H",$date)&& date("d",$Now) == date("d",$date) && date("n",$Now) == date("n",$date))
      return 'moins d\' 1mn';
   elseif (date("i",$Now) != date("i",$date) && date("H",$Now) == date("H",$date)&& date("d",$Now) == date("d",$date) && date("n",$Now) == date("n",$date))
      return (date("i",$Now) - date("i",$date)).'mn';
   elseif (date("H",$Now) != date("H",$date) && intval((time()-$date)/60) < 60 && date("d",$Now) == date("d",$date) && date("n",$Now) == date("n",$date))
      return intval((time()-$date)/60).'mn';
   elseif (date("H",$Now) != date("H",$date) && intval((time()-$date)/60) > 59 && date("d",$Now) == date("d",$date) && date("n",$Now) == date("n",$date))
      return (date("H",$Now) - date("H",$date)).'h';
   elseif (date("d",$Now) != date("d",$date) && date("n",$Now) == date("n",$date) && (date("d",$Now) - date("d",$date)) > 6)
      return date("d",$date).' '.$moisFrench[date("n",$date)];
   elseif (date("d",$Now) != date("d",$date) && date("n",$Now) == date("n",$date) && (date("d",$Now) - date("d",$date)) < 7 && (date("d",$Now) - date("d",$date)) > 1)
      return (date("d",$Now) - date("d",$date)).'j';
   elseif (date("d",$Now) != date("d",$date) && date("n",$Now) == date("n",$date) && (date("d",$Now) - date("d",$date)) < 7 && (date("d",$Now) - date("d",$date)) == 1)
      return 'hier';
   elseif (date("d",$Now) != date("d",$date) && date("n",$Now) != date("n",$date))
      return date("d",$date).' '.$moisFrench[date("n",$date)];
}
?>
