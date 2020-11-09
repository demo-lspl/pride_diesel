<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 *  ======================================= 
 *  Author     : Jagdish 
 *  License    : Protected 
 *  Email      : dev@lastingerp.com 
 * 
 *  ======================================= 
 */

class Efs_api {
	
	public function api_init($url,$fields=null,$method=null,$file=null){
    // required:
    //      url     = include http or https 
    // optionals:
    //      fields  = must be array (e.g.: 'field1' => $field1, ...)
    //      method  = "GET", "POST"
    //      file    = if want to download a file, declare store location and file name (e.g.: /var/www/img.jpg, ...)
    // please create 'cookies' dir to store local cookies if neeeded

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_HEADER, 0);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );

    if($file!=null){
        if (!curl_setopt($ch, CURLOPT_FILE, $file)){ // Handle error
                die("curl setopt bit the dust: " . curl_error($ch));
        }
        //curl_setopt($ch, CURLOPT_FILE, $file);
        //$timeout= 3600*60*24;
    }
	$timeout= 3600*60*24;
	//curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if($fields!=null){
        $postvars = http_build_query($fields); // build the urlencoded data
        //$postvars = $fields; // build the urlencoded data
		//print_r($postvars);
        if($method=="POST"){
            // set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        }
        if($method=="GET"){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $url = $url.'?'.$postvars;
            //$url = $url.'?'.urldecode($postvars);
            //$url = $url.'?clientId='.$postvars.'&request=all';
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    $content = curl_exec($ch);
    if (!$content){
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        die("cURL request failed, error = {$error}; info = " . print_r($info, true));
    }
    if(curl_errno($ch)){
        echo 'error:' . curl_error($ch);
    } else {
		$output = simplexml_load_string($content,'SimpleXMLElement',LIBXML_NOCDATA) or die("Error: Cannot create object");
		//print_r($postvars);echo"<br>";
		
		return $output;
    }
    curl_close($ch);		
	}
}
?>