<?php

/**
 * ObjBasephp short summary.
 *
 * ObjBasephp description.
 *
 * @version 1.0
 * @author recom3
 */
class ObjBase
{

	// This constructor is used by all the db object classes:

	public function __construct(array $options){

		foreach($options as $k=>$v){
			if(isset($this->$k)){
				$this->$k = $v;
			}
		}
	}
}