<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use be\kunstmaan\multichain\MultichainClient;//this is the multichain/php library used, added a few tweaks and stuff, mainly the executeAPI for publishing.
use be\kunstmaan\multichain\MultichainHelper;
use Hashids\Hashids;
use App\Transaction;

class PostController extends Controller
{
    


/*store the post data into the blockchain 
you need to give me the file here with the original path
you can configure the post fields however you like.
*/

public function store(Request $request,Response $response){

    //stuff for multichain conf
        $iphost=config('chainconf.chain.rpchost');
$rpcport=config('chainconf.chain.rpcport');
$rpcname=config('chainconf.chain.rpcuser');
$rpcpass=config('chainconf.chain.rpcpassword');
$constring='http://'.$iphost.':'.$rpcport;
//hashing the file content 
$signature=md5_file($request->file('filep')->path());

/*
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    
} else {
   
}
*/


//okay let's talk about this for a bit, this is the blockchain client agent, you feed it the root node IP,name of the node and the password. all could be found in the multichain.conf file in the VM. You'll also have to add the IP of whatever you're running this API from into said conf file for it to access the blockchain.MAKE SURE TO DO SO. 
//PLEASE SEE THE CONFIG FILE

$client=new MultichainClient($constring,$rpcname,$rpcpass,3);

$clname=$request->input('fullname');
$mail=$request->input('email');
$phone=$request->input('phone');
$message=$request->input('message');
$pubdate=Carbon::now()->toDateTimeString();
/*this is the mac , here I use the IP,pease note that MAC is LOCAL only. so whatever trick you'll perform won't work unless both the client and the server are in the same LAN.
I will leave this here as well as give you an alternative option for mac */
$userip=$request->ip();
//feel free to change the variable names according to what you've used.
$dataarray= array("signature"=>$signature,"clname"=>$clname,"mail"=>$mail,"publishdate"=>$pubdate,"userip"=>$userip,"phone"=>$phone,"message"=>$message);
$dataJSON = json_encode($dataarray);
$dataBase64=base64_encode($dataJSON);
$dataHex=bin2hex($dataBase64);
$dataToReturn=array();
//get the transaction ID and publish data into the stream
$tx_id=$client->setDebug(true)->executeApi('publish',array("POE",$signature,$dataHex));
//getting the block info from the stream
$block_info = $client->setDebug(true)->executeApi('getwallettransaction', array($tx_id));
    $confirmations = $block_info['confirmations'];
    if($confirmations == 0){
        $blockhash = "NA";
        $blocktime = "NA";
    }
    else{
        $blockhash = $block_info['blockhash'];
        $blocktime = $block_info['blocktime'];
    }
    $dataToReturn['userip'] = $userip;
    $dataToReturn['signature'] = $signature;
    $dataToReturn['transaction_id'] = $tx_id;
    $dataToReturn['confirmations'] = $confirmations;
    $dataToReturn['blockhash'] = $blockhash;
    $dataToReturn['blocktime'] = $blocktime;
    $dataToReturn['name'] = $clname;
    $dataToReturn['email'] = $mail;
    $dataToReturn['message'] = $message;
    $dataToReturn['phone']=$phone;
    $dataToReturn['timestamp'] = date('g:i A \o\n l jS F Y \(\T\i\m\e\z\o\n\e \U\T\C\)', time());;
    /*this is the response you're gonna have to parse, it has all the info about the transaction , this only serve as a confirmation.
    If you want I can always only send the signature using this line:
    return response()->json(['signature'=>$dataToReturn['signature']]); */
return response()->json($dataToReturn);
}

/*  listing the last 10 transactions, I don't think you need it but it's here if you want it    */

public function generated(Request $request, Response $response){

    $iphost=config('chainconf.chain.rpchost');
$rpcport=config('chainconf.chain.rpcport');
$rpcname=config('chainconf.chain.rpcuser');
$rpcpass=config('chainconf.chain.rpcpassword');
$constring='http://'.$iphost.':'.$rpcport;

$client=new MultichainClient($constring,$rpcname,$rpcpass,3);
$data = $client->setDebug(true)->executeApi('liststreamitems', array("POE",false,10,-10));
$count=10;
 $data = array_reverse($data);
    $dataReturn = array();
     $listTrans=array();
    for($i=0;$i<$count;$i++)
    {
        $d = array();
        $d['tx_id'] = $data[$i]['txid'];
        $d['blocktime'] = date("Y-m-d H:i:s", $data[$i]['blocktime'])." UTC";
        $d['confirmations'] = $data[$i]['confirmations'];
        $dataReturn[$i] = $d;
$listTrans[]=$dataReturn[$i];
          }

   return response()->json($listTrans);
    
}

/*   
 Checking if the signature already exists in the blockchain. Returns a json that has all the informations about the file including the signature.make sure to hash it using the hashids package from laravel.
 If you're not working with any hash, you can simply ignore it and use the id directly.
 */


public function check($id){
    /* uncomment this if you're not going to hash the id in the url. 
    $hashids = new Hashids();
    $signature= $hashids->decode($id)[0];*/
/* and comment this */
$signature=$id;

    $iphost=config('chainconf.chain.rpchost');
$rpcport=config('chainconf.chain.rpcport');
$rpcname=config('chainconf.chain.rpcuser');
$rpcpass=config('chainconf.chain.rpcpassword');
$constring='http://'.$iphost.':'.$rpcport;

    $client=new MultichainClient($constring,$rpcname,$rpcpass,3);
 
    $data = $client->setDebug(true)->executeApi('liststreamkeyitems', array("POE", $signature));
    //if the file is not found , you can redirect the user to the publish form if you have it
    if(empty($data)){ 
        return response()->json(['message' => 'Not Found!'], 404);
}else{
    //sending the info about the file
     $data = array_reverse($data);
    
    $dataToReturn = array();
    foreach($data as $key => $value){
        $d = array();
        $d['signature'] = $signature;
        $d['transaction_id'] = $value['txid'];
        $d['confirmations'] = $value['confirmations'];
        $d['blocktime'] = date('g:i A \o\n l jS F Y \(\T\i\m\e\z\o\n\e \U\T\C\)', $value['blocktime']);
        //the user's info is stored as the hex form of the json data extracted
        //we decode it and then re-encode it along the previous block info to send it as a response
        $meta_data = json_decode(base64_decode(hex2bin($value['data'])));
    
        $d['name'] = $meta_data->clname;
        $d['email'] = $meta_data->mail;
        $d['message'] = $meta_data->message;
         $d['phone'] = $meta_data->phone;
          $d['userip'] = $meta_data->userip;
           $d['blockhash'] = null;
        $d['recorded_timestamp_UTC'] = $value['blocktime'];
        $d['timestamp'] = date('g:i A \o\n l jS F Y \(\T\i\m\e\z\o\n\e \U\T\C\)', $value['blocktime']);
      
       $dataret=json_encode($d);
        return response()->json($dataret);
    }

}

}


 }
