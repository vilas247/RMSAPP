<?php
namespace App\Models;

use CodeIgniter\Model;

class Settings extends Model {

    protected $table = 'purplebricks_token_validation';	
    protected $primaryKey = 'validation_id';
	protected $returnType ='array';
	protected $useSoftDeletes =true;
	 
	protected $allowedFields =['*'];
		 
	protected $validationRules    =[];
	protected $validationMessages =[];
	protected $skipValidation     = false;

}