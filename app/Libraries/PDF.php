<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Storage;

class PDF {

    private static $execution_path = '/usr/local/bin/phantomjs.new /var/local/rasterize.js';
    // URL output_file paper_format

    public static function make($view_name,$data,$file_name=null){
        $tmp_path = Storage::disk('tmp')->getAdapter()->getPathPrefix();
        $private_path = Storage::disk('private')->getAdapter()->getPathPrefix();

        $view = view($view_name,$data);
        
        $tmp_hash = md5($view.microtime(true));
        Storage::disk('tmp')->put($tmp_hash.'.htm', $view );
        
        $params = "\"file://{$tmp_path}{$tmp_hash}.htm\" \"{$private_path}{$tmp_hash}.pdf\" A4";
        shell_exec( PDF::$execution_path . " {$params}" );
        
        Storage::disk('tmp')->delete($tmp_hash.'.htm');
        if(Storage::disk('private')->exists($tmp_hash.'.pdf')){
            return $tmp_hash . '.pdf';
        }else{
            return false;
        }
        

    }
}
