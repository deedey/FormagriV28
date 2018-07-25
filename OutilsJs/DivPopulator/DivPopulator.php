<script type="text/javascript">

$(document).ready(function() {
    //
    //var url=window.host

    var URL="<?php echo $monURI;?>/OutilsJs/DivPopulator/AjaxDivPopulator.php";//
    //alert(URL);
    jQuery.divpopulator( URL,
                            {
                                postParam:{
                                    dbTable:'<?php echo $table;?>'
                                    ,dbTableFieldLabel:'<?php echo $fieldLabel ;?>'
                                    ,dbTableFieldId:'<?php echo $fieldId;?>'
                                    ,maxItems:50
                                    ,dbTableFieldCond:'<?php echo $fieldCond;?>'
                                },

                                options:{
                                    hiddenName:'<?php echo $HideLabel;?>'
                                    ,defaultValue:''
                                    ,searchEngineContainerId:'searchEngine'
                                    ,inputSearch:'inputSearch'
                                    ,highLightBorderColor:'#319aff'
                                    ,highLightWordSearchClass:'highLightWordSearch'
                                    ,removeResultAfterClick:true
                                }
                            }
     );
});

</script>
<style type="text/css">

 #searchEngine  {background-color:#eee;font-family:tahoma, arial;font-size:11px;color:#3A3A3A;position:absolute;z-index:2;}
 #searchEngine  {text-align:left;border:1px solid gray;margin:4px;}
 #searchEngine #resultInformation{margin-left:4px;}
 #searchEngine #resultList{background:#eee;border:0px;margin:2px;padding:0px;width:auto;height:auto;max-height:300px;overflow:auto;}
 #searchEngine #resultList div.item{padding:5px;border:1px solid #cecece;margin:5px 5px 0px 5px;}
 #searchEngine #inputSearch{background-color:#bcd9de;margin: 6px 6px 2px 6px;}
 #searchEngine #resultInformation{ margin:0px 0px 10px 6px;}
 #searchEngine .highLightWordSearch{background-color:#fff000;padding-bottom:2px;font-weight:bold;}
 #searchEngine  .loadingInInput {
    background: url('<?php echo $monURI;?>/OutilsJs/images/loading.gif') no-repeat;
    padding-left: 20px;
 }

#searchEngine .searchInInput {
  background: url('<?php echo $monURI;?>/OutilsJs/images/search.gif') no-repeat;
  padding-left: 20px;
}

</style>


<!--- DivPopulator -->
<div id="searchEngine">
        <input type="text" name="search" id="inputSearch" value="" size="100" style="width:400px;" autocomplete="off" maxlength="100" />
</div>
<!---fin DivPopulator -->
