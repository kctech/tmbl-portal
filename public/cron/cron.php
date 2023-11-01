<?php

include('Net/SSH2.php');
//define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);

$ssh = new Net_SSH2('ssh61.interdns.co.uk');

if (!$ssh->login('tmblportal', 'K6XKtvEotpMpki2YeXnJ')) {
    exit('Login Failed');
}
//echo $ssh->getLog();

//go to artisan dir
//below stop multiple daemons from running
//queue:restart actaully kills the process
//queue:work starts it again
//echo $ssh->exec('cd /home/tmblportal/public_html/app_core/ && php artisan schedule:run');
echo $ssh->exec('cd /home/tmblportal/public_html/app_core/ && php artisan queue:restart && php artisan queue:work --queue=adviseremails,clientemails --timeout=60 --sleep=5 --tries=3');

?>