<?php

include ('user.class.php');

$userdata = array('username' => 'amineamine', 'user_firstname' => 'Jack', 'user_lastname' => 'Rouchiche', 'user_email' => 'rouchiche.amine1@gmail.com');

$userdata_update = array('user_firstname' => 'Jack', 'user_lastname' => 'Rouchiche', 'user_email' => 'rouchiche.amine1@gmail.com');

// $user = new User('rouchiche.amine1@gmail.com');

/* if ( $user->create($userdata, 4 ) )
    echo $user->get_('username'); */

// print_r(User::userExists('rouchiche.amine1@gmail.com'));
   
$user = new User('rouchiche.amine1@gmail.com');

//$user->update($userdata_update);

//$user->activate();


?>