<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction{
  public  $tx_id;
   public  $blocktime;
   public  $confirmations;

    public function _construct($tid,$btime,$conf){

    	$this->tx_id=$tid;
    	$this->blocktime=$btime;
    	$this->confirmations=$conf;
    }
}
