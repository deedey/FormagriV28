<?php
//error_reporting(E_ALL | E_STRICT);
//------------------------------------------------------------------------------//

class TreeForum
{
 /**
   * Class  TreeForum
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@educagri.fr>
   * @description classe recursive permettant l'archivage d'un fil de discussion ou d'un forum
   * @date 22 oct. 07
   */
  var $aList = array ();
  var $aItems = array ();
  var $itemsUsed = array ();
  var $resolvedTree = array ();
  //
  function display($arr)
  {
      echo $arr;
  }
  function TreeForum($aItems=array())
  {

      $this->aItems =$aItems; //sql
    //
    $this->resolvedTree=$this->recursif($aItems);
  }
  //
  function getResolvedTree()
  {
    return   $this->resolvedTree;
  }
  //
  //
  function isExist($obj)
  {
    if (in_array($obj->id, $this->itemsUsed))
    {
      return true;
    }
  }
  //
  function recursif($aItems)
  {
    $arr = array ();
    foreach ($aItems as $key => $obj)
    {
      //
      if (!$this->isExist($obj))
      {
        if ((int) count($this->getChilds($obj->id)) > 0)
        {
           $subject= $obj->subject;
           $arr[$obj->id] = '<li class="treeItem">' .$subject. $this->recursif($this->getChilds($obj->id)) . '</li>';
        }
        else
        {
           $arr[$obj->id] = '<li class="treeItem">'.$obj->subject . '</li>';
        }
      }
      //
      $this->itemsUsed[] = $obj->id;
      //
    }
    $sList = '<ul style="display: block;">' . implode("\n", $arr) . "\n" . '</ul>';
    //
    // $aList=array_unique($aList);
    return $sList;
?>
<script type="text/javascript">
$(document).ready(
        function()
        {
                tree = $('#myTree');
                $('li', tree.get(0)).each(
                        function()
                        {
                                subbranch = $('ul', this);
                                if (subbranch.size() > 0) {
                                        if (subbranch.eq(0).css('display') == 'none') {
                                                $(this).prepend('<img src="../images/plus.gif"  class="expandImage" />');
                                        } else {
                                                $(this).prepend('<img src="../images/moins.gif" class="expandImage" />');
                                        }
                                } else {
                                        $(this).prepend('<img src="../scorm/spacer1.gif" class="expandImage" />');
                                }
                        }
                );
                $('img.expandImage', tree.get(0)).click(
                        function()
                        {
                                if (this.src.indexOf('spacer') == -1) {
                                        subbranch = $('ul', this.parentNode).eq(0);
                                        if (subbranch.css('display') == 'none') {
                                                subbranch.show();
                                                this.src = '../images/moins.gif';
                                        } else {
                                                subbranch.hide();
                                                this.src = '../images/plus.gif';
                                        }
                                }
                        }
                );
        }
);
</script>
<?php
    //
  }
  //
  function getChilds($parent_id)
  {

    //
    $arr = array ();
    foreach ($this->aItems as $key => $obj)
    {
      if ($obj->parent == $parent_id)
      {
        $arr[$obj->id] = $obj;
      }
    }
    return $arr;
    //
  }

}
//------------------------------------------------------------------------------//

?>