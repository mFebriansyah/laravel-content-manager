<?php

namespace MFebriansyah\LaravelContentManager\Models;

use DB;

class MainModel extends \MFebriansyah\LaravelAPIManager\Models\MainModel
{
    protected $primaryKey = 'id';
    static $columnLabel = 'id';
    public $rules = [];
    public $timestamps = false;
    public $hide = [];

    public function getSchemes(){
       return DB::select('show fields from '.$this->table);
    }

    public function setRules($schemes){
    	$rules = [];

    	foreach ($schemes as $key => $value) {
    		$validations = [];

    		if($value->Null == "NO" && $value->Key != "PRI"){
    			$validations[] = "required";
    		}

    		if(strpos($value->Type, 'int') !== false){
    			$validations[] = "integer";    			
    		}

            if($value->Default == "CURRENT_TIMESTAMP"){
                $validations[] = "date";
            }

    		$rules[$value->Field] = implode("|", $validations);
    	}

		$this->rules = array_merge($rules, $this->rules);

    	return $this;
    }

    public function getReference($field){
        $reference =  DB::select('
            select REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            from
                information_schema.key_column_usage
            where
                referenced_table_name is not null
                and column_name = "'.$field.'"
                and table_name = "'.$this->table.'"
                and table_schema = "'.$_ENV['DB_DATABASE'].'"
            ');

        if(isset($reference[0])){
            $reference = $reference[0];
        }

        return $reference;
    }
}