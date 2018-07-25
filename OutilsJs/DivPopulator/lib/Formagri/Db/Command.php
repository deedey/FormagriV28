<?php
/**
   * Class  Db_Command
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@educagri.fr>
   * @package
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 20 déc. 07 16:11:51
   * @copyright
   */
error_reporting(E_ALL);
//
class Db_Command
{
	//
	var $debug = DEF_MODE_DEBUG;
	//
	var $host = DEF_HOST;
	var $database = DEF_DATABASE;
	var $user = DEF_USER;
	var $pass = DEF_PASSWORD;
	//
	var $select;
	var $sqlJoin = '';
	var $where = '';
	var $limit = '';
	var $orderBy = '';
	//
	var $sqlAction = null;
	function Db_Command()
	{
		$this->_connect();
	}
	//
	/**
	* @description que fait la fonction
	* @param type du parametre, son nom
	* @return type du parametre, son nom
	* @access type d'accès
	*/
	function _connect()
	{
		$db_link = @ mysql_connect($this->host, $this->user, $this->pass);
		if (!is_resource($db_link))
		{
			$this->debug(DEF_NO_CONNEXION, $this->debug);
			exit;
		}
	}
	function select($from, $fields = '*')
	{
		//
		$select[] = 'SELECT';
		if (isset ($fields) AND is_array($fields))
		{
			$fields = implode("\n\t, ", $fields);
		}
		$select[] = "\t" . $fields;
		$select[] = 'FROM';
		$select[] = "\t" . $from;
		$this->select = implode(" \n ", $select);
		//return $this->select;
	}
	//
	function sqlJoin($TableToJoin, $TableToJoinWith, $keyJointure, $joinType = 'INNER', $tabulationNumber = 0)
	{
		$tabulation = str_repeat("\t", $tabulationNumber);
		//
		$innerjoin[] = "\n" . $tabulation . strtoupper($joinType) . " JOIN";
		$innerjoin[] = $tabulation . $TableToJoin;
		$innerjoin[] = "\t  " . $tabulation . 'ON';
		$innerjoin[] = "\t" . $tabulation . $TableToJoinWith . '.' . $keyJointure . '=' . $TableToJoin . '.' . $keyJointure . ' ';
		$this->sqlJoin = implode(" \n ", $innerjoin);
		//
		return $this->sqlJoin;
	}
	//
	function where($clauseWhere)
	{
		$this->where = "\nWHERE";
		$this->where .= "\t" . $clauseWhere;
	}
	//
	function limit($limitFrom = 0, $rowNumber = null)
	{
		if (isset ($rowNumber))
		{
			$this->limit = "\n" . "LIMIT\t" . $limitFrom . ',' . $rowNumber;
		}
	}
	//
	function orderBy($aListField, $order = 'ASC')
	{
		if (!empty ($aListField))
		{
            if(is_array($aListField))
            {
                $aListField=implode(",", $aListField);
            }
			$this->orderBy = "\nORDER BY\t" .$aListField . ' ' . $order;
		}
	}
	//
	function update($table)
	{
		$update[] = 'UPDATE';
		$update[] = " " . $table;
		$update[] = 'SET';
		$this->update = implode(" \n ", $update);
		//return $this->select;
	}
	function set($aSet)
	{
		//
		$set = array ();
		foreach ($aSet as $key => $value)
		{
			$set[] = " " . $key . "='" . $this->quote($value) . "'";
		}
		$this->set = "\t" . implode("\n\t, ", $set);
		//return $this->select;
	}
	function updatePrepare()
	{
		$query = "\n" . $this->update;
		$query .= "\n" . $this->set;
		$query .= $this->where;
		//
		$this->setQuery($query);
	}
	function selectPrepare()
	{
		$query = "\n" . $this->select;
		$query .= "\n" . $this->sqlJoin;
		$query .= " \n " . $this->where;
		$query .= " \n " . $this->orderBy;
		$query .= " \n " . $this->limit;
		//
		$this->setQuery($query);
	}
	function setQuery($query)
	{
		//
		$this->query = $query;
		//
		//        //
		//        $this->select=null;
		//        $this->innerjoin=null;
		//        $this->where=null;
		//        $this->orderBy=null;
		//        $this->query=null;
		//
	}
	function getQuery()
	{
		return $this->query;
		//
	}
	//
	function execute($debugQuery = false)
	{
		if ($debugQuery)
		{
			$this->debug($this->getQuery());
		}
		if (mysql_select_db($this->database))
		{
			//
			$result = mysql_query($this->getQuery());
			//
			if ($result)
			{
				//
				switch ($this->sqlAction)
				{
					//
					case 'add' :
						return mysql_insert_id();
						break;
						//
					case 'update' :
						return mysql_affected_rows();
						break;
						//
					case 'delete' :
						break;
						//
					default :
						$arr = array ();
						if ($result)
						{
							while ($row = mysql_fetch_object($result))
							{
								$arr[] = $row;
							}
						}
						return $arr;
						break;
				} //fin switch
				//
			}
			else
			{
				$this->debug(mysql_error(), $this->debug);
				exit;
			}
			//
		}
		else
		{
			//
			$this->debug(mysql_error(), $this->debug);
			exit;
			//
		}
	}
	//
	function quote($item)
	{

        return $item;
	}
	//
	function debug($strOrObject = 'hello', $debug)
	{
		if ($debug == true)
		{
			$sql_command_to_replace = array (
				'SELECT',
				'DISTINCT',
				'COLUMNS',
				'FROM',
				'LIMIT',
				'LIKE',
				'WHERE',
				'GROUP',
				'HAVING',
				'ORDER BY',
				'ASC',
				'DESC',
				'INNER JOIN',
				'LEFT JOIN',
				'RIGHT JOIN',
				'FULL JOIN',
				'CROSS JOIN',
				'NATURAL JOIN'
			);
			//
			foreach ($sql_command_to_replace as $value)
			{
				$sql_command_to_replaceBy[] = '<span style="color:green;">' . $value . '</span>';
			}
			//
			//settype($strOrObject,'array');
			//
			if (is_array($strOrObject))
			{
				$strOrObject = str_replace($sql_command_to_replace, $sql_command_to_replaceBy, $strOrObject);
			}
			$style = "text-align:left;background-color:#000;padding:8px;margin:4px;font-size:11px;color:red;";
			$style .= "font-family:georgia;border:2px solid red;";
			echo '<pre style="' . $style . '">';
			print_r($strOrObject);
			echo '</pre>';
		}
	}
} //fin classe Db_Command
//
//
//
?>
