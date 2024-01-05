<?php
class utilidades
{
    public static function log($tipo, $texto)
    { 
        $ficherolog = fopen('/var/log/minisnews/minisnews'.date("Ymd").'.log','a'); 
        fwrite($ficherolog, "[".date('Y-m-d h:i:s')."]\t$tipo\t$texto\r\n");
        fclose($ficherolog); 
    }
}
?>