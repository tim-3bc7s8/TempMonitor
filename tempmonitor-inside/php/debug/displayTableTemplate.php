<?php

// logging system
require_once '../KLogger.php';





class Table_Template
{
	var $tableName;
	var $columns;
	var $rows;
	
	public function setTableName($tableName)
	{
		$this->tableName = $tableName;
	}
	
	public function setColumns($columns)
	{
		$this->columns = $columns;
	}
	
	public function setRows($rows)
	{
		$this->rows = $rows;
	}
	
	public function render($template)
	{
		ob_start();
		include($template);
		$buf = ob_get_contents();
		ob_end_clean();
		return $buf;
	}
}