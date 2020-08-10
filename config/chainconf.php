<?php
return [

/*You can always change this to the values in your own multichain.conf in your own
blockchain. Please note that you will have to properly configure your params.dat file and multichain.conf file in your multichain root node server and add few exceptions to inbound and external traffic and ports( if its running on a VM) . 
Make sure to add your server's IP (both public and private ) to the multichain.conf .
You need to set the rpcallowip runtime parameter for the node to allow incoming API connections from your IP or in case of them being on the same subnet, allow for that subnet.
ie:

if all the nodes are on the same subnet (192.168.1), you could use:
-rpcallowip=192.168.1.0/24
so that any 192.168.1.x node could send a request to any 192.168.1.y other node.
*/
// configuration for a Multichain blockchain
'chain' =>[
'name' => 'POEchain',  // name to display in the web interface
'rpchost' =>'192.168.125.1', // IP address of MultiChain node
'rpcport' => 2760,            // default-rpc-port from params.dat
'rpcuser' => 'multichainrpc', // username for RPC from multichain.conf
'rpcpassword'=>'913Zk6d1axxFiD8e65zZR8QNTcYpwR2t79YTFHitYdYy'// password for RPC 


]
]

?>