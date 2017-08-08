<?php

// PHP Compiler  by Antylevsky Aleksei (Next)

define("ColumnAttributesCount",5);

define("ColumnAttributeNull",1);
define("ColumnAttributeBinary",2);
define("ColumnAttributeUnsigned",4);
define("ColumnAttributeZerofill",8);
define("ColumnAttributeCounter",16);

/******************************************************************************/

class Database
{
  var $type="mysql";
  var $title="MySQL";
  var $connection;
  var $database;
  var $debug;

  function Database()
  {
    $this->connection=@mysql_connect(DatabaseHost,DatabaseUser,DatabasePass) or fatalError("fatal_connection");
    mysql_query("SET SESSION sql_mode=''");
    $queries=explodeSmart(",",DatabaseStartupQueries);
    foreach($queries as $query) mysql_query($query);
    $this->changeDatabase();
    $queries=DatabaseQueryLogEnabled?array():0;
    $this->debug=array("queries"=>$queries,"started"=>phpcmicrotime());
  }

  function internalNormalizeName($name)
  {
    static $cache;
    if(!isset($cache)) $cache=$this->getReservedWords();
    return in_array(phpcstrtoupper($name),$cache)?"`$name`":$name;
  }

  function internalNormalizeValue($value)
  {
    if(is_null($value)) return "NULL";
    if(is_bool($value)) return (int)$value;
    if(is_int($value) || is_float($value)) return $value;
    return slashes((string)$value);
  }

  function internalTranslateAttributes($attrs, $default="")
  {
    $result=array();
    if($attrs&ColumnAttributeBinary) $result[]="BINARY";
    if($attrs&ColumnAttributeUnsigned) $result[]="UNSIGNED";
    if($attrs&ColumnAttributeZerofill) $result[]="ZEROFILL";
    $result[]=($attrs&ColumnAttributeNull)?"NULL":"NOT NULL";
    if($default!="") $result[]="DEFAULT ".slashes($default);
    if($attrs&ColumnAttributeCounter) $result[]="AUTO_INCREMENT";
    return implode(" ",$result);
  }

  function internalTranslateTableType($type)
  {
    $types=array(
      "HEAP"=>"HEAP",
      "MEMORY"=>"HEAP",
      "MRG_MYISAM"=>"MERGE",
      "BERKELEYDB"=>"BDB",
      "INNODB"=>"InnoDB");
    return ifset($types[phpcstrtoupper($type)],"MyISAM");
  }

  function changeDatabase($name=DatabaseName)
  {
    @mysql_select_db($name) or fatalError("fatal_connection");
    if(defined("DatabaseCharset")) mysql_query("SET NAMES ".DatabaseCharset);
    $this->database=$name;
  }

  function getDebugInformation()
  {
    $this->debug["finished"]=phpcmicrotime();
    $this->debug["elapsed"]=$this->debug["finished"]-$this->debug["started"];
    return $this->debug;
  }

  function customQuery($query, $onlyfirst=false, $assoc=true)
  {
    $resource=mysql_query($query);
    if(!$resource) fatalError("fatal_wrongquery",array(),mysql_error(),$query);
    if(DatabaseQueryLogEnabled)
      $this->debug["queries"][]=$query; else $this->debug["queries"]++;
    if($resource===true) return false;
    $result=array();
    if($assoc)
      while($line=mysql_fetch_assoc($resource)) $result[]=$line;
      else while($line=mysql_fetch_row($resource)) $result[]=$line;
    mysql_free_result($resource);
    if($onlyfirst) return count($result)?$result[0]:false;
    return $result;
  }

  function customQuerySilent($query, &$error, $onlyfirst=false, $assoc=true)
  {
    $error=false;
    $resource=mysql_query($query);
    if(DatabaseQueryLogEnabled)
      $this->debug["queries"][]=$query; else $this->debug["queries"]++;
    if(!$resource) { $error=mysql_error(); return false; }
    if($resource===true) return false;
    $result=array();
    if($assoc)
      while($line=mysql_fetch_assoc($resource)) $result[]=$line;
      else while($line=mysql_fetch_row($resource)) $result[]=$line;
    mysql_free_result($resource);
    if($onlyfirst) return count($result)?$result[0]:false;
    return $result;
  }

  function customQueryBoolean($query)
  {
    $result=mysql_query($query);
    if(DatabaseQueryLogEnabled)
      $this->debug["queries"][]=$query; else $this->debug["queries"]++;
    return (boolean)$result;
  }

  function addColumn($table, $column, $type, $attrs, $position=false, $default="")
  {
    $fields=$this->getFieldsList($table);
    $attrs=$this->internalTranslateAttributes($attrs,$default);
    $query="ALTER TABLE $table ADD $column $type $attrs";
    if($position!==false)
      $query.=$position?" AFTER ".$fields[$position-1]:" FIRST";
    return $this->customQueryBoolean($query);
  }

  function addIndex($table, $name, $unique, $fields)
  {
    if(!is_array($fields)) $fields=array($fields);
    foreach($fields as $index=>$field)
      if(!is_array($field)) $fields[$index]=array("name"=>$field,"size"=>0);
    if($name=="PRIMARY") $name="PRIMARY KEY";
      else $name=($unique?"UNIQUE":"INDEX")." $name";
    $keystack=array();
    foreach($fields as $field)
      $keystack[]=$field["name"].($field["size"]?"($field[size])":"");
    $keystack=implode(",",$keystack);
    $query="ALTER TABLE $table ADD $name ($keystack)";
    return $this->customQueryBoolean($query);
  }

  function addLine($table, $values, $replace=false)
  {
    foreach($values as $field=>$value)
      $values[$field]=$this->internalNormalizeValue($value);
    $method=$replace?"REPLACE":"INSERT";
    $fields=implode(",",array_keys($values));
    $values=implode(",",$values);
    $query="$method INTO $table ($fields) VALUES ($values)";
    return $this->customQueryBoolean($query);
  }

  function addLineStrict($table, $values, $replace=false)
  {
    foreach($values as $field=>$value)
      $values[$field]=$this->internalNormalizeValue($value);
    $method=$replace?"REPLACE":"INSERT";
    $fields=implode(",",array_keys($values));
    $values=implode(",",$values);
    $query="$method INTO $table ($fields) VALUES ($values)";
    $this->customQuery($query);
    return true;
  }

  function modifyColumn($table, $column, $name, $type, $attrs, $default="")
  {
    $attrs=$this->internalTranslateAttributes($attrs,$default);
    $query="ALTER TABLE $table CHANGE $column $name $type $attrs";
    return $this->customQueryBoolean($query);
  }

  function modifyField($table, $field, $value, $conditions="")
  {
    return $this->modifyLine($table,array($field=>$value),$conditions);
  }

  function incrementField($table, $field, $conditions="", $delta=1)
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $delta=(int)$delta;
    $query="UPDATE $table SET $field=$field+$delta$conditions LIMIT 1";
    return $this->customQueryBoolean($query);
  }

  function decrementField($table, $field, $conditions="", $delta=1)
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $delta=(int)$delta;
    $query="UPDATE $table SET $field=$field-$delta$conditions LIMIT 1";
    return $this->customQueryBoolean($query);
  }

  function modifyLine($table, $values, $conditions="")
  {
    if(!count($values)) return false;
    $update="";
    foreach($values as $field=>$value)
      $update.=", $field=".$this->internalNormalizeValue($value);
    $update=substr($update,2);
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="UPDATE $table SET $update$conditions LIMIT 1";
    return $this->customQueryBoolean($query);
  }

  function modifyLines($table, $values, $conditions="")
  {
    if(!count($values)) return;
    $update="";
    foreach($values as $field=>$value)
      $update.=", $field=".$this->internalNormalizeValue($value);
    $update=substr($update,2);
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="UPDATE $table SET $update$conditions";
    $this->customQueryBoolean($query);
  }

  function deleteColumn($table, $column)
  {
    $query="ALTER TABLE $table DROP $column";
    return $this->customQueryBoolean($query);
  }

  function deleteIndex($table, $name)
  {
    $name=$name=="PRIMARY"?"PRIMARY KEY":"INDEX $name";
    $query="ALTER TABLE $table DROP $name";
    return $this->customQueryBoolean($query);
  }

  function deleteLine($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="DELETE FROM $table$conditions LIMIT 1";
    return $this->customQueryBoolean($query);
  }

  function deleteLines($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="DELETE FROM $table$conditions";
    $this->customQueryBoolean($query);
  }

  function clearTable($table)
  {
    $query="TRUNCATE TABLE $table";
    $this->customQueryBoolean($query);
  }

  function deleteTable($table)
  {
    $query="DROP TABLE $table";
    $this->customQueryBoolean($query);
  }

  function getVersion()
  {
    return mysql_get_server_info();
  }

  function getTableTypes()
  {
    $types="MyISAM,HEAP,MERGE,BDB,InnoDB";
    return explode(",",$types);
  }

  function getFieldTypes()
  {
    $types=
      "TINYINT,SMALLINT,MEDIUMINT,INT,BIGINT,FLOAT,DOUBLE,DECIMAL,CHAR,".
      "VARCHAR,TINYTEXT,TEXT,MEDIUMTEXT,LONGTEXT,TINYBLOB,BLOB,MEDIUMBLOB,".
      "LONGBLOB,DATE,TIME,DATETIME,YEAR,TIMESTAMP,ENUM,SET";
    return explode(",",$types);
  }

  function getNumericFieldTypes()
  {
    $types=
      "TINYINT,SMALLINT,MEDIUMINT,INT,BIGINT,FLOAT,DOUBLE,DECIMAL,DATE,TIME,".
      "DATETIME,YEAR,TIMESTAMP,ENUM,SET";
    return explode(",",$types);
  }

  function getReservedWords()
  {
    $words=
      "ACCESSIBLE,ACTION,ADD,ALL,ALTER,ANALYZE,AND,AS,ASC,ASENSITIVE,BEFORE,".
      "BETWEEN,BIGINT,BINARY,BIT,BLOB,BOTH,BY,CALL,CASCADE,CASE,CHANGE,CHAR,".
      "CHARACTER,CHECK,COLLATE,COLUMN,CONDITION,CONSTRAINT,CONTINUE,CONVERT,".
      "COUNT,CREATE,CROSS,CURRENT_DATE,CURRENT_TIME,CURRENT_TIMESTAMP,".
      "CURRENT_USER,CURSOR,DATABASE,DATABASES,DATE,DAY_HOUR,DAY_MICROSECOND,".
      "DAY_MINUTE,DAY_SECOND,DEC,DECIMAL,DECLARE,DEFAULT,DELAYED,DELETE,DESC,".
      "DESCRIBE,DETERMINISTIC,DISTINCT,DISTINCTROW,DIV,DOUBLE,DROP,DUAL,EACH,".
      "ELSE,ELSEIF,ENCLOSED,ENUM,ESCAPED,EXISTS,EXIT,EXPLAIN,FALSE,FETCH,".
      "FLOAT,FLOAT4,FLOAT8,FOR,FORCE,FOREIGN,FROM,FULLTEXT,GRANT,GROUP,".
      "HAVING,HIGH_PRIORITY,HOUR_MICROSECOND,HOUR_MINUTE,HOUR_SECOND,IF,".
      "IGNORE,IN,INDEX,INFILE,INNER,INOUT,INSENSITIVE,INSERT,INT,INT1,INT2,".
      "INT3,INT4,INT8,INTEGER,INTERVAL,INTO,IS,ITERATE,JOIN,KEY,KEYS,KILL,".
      "LEADING,LEAVE,LEFT,LIKE,LIMIT,LINEAR,LINES,LOAD,LOCALTIME,".
      "LOCALTIMESTAMP,LOCK,LONG,LONGBLOB,LONGTEXT,LOOP,LOW_PRIORITY,".
      "MASTER_SSL_VERIFY_SERVER_CERT,MATCH,MEDIUMBLOB,MEDIUMINT,MEDIUMTEXT,".
      "MIDDLEINT,MINUTE_MICROSECOND,MINUTE_SECOND,MOD,MODIFIES,NATURAL,NOT,".
      "NO_WRITE_TO_BINLOG,NULL,NUMERIC,ON,OPTIMIZE,OPTION,OPTIONALLY,OR,".
      "ORDER,OUT,OUTER,OUTFILE,PRECISION,PRIMARY,PROCEDURE,PURGE,RANGE,READ,".
      "READS,READ_ONLY,READ_WRITE,REAL,REFERENCES,REGEXP,RELEASE,RENAME,".
      "REPEAT,REPLACE,REQUIRE,RESTRICT,RETURN,REVOKE,RIGHT,RLIKE,SCHEMA,".
      "SCHEMAS,SECOND_MICROSECOND,SELECT,SENSITIVE,SEPARATOR,SET,SHOW,".
      "SMALLINT,SPATIAL,SPECIFIC,SQL,SQLEXCEPTION,SQLSTATE,SQLWARNING,".
      "SQL_BIG_RESULT,SQL_CALC_FOUND_ROWS,SQL_SMALL_RESULT,SSL,STARTING,".
      "STRAIGHT_JOIN,TABLE,TERMINATED,TEXT,THEN,TIME,TIMESTAMP,TINYBLOB,".
      "TINYINT,TINYTEXT,TO,TRAILING,TRIGGER,TRUE,UNDO,UNION,UNIQUE,UNLOCK,".
      "UNSIGNED,UPDATE,USAGE,USE,USING,UTC_DATE,UTC_TIME,UTC_TIMESTAMP,".
      "VALUES,VARBINARY,VARCHAR,VARCHARACTER,VARYING,WHEN,WHERE,WHILE,WITH,".
      "WRITE,X509,XOR,YEAR_MONTH,ZEROFILL";
    return explode(",",$words);
  }

  function getSupportedAttributes()
  {
    return ColumnAttributeNull+ColumnAttributeBinary+ColumnAttributeUnsigned+
      ColumnAttributeZerofill+ColumnAttributeCounter;
  }

  function getColumnType($meaning)
  {
    $types=array(
      "key"=>"INT",
      "int"=>"INT",
      "float"=>"DOUBLE",
      "input"=>"TINYTEXT",
      "datetime"=>"INT",
      "chooser"=>"INT",
      "yesno"=>"TINYINT(1)");
    return ifset($types[$meaning],"LONGTEXT");
  }

  function getColumnMeaning($type)
  {
    if($type=="TINYINT(1)") return "yesno";
    if(preg_match("{INT\b}",$type)) return "int";
    if(preg_match("{\b(FLOAT|DOUBLE|DECIMAL)\b}",$type)) return "float";
    if(preg_match("{\b(TEXT|MEDIUMTEXT|LONGTEXT)\b}",$type)) return "textarea";
    if(preg_match("{\b(BLOB|MEDIUMBLOB|LONGBLOB)\b}",$type)) return "textarea";
    return "input";
  }

  function getTablesList($full=false)
  {
    $result=array();
    $restricted=$full?array():explodeSmart(",",DatabaseRestrictedTables);
    $tables=$this->customQuery("SHOW TABLES",false,false);
    foreach($tables as $table) $result[]=$table[0];
    $result=array_diff($result,$restricted);
    sort($result);
    return $result;
  }

  function isTablePresent($table)
  {
    $tables=$this->getTablesList(true);
    return in_array($table,$tables);
  }

  function getTablesInformation($full=false)
  {
    $result=array();
    $restricted=$full?array():explodeSmart(",",DatabaseRestrictedTables);
    $status=$this->customQuery("SHOW TABLE STATUS");
    foreach($status as $table) if(!in_array($table["Name"],$restricted)) {
      if(isset($table["Engine"])) $table["Type"]=$table["Engine"];
      $result[$table["Name"]]=array(
        "type"=>$this->internalTranslateTableType($table["Type"]),
        "rows"=>(int)$table["Rows"],
        "size"=>(int)$table["Data_length"]+(int)$table["Index_length"],
        "datasize"=>(int)$table["Data_length"],
        "indexsize"=>(int)$table["Index_length"],
        "counter"=>(int)$table["Auto_increment"],
        "comment"=>$table["Comment"]);
    }
    ksort($result);
    return $result;
  }

  function getTableInformation($table)
  {
    $fields=$this->customQuery("SHOW COLUMNS FROM $table");
    $info=$this->customQuery("SHOW INDEX FROM $table");
    $result=array("columns"=>array(),"uniques"=>array(),"indexes"=>array());
    foreach($fields as $field) {
      if(phpcstrtoupper($field["Type"])=="TIMESTAMP"
        && $field["Default"]=="CURRENT_TIMESTAMP") $field["Default"]="";
      if(phpcstrtoupper($field["Type"])=="INT(11)") $field["Type"]="INT";
      $pattern="{^(\w+)\(?([^\)]*)\)?\s*(BINARY)?\s*(UNSIGNED)?\s*(ZEROFILL)?\$}i";
      preg_match($pattern,$field["Type"],$matches);
      while(count($matches)<=5) $matches[]="";
      $attrs=0;
      if(phpcstrtoupper($field["Null"])=="YES") $attrs+=ColumnAttributeNull;
      if($matches[3]!="") $attrs+=ColumnAttributeBinary;
      if($matches[4]!="") $attrs+=ColumnAttributeUnsigned;
      if($matches[5]!="") $attrs+=ColumnAttributeZerofill;
      if(phpcstrtoupper($field["Extra"])=="AUTO_INCREMENT")
        $attrs+=ColumnAttributeCounter;
      $column=array();
      $column["name"]=$field["Field"];
      $column["type"]=phpcstrtoupper($matches[1]);
      $column["size"]=$matches[2];
      $column["attrs"]=$attrs;
      $column["default"]=$field["Default"];
      $result["columns"][]=$column;
    }
    foreach($info as $item) {
      $name=$item["Key_name"];
      $index=$item["Seq_in_index"];
      if(isset($item["Index_type"])) $item["Comment"]=$item["Index_type"];
      if(phpcstrtoupper($item["Comment"])=="FULLTEXT") $item["Sub_part"]=0;
      if($item["Non_unique"])
        $place=&$result["indexes"]; else $place=&$result["uniques"];
      $entry=array("name"=>$item["Column_name"],"size"=>(int)$item["Sub_part"]);
      if(!isset($place[$name])) $place[$name]=array();
      $place[$name][$index]=$entry;
    }
    foreach($result["uniques"] as $name=>$key)
      { ksort($key); $result["uniques"][$name]=array_values($key); }
    foreach($result["indexes"] as $name=>$key)
      { ksort($key); $result["indexes"][$name]=array_values($key); }
    ksort($result["uniques"]);
    ksort($result["indexes"]);
    return $result;
  }

  function getFieldsList($table)
  {
    $result=array();
    $fields=$this->customQuery("SHOW COLUMNS FROM $table");
    foreach($fields as $field) $result[]=$field["Field"];
    return $result;
  }

  function isFieldPresent($table, $field)
  {
    $fields=$this->getFieldsList($table);
    return in_array($field,$fields);
  }

  function isLocalizedFieldPresent($table, $field)
  {
    $fields=$this->getFieldsList($table);
    $locales=explode(",",PhpcLocalesList);
    foreach($locales as $locale)
      if(in_array($field.$locale,$fields)) return true;
    return false;
  }

  function getKeyFields($table)
  {
    $info=$this->customQuery("SHOW INDEX FROM $table");
    $result=array();
    foreach($info as $line) if($line["Key_name"]=="PRIMARY")
      $result[$line["Seq_in_index"]]=$line["Column_name"];
    ksort($result);
    return array_values($result);
  }

  function getCounterValue()
  {
    return mysql_insert_id($this->connection);
  }

  function getLinesCount($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT COUNT(*) FROM $table$conditions";
    $line=$this->customQuery($query,true,false);
    return $line[0];
  }

  function getLinesFunction($table, $function, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT $function FROM $table$conditions";
    $line=$this->customQuery($query,true,false);
    return $line[0];
  }

  function getMinField($table, $field, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT MIN($field) FROM $table$conditions";
    $line=$this->customQuery($query,true,false);
    return is_null($line[0])?0:$line[0];
  }

  function getMaxField($table, $field, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT MAX($field) FROM $table$conditions";
    $line=$this->customQuery($query,true,false);
    return is_null($line[0])?0:$line[0];
  }

  function getField($table, $field, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT $field FROM $table$conditions LIMIT 1";
    $line=$this->customQuery($query,true);
    return ifset($line[$field]);
  }

  function getLine($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions LIMIT 1";
    return $this->customQuery($query,true);
  }

  function isLinePresent($table, $conditions="")
  {
    return $this->getLine($table,$conditions)!==false;
  }

  function getLines($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions";
    return $this->customQuery($query);
  }

  function getLinesRange($table, $offset, $count, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions LIMIT $offset,$count";
    return $this->customQuery($query);
  }

  function getOrderedLines($table, $order, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions ORDER BY $order";
    return $this->customQuery($query);
  }

  function getOrderedLinesRange($table, $order, $offset, $count, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions ORDER BY $order LIMIT $offset,$count";
    return $this->customQuery($query);
  }

  function getRandomLine($table, $conditions="")
  {
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions ORDER BY RAND() LIMIT 1";
    return $this->customQuery($query,true);
  }

  function getRandomLines($table, $limit=false, $conditions="")
  {
    $limit=$limit!==false?" LIMIT $limit":"";
    if($conditions!="") $conditions=" WHERE $conditions";
    $query="SELECT * FROM $table$conditions ORDER BY RAND()$limit";
    return $this->customQuery($query);
  }

  function parseSQL($query)
  {
    $result=array();
    $length=strlen($query);
    $position=0;
    while($position<$length) {
      $char=$query[$position];
      if($char=="#") {
        $eolnn=strpos($query,"\n",$position+1);
        $eolnr=strpos($query,"\r",$position+1);
        if($eolnn===false) $eolnn=$length;
        if($eolnr===false) $eolnr=$length;
        $position=min($eolnn,$eolnr);
        continue;
      }
      if($char=="\t" || $char=="\r" || $char=="\n" || $char==" ") {
        $position++;
        continue;
      }
      $start=$position;
      do {
        $colon=$position<$length?strpos($query,";",$position):false;
        $quote=$position<$length?strpos($query,"'",$position):false;
        if($colon===false) $colon=$length;
        if($quote===false) $quote=$length;
        if($colon<=$quote) {
          $result[]=array("offset"=>$start,"length"=>$colon-$start);
          $position=$colon+1;
          break;
        }
        $position=$quote+1;
        do {
          $quote=$position<$length?strpos($query,"'",$position):false;
          $slash=$position<$length?strpos($query,"\\",$position):false;
          if($quote===false) $quote=$length;
          if($slash===false) $slash=$length;
          if($quote<=$slash) { $position=$quote+1; break; }
          $position=$slash+2;
        } while(true);
      } while(true);
    }
    return $result;
  }

  function exportTableStructure($table)
  {
    $status=$this->customQuery("SHOW TABLE STATUS");
    $status=searchArrayLine($status,"Name",$table);
    $table=$this->internalNormalizeName($table);
    $fields=$this->customQuery("SHOW COLUMNS FROM $table");
    $info=$this->customQuery("SHOW INDEX FROM $table");
    $result=array();
    foreach($fields as $field) {
      $field["Field"]=$this->internalNormalizeName($field["Field"]);
      if(phpcstrtoupper($field["Type"])=="TIMESTAMP"
        && $field["Default"]=="CURRENT_TIMESTAMP") $field["Default"]="";
      preg_match("{^(\w+)(.*)\$}",$field["Type"],$matches);
      $field["Type"]=phpcstrtoupper($matches[1]).$matches[2];
      $field["Extra"]=phpcstrtoupper($field["Extra"]);
      if($field["Type"]=="INT(11)") $field["Type"]="INT";
      $field["Null"]=phpcstrtoupper($field["Null"])=="YES"?"NULL":"NOT NULL";
      $item="$field[Field] $field[Type] $field[Null]";
      if($field["Extra"]=="AUTO_INCREMENT") $item.=" AUTO_INCREMENT";
      if($field["Default"]) $item.=" DEFAULT ".slashes($field["Default"]);
      $result[]=$item;
    }
    $keys=array();
    foreach($info as $item) {
      $keyname=$this->internalNormalizeName($item["Key_name"]);
      $column=$this->internalNormalizeName($item["Column_name"]);
      $name=($item["Non_unique"]?"":"UNIQUE ")."KEY $keyname";
      if($item["Key_name"]=="PRIMARY") $name="PRIMARY KEY";
      if(isset($item["Index_type"])) $item["Comment"]=$item["Index_type"];
      if(phpcstrtoupper($item["Comment"])=="FULLTEXT") {
        $name="FULLTEXT KEY $keyname";
        $item["Sub_part"]="";
      }
      if($item["Sub_part"]!="") $item["Sub_part"]="($item[Sub_part])";
      if(!isset($keys[$name])) $keys[$name]=array();
      $keys[$name][$item["Seq_in_index"]]="$column$item[Sub_part]";
    }
    foreach($keys as $key=>$parts) {
      ksort($parts);
      $result[]="$key (".implode(",",$parts).")";
    }
    if(isset($status["Engine"])) $status["Type"]=$status["Engine"];
    $type=$this->internalTranslateTableType($status["Type"]);
    $type=$type=="MyISAM"?"":" TYPE=$type";
    $comment=$status["Comment"];
    $comment=$comment==""?"":" COMMENT=".slashes($comment);
    $result=implode(",\r\n  ",$result);
    $result=
      "DROP TABLE IF EXISTS $table;\r\n".
      "CREATE TABLE $table (\r\n  $result)$type$comment;\r\n";
    return $result;
  }

  function exportTableLine($table, $values, $assoc=false, $replace=false)
  {
    foreach($values as $field=>$value) {
      if(is_null($value)) { $values[$field]="NULL"; continue; }
      if(!isTrueInteger($value)) $values[$field]=slashes($value);
    }
    $method=$replace?"REPLACE":"INSERT";
    $fields=$assoc?" (".implode(",",array_keys($values)).")":"";
    $values=implode(",",$values);
    $result="$method INTO $table$fields VALUES ($values);\r\n";
    return $result;
  }

  function changeTableType($table, $type)
  {
    $query="ALTER TABLE $table TYPE=$type";
    return $this->customQueryBoolean($query);
  }

  function changeTableComment($table, $comment)
  {
    $query="ALTER TABLE $table COMMENT=".slashes($comment);
    return $this->customQueryBoolean($query);
  }

  function renameTable($table, $name)
  {
    $query="ALTER TABLE $table RENAME TO $name";
    return $this->customQueryBoolean($query);
  }

  function optimizeTable($table)
  {
    $query="OPTIMIZE TABLE $table";
    $this->customQuery($query);
    $query="ALTER TABLE $table AUTO_INCREMENT=1";
    $this->customQueryBoolean($query);
  }

  function arrangeTable($table)
  {
    $keys=$this->getKeyFields($table);
    if(!count($keys)) return;
    $query="ALTER TABLE $table ORDER BY ".implode(",",$keys);
    $this->customQuery($query);
  }

  function repairTable($table)
  {
    $query="REPAIR TABLE $table";
    $this->customQuery($query);
  }

  function startTransaction()
  {
    $query="BEGIN";
    $this->customQuery($query);
  }

  function commitTransaction()
  {
    $query="COMMIT";
    $this->customQuery($query);
  }

  function rollbackTransaction()
  {
    $query="ROLLBACK";
    $this->customQuery($query);
  }

  function lockTable($table)
  {
    $query="LOCK TABLES $table WRITE";
    $this->customQuery($query);
  }

  function unlockTable($table)
  {
    $query="UNLOCK TABLES";
    $this->customQuery($query);
  }
}

?>
