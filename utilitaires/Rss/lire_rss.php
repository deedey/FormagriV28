<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
        <meta http-equiv="content-type" content="text/html;charset=ISO-8859-1">
        <META HTTP-EQUIV="Content-Language" CONTENT="fr-FR">
        <title>Formagri : fil de l'actualité</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
        <link rel="icon" href="/favicon.ico" />
         <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
         <script type="text/javascript"><!--
function visible(element_id) {
        var div_element;
        div_element = document.getElementById(element_id) ;
        if (div_element.style.display == 'none' || div_element.style.display == '') {
                div_element.style.display = 'block' ;
        } else {
                div_element.style.display = 'none' ;
        }
}

function montre_tout(element_id) {
        var div_element;
        div_element = document.getElementById(element_id) ;
        div_element.style.display = 'block' ;
}

function cache_tout(element_id) {
        var div_element;
        div_element = document.getElementById(element_id) ;
        div_element.style.display = 'none' ;

}

//-->
</script>
        </head>
<body>
        <?php include 'parse_read.php'; ?>

        <div class="fulllist">

            <?php
            //http://ef-dev2.educagri.fr/utilitaires/Rss/flux.php
            //http://blog.tutofop.educagri.fr/wordpress/?feed=rss
            if (isset($url) && $url != '')
                affiche_fil($url,'read.html', 1, 6);
            else
                affiche_fil('http://ef-dev2.educagri.fr/utilitaires/Rss/flux.php','read.html', 1, 6);
            ?>
        </div>

</body>
</html>