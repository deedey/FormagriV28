<?php
error_reporting(E_ALL);
/**
   * Class  ZSimpleForm
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package
   * @description génération d'un objet table servant à la création d'une class ORM
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 24 déc. 07 15:46:11
   * @todo
   */
require_once 'Command.php';
//
class ZSimpleORM extends Db_Command
{
	var $debug = false;
	//
	//	var $host ;
	//	var $user ;
	//	var $pass;
	//	var $database;
	//
	var $tableMaster = null;
	var $_tableMasterData = array ();
	/**
	 * @description liste des clés associe&eacute;s si pas de cl&eacute; primaire
	 */
	var $_aTableMasterPrimary = array ();
	var $aTableChild = array ();
	var $record_insert_id = null;
	var $action_result = false;
	//
	function ZSimpleORM()
	{
		//
		parent :: Db_Command();
		//
	}
	//
	function getTableChildData()
	{
		$tableChildData = array ();
		//
		$aField = array ();
		foreach ($this->aTableChild as $key => $tableChild)
		{
			$oTableChild = (object) $tableChild;
			$aField[$oTableChild->tableChild] = $this->getTableData($oTableChild->tableChild);
		}
		$tableChildData = @ $aField;
		return $tableChildData;
	}
	//
	function getTableData($table)
	{
		$this->setQuery('SELECT * FROM ' . $table . ' limit 0,10'); //  COLUMNS FROM ' . $this->tableMaster);
		return $this->execute();
	}
	//
	function executeSqlAction($RowToInsert)
	{
		//
		$this->sqlAction = $this->action;
		switch ($this->action)
		{
			//
			case 'add' :
				return $this->addRow($RowToInsert);
				break;
				//
			case 'update' :
				$this->updateRow($RowToInsert);
				break;
				//
			default :
				//$this->_debug('action inconnue');
				break;
		} //fin switch
	}
	//
	function updateRow($RowToInsert)
	{
		$this->update($this->tableMaster);
		$this->set($RowToInsert);
		$query = ' WHERE ';
		//
		foreach ($this->whereParam as $key => $value)
		{
			if (!empty ($value))
			{
				$query .= " " . $key . "='" . addslashes($value) . "' \n";
			}
		}
		$this->where = $query;
		$this->updatePrepare();
		$this->action_result = true;
		return $this->execute();
	}
	function addRow($RowToInsert)
	{
		$query = 'INSERT INTO ' . $this->tableMaster . "\n";
		$query .= "  (" . implode(",", array_keys($RowToInsert)) . ")\n";
		//
		//
		foreach ($RowToInsert as $key => $value)
		{
			$values[] = "'" . addslashes($value) . "'";
		}
		$query .= " VALUES (" . implode(',', $values) . ")\n";

		$this->setQuery($query);
		$this->record_insert_id = $this->execute();
		if (isset ($this->record_insert_id))
		{
			$this->action_result = true;
			return $this->record_insert_id;
		}
		//
	}
	//
	function _getTableMasterData()
	{
		if ($this->action == 'update')
		{
			$this->select($this->tableMaster);
			$this->selectPrepare();
			$aRows = $this->execute();
			if (!empty ($aRows))
			{
				return $aRows[0];
			}
		}
	}
	//
	function getTableObject()
	{
		$tableFull = array ();
		$rowData = $this->_getTableMasterData();
		//
		$this->setQuery('SHOW COLUMNS FROM ' . $this->tableMaster);
		//		if (GlobalVars :: isCached($this->tableMaster) == true)
		//		{
		//			//return (array) GlobalVars :: getFromCache($this->tableMaster);
		//		}
		//
		$aField = $this->execute();
		//
		foreach ($aField as $field)
		{
			//
			$aField = array ();
			$aField['table'] = $this->tableMaster;
			$aField['primary'] = null;
			$aField['autoIncrement'] = null;
			$aField['listData'] = null;
			$aField['required'] = null;
			$aField['defaultValue'] = null;
			$aField['value'] = null;
			//
			$field = (array) $field;
			$aField['field'] = $field['Field'];
			//
			$aField['type'] = $field['Type'];
			//
			$aField = $this->addType($aField);
			if ($field['Key'] == 'PRI')
			{
				$aField['primary'] = true;
			}
			//
			if ($field['Extra'])
			{
				$aField['autoIncrement'] = true;
			}
			//
			if (strstr($field['Type'], 'enum'))
			{
				$str = str_replace(array (
					'enum',
					'(',
					')'
				), '', $field['Type']);
				$enumList = explode("','", trim($str, "'"));
				$aField['listData'] = array_combine($enumList, $enumList);
			}
			//
			$length = preg_replace("/[^0-9,]/", '', $field['Type']);
			$tab = explode(',', $length); //cas du type décimal
			$aField['maxLength'] = $tab[0];
			if ($field['Null'] === 'YES')
			{
			}
			else
			{
				$aField['required'] = true;
			}
			//
			if ($field['Default'])
			{
				$aField['defaultValue'] = $field['Default'];
			}
			//
			$aField['value'] = @ $rowData-> $field['Field'];
			//
			$tableFull[$field['Field']] = (object) $aField;
			//
		}
		//
		//GlobalVars :: setInCache($this->tableMaster, $tableFull);
		//
		return $tableFull;
	}
	//
	function addType($aField)
	{
		$type = $aField['type'];
		$strpos = strpos($type, '(');
		if ($strpos)
		{
			$type = substr($type, 0, $strpos);
		}
		$aField['typeSql'] = $type;
		//
		switch ($type)
		{
			case 'tinyint' :
			case 'smallint' :
			case 'mediumint' :
			case 'int' :
			case 'bigint' :
			case 'timestamp' :
				$aField['wrappedText'] = false;
				$aField['type'] = 'integer';
				break;
				//
			case 'float' :
			case 'double' :
			case 'decimal' :
				$aField['wrappedText'] = false;
				$aField['type'] = 'real';
				break;
				//
			case 'date' :
			case 'time' :
			case 'year' :
			case 'datetime' :
				break;
				//
			case 'varchar' :
			case 'char' :
			case 'tinytext' :
				$aField['wrappedText'] = false;
				$aField['type'] = 'string';
				break;
			case 'mediumtext' :
			case 'longtext' :
			case 'text' :
			case 'tinyblob' :
			case 'blob' :
			case 'mediumblob' :
			case 'longblob' :
				$aField['wrappedText'] = true;
				$aField['type'] = 'string';
				break;
			case 'enum' :
				$aField['type'] = 'list';
			case 'set' :
				break;
		}
		return $aField;
	}
} //fin classe ZSimpleORM
//------------------------------------------------------------------------------//
//$oZSimpleORM=new ZSimpleORM ();
//require_once '../views/HtmlDataForm.php';
?>
