<?php
require(APPPATH.'libraries/stripe-php-master/init.php');

$publishableKey="pk_test_51JJuZaSCzXhOINgBPtVtgzzhcrasV3kXPK6WLmhF0SchNwafg8bQVeGKJkQWpZIuxOHeKQNe5kttT2DF9Txj4Fua00aIJb9IW6";

$secretKey="sk_test_51JJuZaSCzXhOINgBPI0GvDcv7wWLV4gyEvOcmExxediDVHoZOXrwYBZz3Ur2PkTe83bAw00vgTxp6YNFX7ornZ3T00oNlE0IOz";

\Stripe\Stripe::setApiKey($secretKey);
?>