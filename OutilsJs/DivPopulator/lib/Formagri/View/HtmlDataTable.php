<?php
/**
   * Class  HtmlDataTable
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@educagri.fr>
   * @package View
   * @description
   * @dependance
   * @license
   * @version 1.0
   * @date 21 déc. 07 13:17:25
   * @copyright
   */

//
require_once 'View.php';
class HtmlDataTable extends View
{
	var $displayFooterFieldName = false;
	var $limitToDisplayFooterFieldName = 20;
	//
	function HtmlDataTable($aoRow, $aHeader, $options = null)
	{
		$this->aoRow = $aoRow;
		$this->aHeader = $aHeader;
		$this->options = $options;
	}
	//
	function render()
	{
		$fieldList = array_keys($this->aoRow[0]->fields);
		//
		if (!empty ($this->options['alternateBgColor']))
		{
			$tab = explode('-', $this->options['alternateBgColor']);
			$str = ' style="background-color:';
			$bgColor_a = $str . $tab[0] . ';"';
			$bgColor_b = $str . $tab[1] . ';"';
		}
		$numRow = 0;
		foreach ($this->aoRow as $oRow)
		{
			$numRow++;
			if (fmod($numRow, 2) == 0)
			{
				$bgColor = $bgColor_a;
			}
			else
			{
				$bgColor = $bgColor_b;
			}
			$HtmlDataTable[] = '<tr' . $bgColor . '>';
			//
			$th = '<thead><tr>';
			$numCol = 0;
			foreach ($this->aHeader as $key => $header)
			{
				$numCol++;
				if (isset ($header->styleClassName))
				{
					$styleClassName = ' class="' . $header->styleClassName . '"';
				}
				$th .= '<th' . $styleClassName . '>' . ucfirst($header->content) . '</th>';
				//
                $tdStyleClassName='';


                if(!empty($oRow->fields[$key]->styleClassName))
                    {
                    $tdStyleClassName = ' class="' . $oRow->fields[$key]->styleClassName . '"';
                    }
				if ($key == '_NUM_')
				{
					$value = $numRow;
				}
				else
				{
					$value = $oRow->fields[$key]->value;


					if (empty ($value))
					{
						$value = '&nbsp;';
					}
				}



				//C:column,R:row
				$td_id = 'C' . $numCol . '-R' . $numRow;
				$HtmlDataTable[] = '<td id="' . $td_id . '"' . $tdStyleClassName . '>' . trim($value) . '</td>';
			}
			$th .= '</tr></thead>';
			$HtmlDataTable[] = '</tr>';
		}
		//
		$caption = '';
		if (isset ($this->options['caption']))
		{
			//
			foreach ($this->options['caption']['option'] as $key => $value)
			{
				$captionAttrib .= ' ' . $key . '="' . $value . '"';
			}
			$caption = '<caption' . $captionAttrib . '>' . $this->options['caption']['label'] . '</caption>';
		}
		$table = '<table';
        $table.=' id="' . @$this->options['id']  .'"' ;
        $table.=' class="' . @$this->options['class']  .'"' ;
        $table.=' summary="' . @$this->options['summary']  .'"' ;
        $table.=' cellspacing="1" cellpadding="0">';
		$thEnd = '';
		if (count($this->aoRow) >= (int) $this->limitToDisplayFooterFieldName AND $this->displayFooterFieldName)
		{
			//$thEnd = $th;
		}
		$table .= $caption . $th . implode("", $HtmlDataTable) . $thEnd ;
         $table .='</table>';
		return $table;
	}
	//
} //fin classe HtmlDataTable
?>

