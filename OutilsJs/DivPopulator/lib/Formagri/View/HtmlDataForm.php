<?php
error_reporting(E_ALL);
/**
   * Class  HtmlDataForm
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
require_once DEF_FORMAGRI_LIB . 'Db/ZSimpleORM.php';
require_once 'Helper.php';
//
class HtmlDataForm extends ZSimpleORM
{
	var $debug = false;
	//
	var $aPostedFieldToSave = array ();
	var $aFieldToSave = array ();
	var $aFieldRequired = array ();
	var $tableObject;
	var $form_id = null; //
	var $container_form_id = null; //
	var $actionSuccess = null;
	var $actionFailed = null;
	var $action = null; //insert, update
	var $tableMasterPrimaries = array ();
	var $formLabel = null;
	var $formLabelUpdate = null;
	//
	var $aTableChild = array ();
	var $errDefaultMessage = 'Champ obligatoire';
	var $aParamView = array ();
	/**
	 *
	 var $aParamView = array (
		'un champ de la table' => array (
			'label' => 'un libelle',
			'regex' => 'une expression reguliere exemple [^0-9]',
			'errMessage' => 'Le code doit &ecirc;tre un entier',
	        'classNameError' => 'textError',
			'information' => ">> une information"
		)
	);
	*/
	//
	function HtmlDataForm()
	{
		//
		@ if (!isset($_SESSION)) session_start();
		//
		parent :: ZSimpleORM();
		//
		if (empty ($this->form_id) OR preg_match('/[^a-z_]/', $this->form_id))
		{
			Utils :: debug("Le formulaire doit avoir un id valide (les lettres et underscores uniquement)");
			exit;
		}
		//
		$this->init();
		//
		$this->form_id = $this->form_id . '_form';
		if (!empty ($_POST))
		{
			//
			$this->aPostedFieldToSave = array_map(array (
				$this,
				'cleanArray'
			), $_POST[$this->form_id]);
		}
		//
	}
	//
	function init()
	{
		$this->tableObject = $this->getTableObject();
		$this->tableChildData = $this->getTableChildData();
		$fields = array_keys($this->tableObject);
		foreach ($this->tableObject as $key => $oField)
		{
			if ($oField->required)
			{
				$this->aFieldRequired[$key] = null;
			}
		}
		$akeysSetup = array_merge($this->tableMasterPrimaries, array_keys($this->aParamView));
		$aKeysOmitted = array_diff(array_keys($this->aFieldRequired), $akeysSetup);
		if (count($aKeysOmitted) > 0)
		{
			$err = "Ces champs sont requis et doivent &ecirc;tre d&eacute;clar&eacute;s";
			$err .= "\n&raquo;&nbsp;" . implode("\n&raquo;&nbsp;", $aKeysOmitted);
			$this->displayError($err);
		}
	}
	//
	function _addPropertiesToTableMasterInfo()
	{
		foreach ($this->tableObject as $field => $oField)
		{
			//champ non indispensable
			if ($oField->autoIncrement == 1) // OR $obj->primary
			{
				unset ($this->tableObject[$field]);
				continue;
			}
			if (array_key_exists($oField->field, $this->aTableChild))
			{
				$oField->type = 'list';
				$oField->listData = $this->formatTableChildData($oField);
				//$tag = 'select';
			}
			//
			$oField->html = $this->_addHtmlTag($oField);
			//
			$this->tableObject[$field] = $oField;
			//
		}
		//
	}
	function formatTableChildData($oField)
	{
		//redéfinie
		return array ();
	}
	//
	function displayError($arr = 'test')
	{
		$style = "background-color:#000;padding:8px;margin:4px;font-size:12px;";
		$style .= "font-family:'Courier New';border:2px solid red;color:red;width:600px;";
		echo '<pre><div  style="' . $style . '">';
		print_r($arr);
		echo '</div></pre>';
	}
	//
	function cleanArray($item)
	{
		$item = preg_replace("/[[:space:]]+/", ' ', $item);
		$item = preg_replace("/[\"]+/", '"', $item);
		$item = preg_replace("/[\']+/", "'", $item);
		$item = trim($item);
		return $item;
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
		$classNameError = @ $this->aParamView[$field]['classNameError'];
		if (!isset ($classNameError))
		{
			$classNameError = 'textError';
		}
		if ($oField->value == '')
		{
			if ($oField->required)
			{
				$oField->valid = false;
				$oField->errMessage = $this->errDefaultMessage;
				$oField->html->classNameError = $classNameError;
			}
		}
		else
		{
			$regex = @ $this->aParamView[$field]['regex'];
			if (!isset ($regex))
			{
				if ($oField->type == 'integer')
				{
					$regex = '[^0-9]';
				}
			}
			if (!empty ($regex))
			{
				//l'objet devient required car poss�de une r�gle
				$oField->required = true;
				if (preg_match('`(' . $regex . ')`', $oField->value))
				{
					$oField->valid = false;
					$oField->html->classNameError = $classNameError;
					$errMessage = @ $this->aParamView[$field]['errMessage'];
					if (!isset ($errMessage))
					{
						$errMessage = $this->errDefaultMessage;
					}
					$oField->errMessage = $errMessage;
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
	function _addViewProperties($oField)
	{
		$oField->label = @ $this->aParamView[$oField->field]['label'];
		$oField->information = @ $this->aParamView[$oField->field]['information'];
		$oField->displaySelectCount = @ $this->aParamView[$oField->field]['displaySelectCount'];
		//
		foreach ($this->aParamView[$oField->field] as $key => $value)
		{
			$oField-> $key = $value;
		}
		//
		return $oField;
		//
	}
	//
	function _getRow($oField)
	{
		$oField = $this->_addViewProperties($oField);
		if (!isset ($oField->label))
		{
			Utils :: debug('Le label du champ "' . $oField->field . '" est manquant');
			exit;
		}
		//
		if (isset ($oField->information))
		{
			$row[] = '<tr><td colspan="3"><span id="information_'.$oField->field.'" class="information">' . ucfirst($oField->information) . '</span></td></tr>';
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
		$row[] = '<span class="' . $requiredClass . '">&nbsp;*</span>';
		$row[] = '</td>';
		//
		$row[] = '<td class="data">';
		$row[] = $oField->component;
		if (isset ($oField->listData) AND !isset ($oField->displaySelectCount))
		{
			$row[] = '&nbsp;(<span class="select_total_item">' . count($oField->listData) . '</span>)';
		}
		$row[] = '</td>';
		//
		$row[] = '</tr>';
		//
		if (isset ($oField->valid) AND $oField->valid == false)
		{
			$row[] = '<tr><td>&nbsp;</td><td><span class="error_form">^ ' . $oField->errMessage . '</span></td><td>&nbsp;</td></tr>';
		}
		//
		return implode("", $row);
	}
	//
	function _addHtmlTag($oField)
	{
		//
		$oHtmlTag = new stdClass();
		$oHtmlTag->id = $oField->field;
		//$oHtmlTag->classNameError=$oField->classNameError;
		$oHtmlTag->maxLength = $oField->maxLength;
		$oHtmlTag->name = $this->form_id . "[" . $oField->field . "]";
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
		//
	}
	//
	function getForm()
	{
		$this->_addPropertiesToTableMasterInfo();
		//
		$oView = new View_Helper();
		//
		$aFieldLabel = array_keys($this->aParamView); //
		foreach ($aFieldLabel as $fieldName)
		{
			//
			$oField = $this->tableObject[$fieldName];
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
			$tag = $oField->html->tag;
			switch ($tag)
			{
				//
				case 'input' :
					$oField->component = $oView->htmlElement('text', $oField->value, $oField->html);
					$content[] = $this->_getRow($oField);
					break;
					//
				case 'textarea' :
					$oField->component = $oView->htmlElement('textarea', $oField->value, $oField->html);
					$content[] = $this->_getRow($oField);
					break;
				case 'select' :
					//
					$oField->component = $oView->htmlSelect($oField->value, array (
						'id' => $oField->field,
						'name' => $oField->html->name,
						'class' => trim('select ' . @ $oField->html->classNameError)
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
		return implode('', $view) . "<!--d86fc16f71db4749664da8847baa5deced59c04a-->";
	}
	//
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
			$oPost->display = 'block';
			foreach ($this->aFieldRequired as $key => $value)
			{
				if ($key == array_search($key, $this->tableMasterPrimaries))
				{
					continue;
				}
				if (!array_key_exists($key, $this->tableMasterPrimaries) AND empty ($this->aFieldToSave[$key]))
				{
					$aRowNotValid[$key] = true;
				}
			}
			//
			if (isset ($aRowNotValid))
			{
				$isFormValid = false;
			}
			//
			if ($isFormValid == true)
			{
				//
				@ if (!isset($_SESSION)) session_start();
				if (isset ($_SESSION[md5(serialize($this->aFieldToSave))]))
				{
					$oPost->submitResult = $this->noModifiedForm;
					$oPost->styleClassName = ' class="form_not_valid"';
				}
				else
				{
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
	//
} //fin classe HtmlDataForm
?>
