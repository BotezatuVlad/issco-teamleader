<?php

namespace Issco\Store;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Model\Validator\InclusionIn;

class Products extends Model
{
    public $id;
	public $description;
	public $category;
    public $price;
    
    public function validation()
    {
        $this->validate(
            new InclusionIn(
                [
                    'field'  => 'description',
                    'message' => 'Product description',
                ]
            )
        );

        $this->validate(
            new Uniqueness(
                [
                    'field'   => 'category',
                    'domain' => ['1', '2'],
                ]
            )
        );

        // Year cannot be less than zero
		if($this->price < 0)
		{
            $this->appendMessage(new Message('The price cannot be less than zero'));
        }

        // Check if any messages have been produced
		if($this->validationHasFailed() === true)
		{
            return false;
        }
    }
}