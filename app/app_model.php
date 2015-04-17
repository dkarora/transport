<?php
App::import('Sanitize');

class AppModel extends Model
{
	// we should be able to log and contain every model
	var $actsAs = array('Loggable', 'Containable');
	
    /**
     * Get Enum Values
     * Snippet v0.1.3
     * http://cakeforge.org/snippet/detail.php?type=snippet&id=112
     *
     * Gets the enum values for MySQL 4 and 5 to use in selectTag()
     */
    function getEnumValues($columnName=null, $respectDefault=false)
    {
        if ($columnName==null) { return array(); } //no field specified
		
        //Get the name of the table
        $db =& ConnectionManager::getDataSource($this->useDbConfig);
        $tableName = $db->fullTableName($this, false);
		
        //Get the values for the specified column (database and version specific, needs testing)
        $result = $this->query("SHOW COLUMNS FROM {$tableName} LIKE '{$columnName}'");
		
        //figure out where in the result our Types are (this varies between mysql versions)
        $types = null;
        if     ( isset( $result[0]['COLUMNS']['Type'] ) ) { $types = $result[0]['COLUMNS']['Type']; $default = $result[0]['COLUMNS']['Default']; } //MySQL 5
        elseif ( isset( $result[0][0]['Type'] ) )         { $types = $result[0][0]['Type']; $default = $result[0][0]['Default']; } //MySQL 4
        else   { return array(); } //types return not accounted for
		
        //Get the values
        $values = explode("','", preg_replace("/(enum)\('(.+?)'\)/","\\2", $types) );
		
        if($respectDefault){
                $assoc_values = array("$default"=>Inflector::humanize($default));
                foreach ( $values as $value ) {
                        if($value==$default){ continue; }
                        $assoc_values[$value] = Inflector::humanize($value);
                }
        }
        else{
                $assoc_values = array();
                foreach ( $values as $value ) {
                        $assoc_values[$value] = Inflector::humanize($value);
                }
        }
		
        return $assoc_values;
		
    } //end getEnumValues
	
	function _htmlEscape($results, $fields)
	{
		if ($fields === false || empty($fields))
			return $results;
		
		foreach ($results as $key => $value)
		{
			foreach ($fields as $f)
			{
				if (!empty($results[$key][$this->alias][$f]))
					$results[$key][$this->alias][$f] = Sanitize::html($results[$key][$this->alias][$f]);
			}
		}
		
		return $results;
	}
}
?>
