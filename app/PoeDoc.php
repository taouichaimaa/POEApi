<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PoeDoc extends Model
{
   $fullname;
$signature;
$email;
$message;
$phone;
$userip;
$pubdate;
public function __constructor($fullname,$signature,$email,$message,$phone,$userip,$pubdate){
	$this->fullname=$fullname;
	$this->signature=$signature;
	$this->email=$email;
	$this->message=$message;
	$this->phone=$phone;
	$this->userip=$userip;
	$this->pubdate=$pubdate;
}
function getsig(){
	return $this->signature;
}

}
