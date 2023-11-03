<?php

//Make signed routes in blade
use \Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;

//default all signed routes are active for 30 days
function signedRoute($name,$vars=array()) {
    return Url::temporarySignedRoute($name, now()->addDays(30), $vars);
    //return Url::signedRoute($name, $vars);
}

//specify specific link expiration
function temporarySignedRoute($name, $expires, $vars=array()) {
    return Url::temporarySignedRoute($name, $expires, $vars);
}

//Internal

function dq($q){
    dd(vsprintf(str_replace(array('?'), array('\'%s\''), $q->toSql()), $q->getBindings()));
}

//User
use App\Models\User;
function getUser($id){
    return User::where('id',$id)->first();
}

function getModules($user){
    $output = [];
    foreach($user->account->modules as $module) {
        if($module->access >= $user->role->level) {
            $output[]=$module->module;
        }
    }
    return json_encode($output);
}

function checkModulePermission($user, $module) {

    switch(config('app.status')){

        case 'down':
            Session::flash('alert-danger','System is currently in emergency maintenance mode, please refresh in a few minutes.');
            return $user->role->level <= 0;
            break;
        case 'maintenance':
            Session::flash('alert-info','System is currently in maintenance mode, certain modules may not be available, please refresh in a few minutes.');
            return $user->role->level <= 1;
            break;
        default:
            //normal mode
            if (in_array($module, json_decode(Session::get('modules')))) {
                return true;
            } else {
                return false;
            }
    }

}


// Main UI routines

/**
 * @var $path top level path component as string
 * @return CSS class name
 * @author Jamie Ross
 * Receives a path and see
 */
function isActive($path) {
    return Request::is($path . '*') ? 'active' :  '';
}

function selected($item,$current_selection){
    if($item == $current_selection){
        return 'selected="selected"';
    }
    return '';
}

function checked($item,$current_selection){
    if($item == $current_selection){
        return 'checked="checked"';
    }
    return '';
}

function arPosition($item,$array){
    for($i=0;$i<count($array);$i++){
        if($array[$i]->id==$item) return $i;
    }
    return 0;
}

function qs($arAdditions=[],$replaceQueryString=false) {
    if($replaceQueryString==true){
        $set=[];
    }else{
        $set = $_GET;
    }
    if(count($arAdditions)!=0) {
        foreach($arAdditions as $key=>$value) $set[$key] = $value;
        foreach($set as $key=>$value) $set[$key] = "$key=$value";
        return '?' . implode('&',$set);
    }else{
        if(count($set)!=0){
            return '?' . $_SERVER['QUERY_STRING'];
        }else{
            return '';
        }
    }
}

// File Routines
//
function underScoreToSpace($text){
    return str_replace("_"," ", $text);
}

function jsSafe($value) {
    return addslashes($value);
}

function getFileType($extension){
    switch ($extension){
        case '.doc':
        case '.docx':
            return "Word";
            break;
        case '.ppt':
        case '.pptx':
            return "Powerpoint";
            break;
        case '.xls':
        case '.xlsx':
        case '.csv':
            return "Excel";
            break;
        case '.pdf':
            return "Adobe PDF";
            break;
        case '.txt':
            return "Plain Text";
            break;
        case '.jpg':
        case '.jpeg':
            return "JPEG Image";
            break;
        case '.xlsx':
            return "PNG Image";
            break;
        case '.ytv':
            return "Youtube Video";
            break;
        default:
            return "$extension file";
            break;
    }
}

function arrayObjectvalue($arrayObject,$id,$property) {
    foreach($arrayObject as $obj){
        if($obj->id == $id){
            return $obj->$property;
        }
    }
    return '';
}

function getFileIcon($extension,$size='inherit'){
    switch ($extension){
        case '.doc':
        case '.docx':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-word word-color\"></i>";
            break;
        case '.ppt':
        case '.pptx':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-powerpoint powerpoint-color\"></i>";
            break;
        case '.xls':
        case '.xlsx':
        case '.csv':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-excel excel-color\"></i>";
            break;
        case '.pdf':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-pdf pdf-color\"></i>";
            break;
        case '.txt':
            return "<i style=\"font-size:$size\" class=\"fal fa-file \"></i>";
            break;
        case '.jpg':
        case '.jpeg':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-image \"></i>";
            break;
        case '.xlsx':
            return "PNG Image File";
            break;
        case '.ytv':
            return "<i style=\"font-size:$size\" class=\"fal fa-file-video \"></i>";
            break;
        default:
            return "<i style=\"font-size:$size\" class=\"fal fa-file \"></i>";
            break;
    }
}

/* PDF File Helpers */
function imgBase64($file) {
    $path = public_path().'/'.$file;
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

function cssToInline($file) {
    return file_get_contents(public_path(). '/'.$file);
}
