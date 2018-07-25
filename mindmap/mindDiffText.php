<?php
   /**
        Diff implemented in pure php, written from scratch.
        Copyright (C) 2003  Daniel Unterberger <diff.phpnet@holomind.de>
        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation; either version 2
        of the License, or (at your option) any later version.
        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.
        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
        http://www.gnu.org/licenses/gpl.html
        About:
        I searched a function to compare arrays and the array_diff()
        was not specific enough. It ignores the order of the array-values.
        So I reimplemented the diff-function which is found on unix-systems
        but this you can use directly in your code and adopt for your needs.
        Simply adopt the formatline-function. with the third-parameter of arr_diff()
        you can hide matching lines. Hope someone has use for this.
        Contact: d.u.diff@holomind.de <daniel unterberger>
    **/
    /**
        Modifiée par Dey Bendifallah pour un affichage intégral des phrases
        et un stylage des différences
    **/
    function arr_diff( $f1 , $f2 , $show_equal = 0 )
    {
        $c1         = 0 ;
        $c2         = 0 ;
        $max1       = count( $f1 ) ;
        $max2       = count( $f2 ) ;
        $outcount   = 0;
        $hit1       = "" ;
        $hit2       = "" ;
        $stop       = 0;
        $out        = "";
        while (($c1 <= $max1 or $c2 <= $max2) and ($stop++) < 10000 and $outcount < 1000)
        {
            if (isset($f1[$c1]) && isset($f2[$c2]) && trim( html_entity_decode($f1[$c1],ENT_QUOTES,'ISO-8859-1' )) == trim ( html_entity_decode($f2[$c2],ENT_QUOTES,'ISO-8859-1'))  )
            {
                $out    .= ($show_equal==1) ?  formatline ( ($c1) , ($c2), "=", $f1[ $c1 ] ) : "" ;
                if ( $show_equal == 1 )
                    $outcount++ ;
                $c1 ++;
                $c2 ++;
            }
            else
            {
                $b      = "" ;
                $s1     = 0  ;
                $s2     = 0  ;
                $found  = 0  ;
                $b1     = "" ;
                $b2     = "" ;
                $fstop  = 0  ;
                while ($found == 0 and (( $c1 + $s1 <= $max1 ) or ( $c2 + $s2 <= $max2 )) and $fstop++  < 1000)
                {

                    if (isset($f1[$c1+$s1]) && isset($f2[$c2]) && trim( $f1[$c1+$s1] ) == trim( $f2[$c2] ))
                    {
                        $found  = 1   ;
                        $s2     = 0   ;
                        $c2--         ;
                        $b      = $b1 ;
                    }
                    else
                    {
                        if (!isset($hit1[ ($c1 + $s1) . "_" . $c2 ]) && (($c2 + $s2)<=$c1 || ($c1 + $s1)<=$c2))
                        {
                            $b1  .= formatline( ($c1 + $s1) , ($c2), "-", $f1[ $c1+$s1 ] );
                            $hit1[ ($c1 + $s1) . "_" . $c2 ] = 1 ;
                        }
                    }
                    if (isset($f1[$c1]) && isset($f2[$c2+$s2]) && trim ( $f1[$c1] ) == trim ( $f2[$c2+$s2])  )
                    {
                        $found  = 1   ;
                        $s1     = 0   ;
                        $c1--         ;
                        $b      = $b2 ;
                    }
                    else
                    {
                        if (!isset($hit2[ $c1 . "_" . ( $c2 + $s2) ]) && (($c2 + $s2)<=$c1 || ($c1 + $s1)<=$c2))
                        {
                            $b2   .= formatline ( ($c1) , ($c2 + $s2), "+", $f2[ $c2+$s2 ] );
                            $hit2[ $c1 . "_" . ($c2 + $s2) ] = 1;
                        }
                    }
                    $s1++ ;
                    $s2++ ;
                }
                if ( $found == 0 )
                {
                    $b  .= formatline ( ($c1) , ($c2), "-", $f1[ $c1 ] );
                    $b  .= formatline ( ($c1) , ($c2), "+", $f2[ $c2 ] );
                }
                $out        .= $b;
                $outcount++ ;
                $c1++  ;
                $c2++  ;
            }
        }
        return $out;
    }
    function formatline( $nr1, $nr2, $stat, &$value )
    {
        if ( trim( $value ) == "" )
            return "";
        switch ( $stat )
        {
            case "=":
                return "<span style='font-weight:normal;'>".$value. "</span> -  ";
               break;
            case "+":
                return "<span style='background-color:yellow;padding:0 4px 0 4px;'>".$value  ."</span> -  ";
               break;
            case "-":
                return "<span style='background-color:red;color:#fff;padding:0 4px 0 4px;'>".$value  ."</span>". " - ";
              break;
        }
    }
    function trimUltime($chaine)
    {
             $chaine = trim($chaine);
             $chaine = str_replace("\t", " ", $chaine);
        return $chaine;
    }
session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../fonction_html.inc.php";
dbConnect();
$content = '';$str1 = '';$str2 = '';
$Tab = explode("<Text>",html_entity_decode(GetDataField ($connect,"select mindhisto_xmldata_cmt from mindmaphistory where mindhisto_cdn = ".$_GET['numero'],"mindhisto_xmldata_cmt"),ENT_QUOTES,'ISO-8859-1'));
$NbI= count($Tab);
for ($i=0;$i<$NbI;$i++)
{
 $TabC= explode("</Text>",$Tab[$i]);
 $str1 .= strip_tags($TabC[0]).' - ';
}
$Tab = explode("<Text>",html_entity_decode(GetDataField ($connect,"select mindhisto_xmldata_cmt from mindmaphistory where mindhisto_cdn = ".$_GET['numRef'],"mindhisto_xmldata_cmt"),ENT_QUOTES,'ISO-8859-1'));
$NbI= count($Tab);
for ($i=0;$i<$NbI;$i++)
{
 $TabC= explode("</Text>",$Tab[$i]);
 $str2 .= strip_tags($TabC[0]).' - ';
}
$f1 = explode(" - ", clean_text($str1));
$f2 = explode(" - ", clean_text($str2));
//echo "<pre>";print_r($f1);print_r($f2);echo "</pre>";
$content .= '<html><head><style>'.
            '.completBody{clear:both;margin:10px 10px 10px 5px;padding:4px;border:1px dotted #000;'.
            'background-color:#eee;width:600px;font-family:arial,verdana,tahoma;font-size:11px;}'.
            '</style></head><body>';
$content .= '<div class="completBody"><span style="font-size:11px;font-weight:bold;">Instance en cours en date du '.
            reverse_date(substr($_GET['actuDt'],0,10),'-','-').' à '.
           substr($_GET['actuDt'],11,8).' par '.NomUser($_GET['UserActu']).'</span><br />'.clean_text($str1).'</div>';
$content .= '<div class="completBody"><span style="font-size:11px;font-weight:bold;">Comparée à l\'instance en date du '.
            reverse_date(substr($_GET['IdDt'],0,10),'-','-').' à '.
           substr($_GET['IdDt'],11,8).' par '.NomUser($_GET['IdUser']).'</span><br />'.clean_text($str2).'</div>';
$content .= '<div style="border:1px solid #000;width:250px;padding:4px;margin:10px 10px 10px 5px;'.
            'font-family:arial,verdana,tahoma;font-size:11px;">'.
            '<span style="font-weight:bold;font-size:12px;">Légende:</span><br>';
$content .= '<span style="font-weight:normal;">Identique</span>'.
            '<span style="background-color:yellow;padding:0 4px 0 4px;margin-right:4px;">En plus</span>'.
            '<span style="background-color:red;color:#fff;padding:0 4px 0 4px;">En moins</span></div><p>';
$content .= '<div style="font-family:arial,verdana,tahoma;font-size:12px;">'.
            arr_diff( $f2, $f1 ,1);
$content .= '</div>';
$content .= '</body></html>';
print $content;
?>