<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Formagri :: R&eacute;ferentiel</title>
<meta http-equiv="content-type" content="charset=ISO-8859-1" />
<link rel="stylesheet" type="text/css" href="../../../views/css/referentiel.css" />

</head>
<body>
<?php
error_reporting(E_ALL);
/**
   * Class  HtmlForm
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package View
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 15 janv. 08 16:33:46
   * @copyright
   */
require_once 'Helper.php';
require_once '../Utils/Utils.php';
//
$oHtmlForm = new HtmlForm();
echo '<div id="moduleReferentiel"><div id="refMiddle">' . $oHtmlForm->getForm() . '</div></div>';
class HtmlForm
{
	var $debug = false;
	//
	var $aPostedFieldToSave = array ();
	var $aFieldToSave = array ();
	var $aFieldRequired = array ();
	//var $tableObject;
	var $form_id = 'grainPedago';//
	var $container_form_id = 'form_container'; //
	var $actionSuccess = null;
	var $actionFailed = null;
	var $action = null; //insert, update
	//var $tableMasterPrimaries = array ();
	var $formLabel = 'Ajouter un grain';
	//var $formLabelUpdate = null;
	//
	//var $aTableChild = array ();
	var $errDefaultMessage = 'Champ obligatoire';
	var $aoField = array ();
	/**
	 *
	 var $aoField = array (
		'un champ de la table' => array (
			'label' => 'un libelle',
			'regex' => 'une expression reguliere exemple [^0-9]',
			'errMessage' => 'Le code doit &ecirc;tre un entier',
			'information' => ">> une information"
		)
	);
	*/
	//
	function __construct()
	{
		//
		@ if (!isset($_SESSION)) session_start();
		//
        /*
         * table] => ref_sequence_savoir
            [primary] => 1
            [autoIncrement] => 1
            [listData] =>
            [required] => 1
            [defaultValue] =>
            [value] =>
            [field] => sequence_savoir_id
            [type] => integer
            [typeSql] => int
            [wrappedText] =>
            [maxLength] => 11

         */
		//
		/*
         $oField = new stdClass();
		$oField->field = 'name';
		$oField->label = 'un label';
		$oField->regex = 'regex';
		$oField->validatingFunction = null;
		$oField->information = 'une information';
		$oField->displaySelectCount = false;
		$oField->required = true;
		$oField->defaultValue = 'test defaultValue';
		$oField->value = 'test value';
		$oField->errMessage = null;
		$oField->classNameError = null;
		$oField->isValid = null;
		$oField->html = (object) array (
             'tag' => 'input',
			'type' => 'text'
		);
		$oField->listData = $array = array (
			'key_1' => 'val_1',
			'key_2' => 'val_2',
			'key_3' => 'val_3'
		);

		$this->aoField[$oField->field] = $oField;
          */

		//
		if (!empty ($_POST))
		{
			//
			$this->aPostedFieldToSave = array_map(array (
				$this,
				'trimArray'
			), $_POST[$this->form_id]);
		}
		//
	}
	//
    function getNewObject()
    {
        $oField = new stdClass();
        $oField->field = 'name';
        $oField->label = 'un label';
        $oField->regex = 'regex';
        $oField->validatingFunction = null;
        $oField->information = 'une information';
        $oField->displaySelectCount = false;
        $oField->required = true;
        $oField->defaultValue = 'test defaultValue';
        $oField->value = 'test value';
        $oField->errMessage = null;
        $oField->classNameError = null;
        $oField->valid = null;
        $oField->html = new stdClass();
        $oField->listData =  null;
    }
	function displayError($arr = 'test')
	{
		$style = "background-color:#000;padding:8px;margin:4px;font-size:12px;";
		$style .= "font-family:'Courier New';border:2px solid red;color:red;width:600px;";
		echo '<pre><div  style="' . $style . '">';
		print_r($arr);
		echo '</div></pre>';
	}
	//
	function trimArray($item)
	{
		return trim($item);
	}
	//
	function _isValid($oField)
	{
		if (empty ($_POST))
		{
			return $oField;
		}
		//
		$field = $oField->field;
		//
		if ($oField->value == '')
		{
			if ($oField->required)
			{
				$oField->isValid = false;
				$oField->errMessage = $this->errDefaultMessage;
				$oField->error = true;
			}
		}
		else
		{
			if (isset ($oField->regex))
			{
				//l'objet devient required car poss�de une r�gle
				$oField->required = true;
				if (preg_match('`(' . $oField->regex . ')`', $oField->value))
				{
					$oField->isValid = false;
					$oField->classNameError = 'textError';
					if (!isset ($oField->errMessage))
					{
						$errMessage = $this->errDefaultMessage;
					}
					$oField->errMessage = $oField->errMessage;
				}
				else
				{
					$this->aFieldToSave[$field] = $this->aPostedFieldToSave[$field];
				}
			}
			else
			{
				$this->aFieldToSave[$field] = $oField->value;
			}
		}
		//
		return $oField;
	}
	//
	//
	//	function _addViewProperties($oField)
	//	{
	//		$oField->label = @ $this->aoField[$oField->field]['label'];
	//		$oField->information = @ $this->aoField[$oField->field]['information'];
	//		$oField->displaySelectCount = @ $this->aoField[$oField->field]['displaySelectCount'];
	//		//
	//		foreach ($this->aoField[$oField->field] as $key => $value)
	//		{
	//			$oField-> $key = $value;
	//		}
	//		//
	//		return $oField;
	//		//
	//	}
	//
	function _getRow($oField)
	{
		//$oField = $this->_addViewProperties($oField);
		if (!isset ($oField->label))
		{
			Utils :: debug('Le label du champ "' . $oField->field . '" est manquant');
			exit;
		}
		//
		if (isset ($oField->information))
		{
			$row[] = '<tr><td colspan="3"><p class="information">www</p></td></tr>';
		}
		//
		$row[] = '<tr>';
		//
		$row[] = '<td class="label">';
		//
		$row[] = '<label>' . ucfirst($oField->label);
		if ($oField->required)
		{
			$requiredClass = 'required';
		}
		else
		{
			$requiredClass = 'notRequired';
		}
		$row[] = '</label>';
		$row[] = '<span class="' . $requiredClass . '">*</span>';
		$row[] = '</td>';
		//
		$row[] = '<td class="data">';
		$row[] = $oField->component;
		if (isset ($oField->listData) AND !isset ($oField->displaySelectCount))
		{
			$row[] = '(<span class="select_total_item">' . count($oField->listData) . '</span>)';
		}
		$row[] = '</td>';
		//
		$row[] = '</tr>';
		//
		if (!empty ($oField->classNameError))
		{
			$row[] = '<tr><td>&nbsp;</td><td><span class="error_form">^ ' . $oField->errMessage . '</span></td><td>&nbsp;</td></tr>';
		}
		//
		return implode("\n", $row);
	}
	//
	function getForm()
	{
		//$this->_addPropertiesToTableMasterInfo();
		//
		$oView = new View_Helper();
		//
		foreach ($this->aoField as $fieldName => $oField)
		{
			//
			$oField = $this->aoField[$fieldName];
			//
			$value = @ $this->aPostedFieldToSave[$fieldName];
			if (isset ($value))
			{
				$oField->value = $value;
			}
			else
			{
				if (empty ($oField->value))
				{
					$oField->value = $oField->defaultValue;
				}
				else
				{
					$oField->value = stripslashes($oField->value);
				}
			}
			//
			$oField = $this->_isValid($oField);
			//
			$nameComponent = $this->form_id . "[" . $oField->field . "]";
			//
            $oField->html->value=$oField->value;
			$tag = $oField->html->tag;
			switch ($tag)
			{
				//
				case 'input' :
					$oField->component = $oView->htmlElement('text','', $oField->html);
					$content[] = $this->_getRow($oField);
					break;
					//
				case 'textarea' :
					$oField->component = $oView->htmlElement('textarea', $oField->value, $array = array (
						'name' => $nameComponent,
						'class' => 'textarea ' . $oField->classNameError
					));
					$content[] = $this->_getRow($oField);
					break;
				case 'select' :
					//
					asort($oField->listData);
					$oField->component = $oView->htmlSelect($oField->value, array (
						'id' => $oField->field,
						'name' => $nameComponent,
						'class' => 'select '
					), $oField->listData, '>>');
					$content[] = $this->_getRow($oField);
					break;
					//
			} //fin switch
			//
		}
		//
		$oPost = $this->_postForm();
		//
		$submitButton = $oView->input('submit', $array = array (
			'name' => $this->form_id . '_submit_' . $this->action,
			'value' => 'Enregistrer',
			'class' => 'inputSubmit'
		));
		$view[] = '<div id="' . $this->container_form_id . '">';
		$view[] = '<fieldset><legend>' . $this->formLabel . '</legend>';
		$view[] = '<form  id="' . $this->form_id . '" action="" method="post">';
		//
		$view[] = '<table summary="" cellpadding="0" >' . implode('', $content) . '</table>';
		//
		$view[] = '<div id="submitContainer">';
		$view[] = '<span class="submitResult" style="text-align:left;display:' . $oPost->display . ';">';
		$view[] = '<span' . $oPost->styleClassName . '>&nbsp;</span>' . $oPost->submitResult;
		$view[] = '</span>';
		$view[] = $submitButton;
		$view[] = '</div>';
		//
		$view[] = '</form>';
		$view[] = '</fieldset>';
		$view[] = '</div>';
		return implode('', $view) . "<!--d86fc16f71db4749664da8847baa5deced59c04a-->";
	}
	function _postForm()
	{
		$oPost = new stdClass();
		$oPost->display = 'none';
		$oPost->styleClassName = '';
		$oPost->submitResult = '';
		//
		$isFormValid = true;
		if (!empty ($this->aPostedFieldToSave))
		{
			foreach ($this->aFieldRequired as $key => $value)
			{

			}
			//
			if (isset ($aRowNotValid))
			{
				$isFormValid = false;
			}
			//
			if ($isFormValid == true)
			{
				$display = 'block';
				//
				@ if (!isset($_SESSION)) session_start();
				if (isset ($_SESSION[md5(serialize($this->aFieldToSave))]))
				{
					$oPost->submitResult = $this->noModifiedForm;
					$oPost->styleClassName = ' class="form_not_valid"';
				}
				else
				{
					//
					$this->executeSqlAction($this->aFieldToSave);
					if ($this->action_result)
					{
						$oPost->submitResult = $this->actionSuccess;
						$oPost->styleClassName = ' class="form_valid"';
						//
						$_SESSION[md5(serialize($this->aFieldToSave))] = true;
					}
					else
					{
						$oPost->submitResult = $this->actionFailed;
						$oPost->styleClassName = ' class="form_not_valid"';
					}
					//
				}
			}
		}
		return $oPost;
	}
	function _addHtmlTag($oField)
    {

        //
        $oHtmlTag = new stdClass();
        $oHtmlTag->maxLength = $oField->maxLength;
        $oHtmlTag->componentName = $this->form_id . "[" . $oField->field . "]";
        //pour résoudre le problème lors du formatage du code par phpeclipse
        //default et class plante
        $className = 'class';
        switch ($oField->type)
        {
            //
            case 'integer' :
            case 'string' :
                //
                if ($oField->wrappedText)
                {
                    $oHtmlTag->tag = 'textarea';
                    $oHtmlTag-> $className = 'textarea';
                }
                else
                {
                    $oHtmlTag->tag = 'input';
                    $oHtmlTag-> $className = 'inputText';
                    $oHtmlTag->size = 35;
                    $oHtmlTag->type = 'text';
                }
                //
                break;
                //
            case 'list' :
                $oHtmlTag->tag = 'select';
                break;
                //
            default :
                break;
        }
        //
        return $oHtmlTag;
    }
	//
	//	function _addPropertiesToTableMasterInfo()
	//	{
	//		foreach ($this->tableObject as $field => $oField)
	//		{
	//			//champ non indispensable
	//			if ($oField->autoIncrement == 1) // OR $obj->primary
	//			{
	//				unset ($this->tableObject[$field]);
	//				continue;
	//			}
	//			if (array_key_exists($oField->field, $this->aTableChild))
	//			{
	//				$oField->type = 'list';
	//				$oField->listData = $this->formatTableChildData($oField);
	//				$tag = 'select';
	//			}
	//			//
	//			$oField->html = $this->_addHtmlTag($oField);
	//			//
	//			$this->tableObject[$field] = $oField;
	//		}
	//		//
	//	}
	//
} //fin classe HtmlForm
?>
</body>
</html>