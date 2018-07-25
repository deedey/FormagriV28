
<?php
/**
   * Class  Helper
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package View
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 8 janv. 08 14:58:18
   * @copyright
   */
require_once 'View.php';
//
class View_Helper extends View
{
	//
	function Helper()
	{
	}
	//
	function htmlElement($htmlElement, $content = '', $attribs = array ())
	{
		$list = array (
			'a',
			'form',
			'label',
			'span',
			'div',
			'table',
			'td',
			'tr',
			'text',
			'textarea'
		);
		if (!in_array($htmlElement, $list))
		{
			$this->debug('Le composant  "' . $htmlElement . '"  n\'&eacute;xiste pas');
			exit;
		}
		else
		{
			//
			//			$aHtmlElementAttrib['id'] = '';
			//			$aHtmlElementAttrib['class'] = '';
			//
			$autorizedAttrib = array ();
			$attribs = (array) $attribs;
			switch ($htmlElement)
			{
				case 'a' :
					$autorizedAttrib = array (
						'href' => ' ',
						'title' => ' '
					);
					$attribs = $this->rebuildAttrib($attribs, $autorizedAttrib);
					return '<' . $htmlElement . $this->buildAttribs($attribs) . '>' . $content . '</' . $htmlElement . '>';
					//
					break;
				case 'form' :
					$autorizedAttrib = array (
						'action' => ' ',
						'method' => 'post'
					);
					$newAttribs = $this->rebuildAttrib($attribs, $autorizedAttrib);
					return '<' . $htmlElement . $this->buildAttribs($newAttribs) . '>' . $content . '</' . $htmlElement . '>';
					//
					break;
					//
				case 'text' :
					//
					$autorizedAttrib = array (
						'id' => null,
						'name' => null,
                        'value' => null,
						'class' => 'inputText',
						'disabled' => '',
						'readonly' => '',
						'tabindex' => ''
					);
					$autorizedAttrib['value'] = $content;
					$autorizedAttrib['type'] = $htmlElement;
					$class = trim($autorizedAttrib['class'] . ' ' . @ $attribs['classNameError']);
					$newAttribs = $this->rebuildAttrib($attribs, $autorizedAttrib);
					$newAttribs['class'] = $class;
					return '<input' . $this->buildAttribs($newAttribs) . ' />';
					break;
					//
				case 'textarea' :
					$autorizedAttrib = array (
						'id' => null,
						'name' => null,
						'class' => 'textarea',
						'rows' => '10',
						'cols' => '50',
						'disabled' => '',
						'readonly' => '',
						'tabindex' => ''
					);
					//
					$class = trim($autorizedAttrib['class'] . ' ' . @ $attribs['classNameError']);
					$newAttribs = $this->rebuildAttrib($attribs, $autorizedAttrib);
					$newAttribs['class'] = $class;
					return '<' . $htmlElement . $this->buildAttribs($newAttribs) . '>' . $content . '</' . $htmlElement . '>';
					break;
					//
			} //fin switch
			//
		}
	}
	//
	function rebuildAttrib($attribs, $autorizedAttrib)
	{
		foreach ($autorizedAttrib as $key => $value)
		{
			if (!isset ($attribs[$key]))
			{
				if (empty ($autorizedAttrib[$key]))
				{
					unset ($autorizedAttrib[$key]);
				}
			}
			else
			{
				$autorizedAttrib[$key] = $attribs[$key];
			}
		}
		return $autorizedAttrib;
	}
	//
	function image($src, $title = '', $alt = '', $attribs = array ())
	{
		return '<img src="' . $src . '" ' . ' title="' . $title . '"  alt="' . $alt . '" ' . $this->buildAttribs($attribs) . ' />';
	}
	//
	function input($elementType, $attribs)
	{
		$input = '<input type="' . $elementType . '"' . $this->buildAttribs($attribs) . ' />';
		//
		return $input;
	}
	//
	function htmlSelect($value = null, $attribs, $data = array (), $firstOption = null)
	{
		//
		$select = array ();
		foreach ($data as $_key => $_val)
		{
			if (is_array($_val))
			{
				$select[] = '<optgroup label="' . $_key . '">';
				foreach ($_val as $key => $val)
				{
					$selected = '';
					if (strcmp($value, $key) == 0)
					{
						$selected = ' selected="selected"';
					}
					$select[] = '<option value="' . trim($key) . '"' . $selected . '>' .
                                                    htmlentities(trim($val),ENT_QUOTES,'ISO-8859-1') . '</option>';
				}
				$select[] = '</optgroup>';
			}
			else
			{
				$selected = '';
				if (strcmp($value, $_key) == 0)
				{
					$selected = ' selected="selected"';
				}
				$select[] = '<option value="' . trim($_key) . '"' . $selected . '>' .
                                            htmlentities(trim($_val),ENT_QUOTES,'ISO-8859-1') . '</option>';
			}
		}
		//
		$selectFirstOption = '';
		if (isset ($firstOption))
		{
			$selectFirstOption = '<option value="">' . $firstOption . '</option>';
		}
		$htmlSelect = '<select' . $this->buildAttribs($attribs) . '>' . $selectFirstOption . implode("\n", $select) . '</select>';
		return $htmlSelect;
	}
	//
}
?>
