<?php
if (!isset($_SESSION)) session_start();
require '../../admin.inc.php';
?>
<html>
<head>
<meta http-equiv="Content-Language" content="fr" />
<meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
<META HTTP-EQUIV="Content-Language" CONTENT="fr-FR">
<link rel="icon" href="/favicon.ico" />
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
<link rel="stylesheet" href="style.css" type="text/css" />
<title>Formagri : fil de l'actualité</title>
<script type="text/javascript" src="<?php echo $_SESSION['monURI'];?>/OutilsJs/jquery-1.2.2.pack.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['monURI'];?>/OutilsJs/lib/interface.js"></script>
<style type="text/css" media="all">
*
{
        margin: 0;
        padding: 0;
}
body
{
        background: #fff;
        height: 100%;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
}
#myAccordion{
        width: 600px;
        border: 1px solid #182052; /*#6699CC;  #6CAF00;*/
        position: absolute;
        left: 10px;
        top: 10px;
}
#myAccordion dt{
        line-height: 20px;
        background-color:  #8a9ec1; /*#0099cc;#80df20;*/
        border-top: 2px solid #BDC7E7;  /*#97b2e2;#669999;#DAFF9F;*/
        border-bottom: 2px solid #182052;  /*#4170c3;#6699CC;#6CAF00;*/
        padding: 0 10px;
        font-weight: bold;
        color: #fff;
}
#myAccordion dd{
        overflow: auto;
}
#myAccordion p{
        margin: 16px 10px;
}
#myAccordion dt.myAccordionHover
{
        background-color: #7593c7;  /*#1f669b; #66CCCC;#006690; #90ef30;*/
        cursor: pointer;
}
#myAccordion dt.myAccordionActive
{
        background-color: #97b2e2;  /*#6699CC;#6CAF00;*/
        border-top: 2px solid #BDC7E7; /*#1f669b;   #97b2ee;  0099cc;#80df20;*/
        border-bottom: 2px solid #4170c3;  /*#182052; #000;*/
}
</style>
</head>
<body>

<dl id="myAccordion">
         <?php
            include 'acco_parse_read.php';
            //
            //http://blog.tutofop.educagri.fr/wordpress/?feed=rss
            if (!isset($p) || (isset($p) && $p == 0))
                 $p = 6;
            if (isset($url) && $url != '')
                affiche_fil($url.'?p='.$p,'acco_read.html', 1,$p);
            else
                affiche_fil($adresse_http.'/utilitaires/Rss/flux.php?p='.$p,'acco_read.html', 1,$p);
            ?>
</dl>
<script type="text/javascript">
        $(document).ready(
                function()
                {
                        $('#myAccordion').Accordion(
                                {
                                        headerSelector       : 'dt',
                                        panelSelector        : 'dd',
                                        activeClass          : 'myAccordionActive',
                                        hoverClass           : 'myAccordionHover',
                                        panelHeight          : 150,
                                        speed                : 'normal'
                                }
                        );
                }
        );
</script>

</body>
</html>
