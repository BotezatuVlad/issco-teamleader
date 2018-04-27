<?php

namespace Issco\Accounts;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Customers extends Model
{
    public $id;
	public $name;
	public $since;
    public $revenue;
    
    public function validation()
    {
        $this->validate(
            new InclusionIn(
                [
                    'field'  => 'name',
                    'message' => 'Customer name',
                ]
            )
        );
        
		if($this->validationHasFailed() === true)
		{
            return false;
        }
    }
}