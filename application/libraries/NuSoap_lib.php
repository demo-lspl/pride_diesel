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

class NuSoap_lib{
          //function Nusoap_lib(){
          //function Nusoap_library(){
          function __construct(){
               require_once(str_replace("\\","/",APPPATH).'libraries/NuSOAP/lib/nusoap'.EXT); //If we are executing this script on a Windows server
          }

      }
?>