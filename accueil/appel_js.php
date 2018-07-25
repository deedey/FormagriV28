<?php
//-------------------------------------------------- debut de l'inclusion  -- AjaX -------------------------------------------------
?>
<div id="new">
  <a style="" href="#" id="windowOpen" title="" class='bouton_new'><?php echo $le_titwin;?></a>
    <div id="window">
        <div id="windowTop">
                <div id="windowTopContent"><?php echo $le_titwin;?></div>
                <img src="<?php echo $monURI;?>/OutilsJs/images/window_min.jpg" id="windowMin" />
                <img src="<?php echo $monURI;?>/OutilsJs/images/window_max.jpg" id="windowMax" />
                <img src="<?php echo $monURI;?>/OutilsJs/images/window_close.jpg" id="windowClose" />
        </div>
        <div id="windowBottom">
             <div id="windowBottomContent">&nbsp;</div>
        </div>
        <div id="windowContent">
           <?php include ($mon_fichier);?>
        </div>
        <img src="<?php echo $monURI;?>/OutilsJs/images/window_resize.gif" id="windowResize" />
    </div>
   <div class="" style="overflow: visible; display: none; position: absolute; width: 450px; height: 298px; top: 100px; left: 200px;" id="transferHelper">
   </div>
</div>
<?php
//-------------------------------------------------- Fin de l'inclusion -- AjaX -------------------------------------------------
?>