<?php
/**
 * Copyright (c) 2012 Debarshi Kr. Banerjee, Laddu, Madcaplaughs.
 * debarshi dot ban at gmail dot com
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
 
 /*
  Version : 0.1
  Beta  
 */
if (!function_exists('json_decode')) {
  die('ESPN needs the JSON PHP extension.');
}

class Espn {
  
  protected static $apiV = "v1";
  protected static $apiR = "?apikey=";
  protected static $apiKey = "yvy6hftkmz7pw26ygax4a45z";
  protected static $uriapi = "http://api.espn.com/";  
  protected static $rawop = false;

  public static function setApiV($v){
    if (!empty($v)) self::$apiV = $v; 
  }
  
  public static function getApiV(){
    return self::$apiV;
  }
  
  public static function setApiKey($apikey){
    if (!empty($apikey)) self::$apiKey = $apikey;
  }
  
  public static function setOutputToRaw(){
    self::$rawop = true;
  }
  
  public static function resetOutput(){
    self::$rawop = false;
  }
  
  public static function listAllSupportedSports(){
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . self::$apiR . self::$apiKey);
    if (self::$rawop) return $res;
    $toreturn = array(); $i=0;
    foreach($res->{'sports'} as $alls){
      $toreturn[$i]['name'] = $alls->{'name'};
      if (isset($alls->{'id'})) $toreturn[$i]['id'] = $alls->{'id'}; else $toreturn[$i]['id'] = "";
      $i++;
    }
    return $toreturn;
  }

  public static function listAllLeagues($sport){
    $sport=strtolower($sport);
    if (self::$rawop) return $res;
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $sport . "/" . "leagues" . self::$apiR . self::$apiKey);
    $toreturn = array();
    for($i=0;$i < count($res->{'sports'}[0]->{'leagues'});$i++){
      $toreturn[$i]['name'] = $res->{'sports'}[0]->{'leagues'}[$i]->{'name'};
      $toreturn[$i]['abbr'] = $res->{'sports'}[0]->{'leagues'}[$i]->{'abbreviation'};
      $toreturn[$i]['id'] = $res->{'sports'}[0]->{'leagues'}[$i]->{'id'};
      if (isset($res->{'sports'}[0]->{'leagues'}[$i]->{'shortName'})) $toreturn[$i]['shortName'] = $res->{'sports'}[0]->{'leagues'}[$i]->{'shortName'}; else $toreturn[$i]['shortName'] = "";
    }
    return $toreturn;
  }

  public static function getTeams($sport, $league)
  {

    if( empty($sport) || empty($league) )
      return false;

    $sport = strtolower($sport);
    $league = strtolower($league);

    $get = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" .$sport. "/" .$league. "/teams" . self::$apiR . self::$apiKey);

    $teams = $get->sports[0]->leagues[0]->teams;

    $i = 0;

    $data = array();

    while( $i < count( $teams ) ):

      $team = $teams[$i];

      $data[] = array(
        'id'        =>  $team->id,
        'name'      =>  $team->name,
        'location'  =>  $team->location
      );

      $i++;

    endwhile;

    if( is_null($data) )
      return false;

    return $data;
  }
  
  public static function listAllDivisons($sport,$labbr){
    $sport=strtolower($sport); $labbr=strtolower($labbr);
    return espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $sport . "/" . $labbr . "/" . "groups" . self::$apiR . self::$apiKey);
  }
  
  public static function getHeadLines(){
    $numargs = func_num_args();
    $param = '';
    if ($numargs > 0){
      $arg_list = func_get_args();
      for($i=0; $i < $numargs; $i++)
        $param .= strtolower($arg_list[$i])."/";
    }
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $param  . "news/headlines" . self::$apiR . self::$apiKey);
    if (self::$rawop)
      return $res;
    else
      return espn::streamline($res);
  }
  
  public static function getTopHeadLines(){
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . "news/headlines/top" . self::$apiR . self::$apiKey);
    if (self::$rawop)
      return $res;
    else
      return espn::streamline($res);
  }
  
  /*
    Expects the first three params to be year, month and date
  */
  public static function getNewsOn(){
    $numargs = func_num_args();
    if ($numargs < 3) return false;
    $param = ''; $arg_list = func_get_args();
    if ($numargs > 3){      
      for($i=3; $i < $numargs; $i++)
        $param .= strtolower($arg_list[$i])."/";
    }
    $yr = $arg_list[0]; $mon = $arg_list[1]; $dat = $arg_list[2];
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $param  . "news/dates/" . $yr . $mon . $dat . self::$apiR . self::$apiKey);
    if (self::$rawop)
      return $res;
    else
      return espn::streamline($res);
  }
  
  public static function getNews(){
    $numargs = func_num_args();
    $param = '';
    if ($numargs > 0){
      $arg_list = func_get_args();
      for($i=0; $i < $numargs; $i++)
        $param .= strtolower($arg_list[$i])."/";
    }
    $res = espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $param  . "news" . self::$apiR . self::$apiKey);

    if (self::$rawop)
      return $res;
    else
      return espn::streamline($res);
  }
  
  public static function getTeamNews($sport, $league, $team_id)
  {
    $res =  espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . $sport . "/" .$league. "/teams" . "/" . $team_id . "/news/" . self::$apiR . self::$apiKey);

    if ( self::$rawop ):
      return $res;
    else:
      return espn::streamline($res);
    endif;
  }

  public static function getAtheleteNews($id){
    $res =  espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . "athletes" . "/" . $id . "/news/" . self::$apiR . self::$apiKey);
    if (self::$rawop)
      return $res;
    else
      return espn::streamline($res);
  }
  
  public static function getStory($id){
    return espn::espn_get(self::$uriapi . self::$apiV . "/" . "sports" . "/" . "news" . "/" . $id . self::$apiR . self::$apiKey);
  } 
  
  protected static function espn_get($uri){
    return json_decode(file_get_contents($uri));
  }
  
  // this is for the news api
  protected static function streamline($res){
    $toreturn=array();
    for($i=0; $i < count($res->{'headlines'}); $i++){
      if (isset($res->{'headlines'}[$i]->{'headline'})) $toreturn[$i]['headline'] = $res->{'headlines'}[$i]->{'headline'}; else $toreturn[$i]['headline'] = "";
      $toreturn[$i]['keywords'] = $res->{'headlines'}[$i]->{'keywords'};
      $toreturn[$i]['lastModified'] = $res->{'headlines'}[$i]->{'lastModified'};
      $toreturn[$i]['web_link'] = $res->{'headlines'}[$i]->{'links'}->{'web'}->{'href'};
      $toreturn[$i]['mobile_link'] = $res->{'headlines'}[$i]->{'links'}->{'mobile'}->{'href'};
      $toreturn[$i]['id'] = $res->{'headlines'}[$i]->{'id'};
      if (isset($res->{'headlines'}[$i]->{'title'})) $toreturn[$i]['title'] = $res->{'headlines'}[$i]->{'title'}; else $toreturn[$i]['title'] = "";
      if (isset($res->{'headlines'}[$i]->{'description'})) $toreturn[$i]['description'] = $res->{'headlines'}[$i]->{'description'}; else $toreturn[$i]['description'] = "";
    }
    return $toreturn;
  }
}
?>