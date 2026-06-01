<?php
session_start();
$dataFile=__DIR__.'/nav-data.json';
function loadData(){global $dataFile;if(file_exists($dataFile)){$d=json_decode(file_get_contents($dataFile),true);if($d&&is_array($d['groups']??null))return $d;}return defaultData();}
function defaultData(){return['adminHash'=>'44f61792d66021c0030fa37dca5162871345c525f61984b88fa1af16d8117672','siteName'=>'E家导航','siteDesc'=>'E家导航 - 最实用的经验，分享最需要的你','groups'=>[['id'=>'default','name'=>'默认','emoji'=>'📌','bookmarks'=>[]]]];}
function saveData($d){global $dataFile;file_put_contents($dataFile,json_encode($d,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),LOCK_EX);}
function resp($ok,$msg='') { echo json_encode($ok?['success'=>true]:['success'=>false,'message'=>$msg]); exit; }
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$action=$_GET['action']??$_POST['action']??'';
if($action==='login'){$pw=$_POST['password']??'';$d=loadData();if($pw&&$pw===($d['adminHash']??defaultData()['adminHash'])){$_SESSION['admin']=true;resp(true);}resp(false,'密码错误');}
if($action==='logout'){unset($_SESSION['admin']);header('Location: nav-page.php');exit;}
if(!($_SESSION['admin']??false))resp(false,'未登录');
if($action==='getGroup'){$gid=$_GET['groupId']??'';foreach(loadData()['groups'] as $g)if($g['id']===$gid){echo json_encode(['success'=>true,'id'=>$g['id'],'name'=>$g['name'],'emoji'=>$g['emoji']]);exit;}resp(false,'分组不存在');}
if($action==='getBookmark'){$gid=$_GET['groupId']??'';$bid=$_GET['bookmarkId']??'';foreach(loadData()['groups'] as $g)if($g['id']===$gid)foreach($g['bookmarks'] as $b)if($b['id']===$bid){echo json_encode(['success'=>true,'id'=>$b['id'],'name'=>$b['name'],'url'=>$b['url'],'desc'=>$b['desc']??'','favicon'=>$b['favicon']??'']);exit;}resp(false,'书签不存在');}
if($action==='saveGroup'){$gid=$_POST['groupId']??'';$nm=trim($_POST['name']??'');$em=$_POST['emoji']??'📌';if(!$nm)resp(false,'分组名称不能为空');$d=loadData();if($gid){foreach($d['groups'] as &$g)if($g['id']===$gid){$g['name']=$nm;$g['emoji']=$em;break;}unset($g);}else{$d['groups'][]=['id'=>uniqid('g'),'name'=>$nm,'emoji'=>$em,'bookmarks'=>[]];}saveData($d);resp(true);}
if($action==='deleteGroup'){$gid=$_POST['groupId']??'';$d=loadData();$d['groups']=array_values(array_filter($d['groups'],function($g)use($gid){return $g['id']!==$gid;}));saveData($d);resp(true);}
if($action==='saveBookmark'){$gid=$_POST['groupId']??'';$bid=$_POST['bookmarkId']??'';$url=trim($_POST['url']??'');$nm=trim($_POST['name']??'');$ds=trim($_POST['desc']??'');$fv=$_POST['favicon']??'';if(!$url||!$nm)resp(false,'网址和名称不能为空');if(!preg_match('/^https?:\/\//i',$url))$url='https://'.$url;$d=loadData();$fd=false;foreach($d['groups'] as &$g){if($g['id']===$gid){if($bid){foreach($g['bookmarks'] as &$b)if($b['id']===$bid){$b=['id'=>$b['id'],'name'=>$nm,'url'=>$url,'desc'=>$ds,'favicon'=>$fv];$fd=true;break;}unset($b);}else{$g['bookmarks'][]=['id'=>uniqid('b'),'name'=>$nm,'url'=>$url,'desc'=>$ds,'favicon'=>$fv];$fd=true;}break;}}unset($g);if($fd){saveData($d);resp(true);}resp(false,'分组不存在');}
if($action==='deleteBookmark'){$gid=$_POST['groupId']??'';$bid=$_POST['bookmarkId']??'';$d=loadData();foreach($d['groups'] as &$g){if($g['id']===$gid){$g['bookmarks']=array_values(array_filter($g['bookmarks'],function($b)use($bid){return $b['id']!==$bid;}));break;}}unset($g);saveData($d);resp(true);}
resp(false,'未知操作');
