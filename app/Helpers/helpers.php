<?php

foreach (glob(getcwd()."/../app/Helpers/*.php") as $filename)
{
    if(stripos($filename,'helpers.php') === false){
        include $filename;
    }
}
