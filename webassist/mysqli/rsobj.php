<?php
// version 1.14
class WA_MySQLi_RS  {
	public function __construct($name,$conn,$maxRows=0) {
	  $this->Name = $name;
	  $this->Connection = $conn;
	  $this->CurrentPage = $_SERVER['PHP_SELF'];
	  $this->Statement = "";
	  $this->ParamTypes = "";
	  $this->ParamValues= array();
	  $this->Results = array();
	  $this->Index = 0;
	  $this->MaxRows = $maxRows;
	  $this->PageNum = isset($_GET['pageNum_'.$name])?$_GET['pageNum_'.$name]:0;
	  $this->PrevPage = max(0, $this->PageNum - 1);
	  $this->NextPage = 0;
	  $this->StartLimit = $this->PageNum * $this->MaxRows;
	  $this->StartRow = 0;
	  $this->LastRow = 0;
	  $this->TotalRows = 0;
	  $this->TotalPages = 0;
	  $this->QueryString = "";
	}
	
	private function setQueryString() {
	  $queryString = "";
	  if (!empty($_SERVER['QUERY_STRING'])) {
		$params = explode("&", $_SERVER['QUERY_STRING']);
		$newParams = array();
		foreach ($params as $param) {
		  if (stristr($param, "pageNum_".$this->Name) == false && 
			  stristr($param, "totalRows_".$this->Name) == false) {
			array_push($newParams, $param);
		  }
		}
		if (count($newParams) != 0) {
		  $queryString = "&" . htmlentities(implode("&", $newParams));
		}
	  }
	  $queryString = sprintf("&totalRows_".$this->Name."=%d%s", $this->TotalRows, $queryString);
	  $this->QueryString = $queryString;
	}
	public function getFirstPageLink() {
	  return $this->CurrentPage . "?pageNum_" . $this->Name . "=0" . $this->QueryString;	
	}
	public function getPrevPageLink() {	  	
	  return $this->CurrentPage . "?pageNum_" . $this->Name . "=" . $this->PrevPage . $this->QueryString;
	}
	public function getNextPageLink() {
	  return $this->CurrentPage . "?pageNum_" . $this->Name . "=" . $this->NextPage . $this->QueryString;
	}
	public function getLastPageLink() {
	  return $this->CurrentPage . "?pageNum_" . $this->Name . "=" . $this->TotalPages . $this->QueryString;	
	}
	public function setQuery($statement) {
	  $this->Statement = $statement;	
	}
	public function bindParam($paramType,$paramValue,$paramDefault) {
	  if (empty($paramValue)) $paramValue = $paramDefault;
	  $paramArray = array($paramValue);
	  if (strpos($paramType,"l")) {
		$paramType = substr($paramType,0,1);
		$paramArray = preg_split("/\s*\,\s*/", $paramValue);
	  }
	  for ($x=0; $x<sizeof($paramArray); $x++) {
		$paramValue = $paramArray[$x];
		if ($paramType == "t") {
		  $paramType = "s";
		  $hasTime = strpos($paramValue," ") !== false;
		  $paramValue = strtotime($paramValue);
		  if ($hasTime) {
			$paramValue = date('Y-m-d H:i:s',$paramValue);
		  } else {
			$paramValue = date('Y-m-d',$paramValue);
		  }
		} else if ($paramType == "c") {
		  $paramType = "s";
		  $paramValue = "%" . $paramValue . "%";
		} else if ($paramType == "b") {
		  $paramType = "s";
		  $paramValue = $paramValue . "%";
		} else if ($paramType == "e") {
		  $paramType = "s";
		  $paramValue = "%" . $paramValue;
		} 
		if (sizeof($paramArray) > 1 && $x == 0) {
		  $sqlParts = explode("?",$this->Statement);
		  if (!preg_match("/\(\s*$/",$sqlParts[sizeof($this->ParamValues)]) && !preg_match("/^\s*\)/",$sqlParts[sizeof($this->ParamValues)+1])) {
			$sqlParts[sizeof($this->ParamValues)] =  $sqlParts[sizeof($this->ParamValues)] . "(";
			$sqlParts[sizeof($this->ParamValues)+1] = ")" . $sqlParts[sizeof($this->ParamValues)+1];
		  }
		  $this->Statement = implode("?",$sqlParts);
		}
		if ($x>0) {
		  $sqlParts = explode("?",$this->Statement);
		  $sqlParts[sizeof($this->ParamValues)] = ", ?" . $sqlParts[sizeof($this->ParamValues)];
		  $this->Statement = implode("?",$sqlParts);
		}
		$this->ParamTypes .= $paramType;	
		$this->ParamValues[] = $paramValue;	
	  }
	}
	public function getParams() {
	  return array_merge(array($this->ParamTypes), $this->ParamValues); 
	}
	public function atEnd() {
	  return $this->Index == sizeof($this->Results); 
	}
	public function addRow($row) {
	  $this->Results[] = $row;
	}
	public function getColumnVal($col) {
	  $colVal = "";
	  if (isset($this->Results[$this->Index]) && isset($this->Results[$this->Index][$col])) $colVal = $this->Results[$this->Index][$col];
	  return $colVal;
	}
	public function movePrevious() {
	  if ($this->Index>0) {
	    $this->Index--;
	    return true;
	  }
	  return false;
	}
	public function moveNext() {
	  $this->Index++;
	  if (sizeof($this->Results) > $this->Index) {
	    return true;
	  }
	  $this->Index = sizeof($this->Results);
	  return false;
	}
	public function moveFirst() {
	  $this->Index = 0;
	}
	public function execute() {
	  $statement = $this->Statement;
	  if ($this->MaxRows && !isset($_GET['totalRows_'.$this->Name])) {
	    $statement = preg_replace('/^select /i',"SELECT SQL_CALC_FOUND_ROWS ",$statement);
	  }
	  if ($this->MaxRows) $statement .= " LIMIT ".$this->StartLimit.",".$this->MaxRows;
	  $query = $this->Connection->Prepare($statement);
	  if (!$query) {
		die("Error in SQL syntax");
		return;  
	  }
	  if ($this->ParamTypes) call_user_func_array(array($query, "bind_param"),$this->paramRefs($this->getParams()));
	  $query->execute();
	  if (method_exists($query,'get_results')) {
        $result = $query->get_result();
        while ($rows = $result->fetch_array(MYSQLI_ASSOC)) {
          $this->addRow($rows);
        }
	  } else {
	    $result = $this->wa_mysqli_stmt_get_result($query);
	    while ($rows = $this->wa_mysqli_result_fetch_assoc($result)) {
		  $this->addRow($rows);
	    }
	  }
	  if ($this->MaxRows) {
		if (isset($_GET['totalRows_'.$this->Name])) {
		  $this->TotalRows = intval($_GET['totalRows_'.$this->Name]);
		} else {
		  $stmt = $this->Connection->prepare("SELECT FOUND_ROWS()");
		  $stmt->execute();
		  $stmt->bind_result($num);
		  $stmt->fetch();
		  $this->TotalRows = $num;
		  $stmt->close();
		}
		$this->TotalPages = ceil($this->TotalRows/$this->MaxRows)-1;
	  } else {
        $this->TotalRows = sizeof($this->Results);
	  }
	  if ($this->TotalRows < $this->StartRow) {
		  $this->StartRow = $this->TotalRows;
	  } else if ($this->TotalRows > 0) {
		  $this->StartRow = ($this->PageNum * $this->MaxRows) + 1;
	  }
	  $this->LastRow = min($this->StartRow + $this->MaxRows - 1, $this->TotalRows);
	  if ($this->MaxRows == 0) $this->LastRow = $this->TotalRows;
	  $this->NextPage = min($this->TotalPages, $this->PageNum + 1); 
	  $this->setQueryString();
      $query->close();
	}
	public function paramRefs($arr) {
	  if (strnatcmp(phpversion(),'5.3') >= 0)
	  {
		$refs = array();
		foreach($arr as $key => $value)
		  $refs[$key] = &$arr[$key];
		return $refs;
	  }
	  return $arr;
	}
	public function debugSQL() {
	  $debugStatement = $this->Statement;
	  $paramTypes = $this->ParamTypes;
	  for ($x=0; $x<sizeof($this->ParamValues); $x++) {
	    $pos = strpos($debugStatement,"?");
		$thisType = substr($paramTypes,$x,1);
        if ($pos !== false) {
          $debugStatement = substr_replace($debugStatement,(($thisType=="s")?"'":"") . $this->ParamValues[$x] . (($thisType=="s")?"'":""),$pos,strlen("?"));
        }
	  }
	  return $debugStatement;
	}
	private function wa_mysqli_stmt_get_result($stmt)  {
	  $metadata = mysqli_stmt_result_metadata($stmt);
	  $ret = (object) array('nCols'=>'0', 'fields'=>array(), 'stmt'=>'');
	  $ret->nCols = mysqli_num_fields($metadata);
	  $ret->fields = mysqli_fetch_fields($metadata);
	  $ret->stmt = $stmt;
	  mysqli_free_result($metadata);
	  return $ret;
	}
	private function wa_mysqli_result_fetch_assoc(&$result) {
	  $ret = array();
	  $code = "mysqli_stmt_store_result(\$result->stmt); return mysqli_stmt_bind_result(\$result->stmt ";
	  for ($i=0; $i<$result->nCols; $i++) {
		$ret[$result->fields[$i]->name] = NULL;
		$code .= ", \$ret['" .$result->fields[$i]->name ."']";
	  };
	  $code .= ");";
	  if (!eval($code)) { return NULL; };
	  if (!mysqli_stmt_fetch($result->stmt)) { return NULL; };
	  return $ret;
	}
}
?>