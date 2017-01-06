<?php
/**
 * Created by PhpStorm.
 * User: UTHEO
 * Date: 26/11/2016
 * Time: 10:13 SA
 */
namespace API1Bundle\Reference;

class Reference {

    // Ham random chuoi ki tu
   public function randomString($length)
    {
        $keys = array_merge(range(0, 9), range('a', 'z'));

        $key = "";
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[mt_rand(0, count($keys) - 1)];
        }
        return $key;
    }

    public function getDate(){

       $ngay = date (d, $timestamp = 'time()');
       $thang = date (m, $timestamp = 'time()');
       $nam = date (y, $timestamp = 'time()');
       $thang = date (m, $timestamp = 'time()');
        $gio = date (H, $timestamp = 'time()');
        $giay = date (s, $timestamp = 'time()');


   }
}