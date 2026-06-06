<?php
session_start();
$doGz=isset($_SERVER['HTTP_ACCEPT_ENCODING'])&&strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'gzip')!==false;
if($doGz){ob_start(function($c){$e=gzencode($c,6);return $e?$e:$c;});header('Content-Encoding: gzip');}else{ob_start();}
header('Cache-Control: no-store,no-cache,must-revalidate');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
$dataFile=__DIR__.'/nav-data.json';
function loadData(){global $dataFile;if(file_exists($dataFile)){$d=json_decode(file_get_contents($dataFile),true);if($d&&is_array($d['groups']??null))return $d;}return['adminHash'=>'44f61792d66021c0030fa37dca5162871345c525f61984b88fa1af16d8117672','siteName'=>'StarNav','siteDesc'=>'StarNav - 最实用的经验，分享最需要的你','groups'=>[['id'=>'default','name'=>'默认','emoji'=>'📌','bookmarks'=>[]]]];}
function isAdmin(){return!empty($_SESSION['admin']);}
function esc($s){return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');}
$data=loadData();$adminMode=isAdmin();
// SVG icon definitions: id => SVG path(s)
$ICONS=[
'home'=>['M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z','polyline points="9 22 9 12 15 12 15 22"'],
'grid'=>['rect x="3" y="3" width="7" height="7"','rect x="14" y="3" width="7" height="7"','rect x="3" y="14" width="7" height="7"','rect x="14" y="14" width="7" height="7"'],
'code'=>['polyline points="16 18 22 12 16 6','polyline points="8 6 2 12 8 18'],
'layers'=>['polygon points="12 2 2 7 12 12 22 7 12 2','polyline points="2 17 12 22 22 17','polyline points="2 12 12 17 22 12'],
'book'=>['path d="M4 19.5A2.5 2.5 0 016.5 17H20','path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z'],
'play'=>['polygon points="5 3 19 12 5 21 5 3'],
'briefcase'=>['rect x="2" y="7" width="20" height="14" rx="2" ry="2','path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16'],
'shopping'=>['circle cx="9" cy="21" r="1','circle cx="20" cy="21" r="1','path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6'],
'heart'=>['path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z'],
'star'=>['polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'],
'globe'=>['circle cx="12" cy="12" r="10','line x1="2" y1="12" x2="22" y2="12','path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z'],
'video'=>['polygon points="23 7 16 12 23 17 23 7','rect x="1" y="5" width="15" height="14" rx="2" ry="2'],
'music'=>['path d="M9 18V5l12-2v13','circle cx="6" cy="18" r="3','circle cx="18" cy="16" r="3'],
'camera'=>['path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z','circle cx="12" cy="13" r="4'],
'mail'=>['path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z','polyline points="22,6 12,13 2,6'],
'cloud'=>['path d="M18 10h-1.26A8 8 0 109 20h9a5 5 0 000-10z'],
'settings'=>['circle cx="12" cy="12" r="3','path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z'],
'monitor'=>['rect x="2" y="3" width="20" height="14" rx="2" ry="2','line x1="8" y1="21" x2="16" y2="21','line x1="12" y1="17" x2="12" y2="21'],
'smartphone'=>['rect x="5" y="2" width="14" height="20" rx="2" ry="2','line x1="12" y1="18" x2="12.01" y2="18'],
'tv'=>['rect x="2" y="7" width="20" height="15" rx="2" ry="2','polyline points="17 2 12 7 7 2'],
'cpu'=>['rect x="4" y="4" width="16" height="16" rx="2" ry="2','rect x="9" y="9" width="6" height="6','line x1="9" y1="1" x2="9" y2="4','line x1="15" y1="1" x2="15" y2="4','line x1="9" y1="20" x2="9" y2="23','line x1="15" y1="20" x2="15" y2="23','line x1="20" y1="9" x2="23" y2="9','line x1="20" y1="14" x2="23" y2="14','line x1="1" y1="9" x2="4" y2="9','line x1="1" y1="14" x2="4" y2="14'],
'wifi'=>['path d="M5 12.55a11 11 0 0114.08 0','path d="M1.42 9a16 16 0 0121.16 0','path d="M8.53 16.11a6 6 0 016.95 0','line x1="12" y1="20" x2="12.01" y2="20'],
'rss'=>['path d="M4 11a9 9 0 019 9','path d="M4 4a16 16 0 0116 16','circle cx="5" cy="19" r="1'],
'users'=>['path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2','circle cx="9" cy="7" r="4','path d="M23 21v-2a4 4 0 00-3-3.87','path d="M16 3.13a4 4 0 010 7.75'],
'lock'=>['rect x="3" y="11" width="18" height="11" rx="2" ry="2','path d="M7 11V7a5 5 0 0110 0v4'],
'unlock'=>['rect x="3" y="11" width="18" height="11" rx="2" ry="2','path d="M7 11V7a5 5 0 019.9-1'],
'tool',['path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z'],
'zap'=>['polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2'],
'target'=>['circle cx="12" cy="12" r="10','circle cx="12" cy="12" r="6','circle cx="12" cy="12" r="2'],
'bookmark'=>['path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z'],
'file'=>['path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z','polyline points="13 2 13 9 20 9'],
'folder'=>['path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z'],
'database'=>['ellipse cx="12" cy="5" rx="9" ry="3','path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3','path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5'],
'key'=>['path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4'],
'palette'=>['circle cx="13.5" cy="6.5" r="0.5','circle cx="17.5" cy="10.5" r="0.5','circle cx="8.5" cy="7.5" r="0.5','circle cx="6.5" cy="12.5" r="0.5','path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 011.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z'],
'coffee'=>['path d="M18 8h1a4 4 0 010 8h-1','path d="M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8z','line x1="6" y1="1" x2="6" y2="4','line x1="10" y1="1" x2="10" y2="4','line x1="14" y1="1" x2="14" y2="4'],
'map'=>['polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6','line x1="8" y1="2" x2="8" y2="18','line x1="16" y1="6" x2="16" y2="22'],
'pin'=>['path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z','circle cx="12" cy="10" r="3'],
'rocket'=>['path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 00-2.91-.09z','path d="M12 15l-3-3a22 22 0 012-3.95A12.88 12.88 0 0122 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 01-4 2z','path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0','path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5'],
'gift'=>['polyline points="20 12 20 22 4 22 4 12','rect x="2" y="7" width="20" height="5','line x1="12" y1="22" x2="12" y2="7','path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z','path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z'],
'award'=>['circle cx="12" cy="8" r="7','polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88'],
'trophy'=>['path d="M6 9H4.5a2.5 2.5 0 010-5h.5','path d="M18 9h1.5a2.5 2.5 0 000-5h-.5','path d="M4 22h16','path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22','path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22','path d="M18 2H6v7a6 6 0 0012 0V2Z'],
'crown'=>['path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7z','path d="M3 20h18'],
'phone'=>['path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z'],
'headphone'=>['path d="M3 18v-6a9 9 0 0118 0v6','path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z'],
'mic'=>['path d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z','path d="M19 10v2a7 7 0 01-14 0v-2','line x1="12" y1="19" x2="12" y2="23','line x1="8" y1="23" x2="16" y2="23'],
'compass'=>['circle cx="12" cy="12" r="10','polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76'],
'anchor'=>['circle cx="12" cy="5" r="3','line x1="12" y1="22" x2="12" y2="8','path d="M5 12H2a10 10 0 0020 0h-3'],
'feather'=>['path d="M20.24 12.24a6 6 0 00-8.49-8.49L5 10.5V19h8.5z','line x1="16" y1="8" x2="2" y2="22','line x1="17.5" y1="15" x2="9" y2="15'],
'eye'=>['path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z','circle cx="12" cy="12" r="3'],
'activity'=>['polyline points="22 12 18 12 15 21 9 3 6 12 2 12'],
'bell'=>['path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9','path d="M13.73 21a2 2 0 01-3.46 0'],
'flashlight'=>['line x1="9" y1="18" x2="15" y2="18','line x1="10" y1="22" x2="14" y2="22','path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0018 8 6 6 0 006 8c0 1 .23 2.23 1.3 3.5A4.67 4.67 0 018.91 14'],
'sunrise'=>['path d="M17 18a5 5 0 00-10 0','line x1="12" y1="9" x2="12" y2="2','line x1="4.22" y1="10.22" x2="5.64" y2="11.64','line x1="1" y1="18" x2="3" y2="18','line x1="21" y1="18" x2="23" y2="18','line x1="18.36" y1="11.64" x2="19.78" y2="10.22','line x1="23" y1="22" x2="1" y2="22','polyline points="8 6 12 2 16 6'],
'umbrella'=>['path d="M23 12a11.05 11.05 0 00-22 0','path d="M5 12.18a5.79 5.79 0 015-3.43 5.79 5.79 0 015 3.43','line x1="12" y1="12" x2="12" y2="22'],
'flag'=>['path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z','line x1="4" y1="22" x2="4" y2="15'],
'link'=>['path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71','path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71'],
'share'=>['circle cx="18" cy="5" r="3','circle cx="6" cy="12" r="3','circle cx="18" cy="19" r="3','line x1="8.59" y1="13.51" x2="15.42" y2="17.49','line x1="15.41" y1="6.51" x2="8.59" y2="10.49'],
'plus'=>['line x1="12" y1="5" x2="12" y2="19','line x1="5" y1="12" x2="19" y2="12'],
'x'=>['line x1="18" y1="6" x2="6" y2="18','line x1="6" y1="6" x2="18" y2="18'],
'check'=>['polyline points="20 6 9 17 4 12'],
'edit'=>['path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7','path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z'],
'trash'=>['polyline points="3 6 5 6 21 6','path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2'],
'log-out'=>['path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4','polyline points="16 17 21 12 16 7','line x1="21" y1="12" x2="9" y2="12'],
'log-in'=>['path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4','polyline points="10 17 15 12 10 7','line x1="15" y1="12" x2="3" y2="12'],
'download'=>['path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4','polyline points="7 10 12 15 17 10','line x1="12" y1="15" x2="12" y2="3'],
'upload'=>['path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4','polyline points="17 8 12 3 7 8','line x1="12" y1="3" x2="12" y2="15'],
'calendar'=>['rect x="3" y="4" width="18" height="18" rx="2" ry="2','line x1="16" y1="2" x2="16" y2="6','line x1="8" y1="2" x2="8" y2="6','line x1="3" y1="10" x2="21" y2="10'],
'clock'=>['circle cx="12" cy="12" r="10','polyline points="12 6 12 12 16 14'],
'timer'=>['circle cx="10" cy="12" r="8','polyline points="10 8 10 12 12 14','path d="M16.24 7.76a6 6 0 00-8.49-8.49L5 10.5V19h8.5z','line x1="16" y1="8" x2="2" y2="22'],
'box'=>['path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z','polyline points="3.27 6.96 12 12.01 20.73 6.96','line x1="12" y1="22.08" x2="12" y2="12'],
'archive'=>['polyline points="21 8 21 21 3 21 3 8','rect x="1" y="3" width="22" height="5','line x1="10" y1="12" x2="14" y2="12'],
'clipboard'=>['path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2','rect x="8" y="2" width="8" height="4" rx="1" ry="1'],
'list'=>['line x1="8" y1="6" x2="21" y2="6','line x1="8" y1="12" x2="21" y2="12','line x1="8" y1="18" x2="21" y2="18','line x1="3" y1="6" x2="3.01" y2="6','line x1="3" y1="12" x2="3.01" y2="12','line x1="3" y1="18" x2="3.01" y2="18'],
'menu'=>['line x1="3" y1="12" x2="21" y2="12','line x1="3" y1="6" x2="21" y2="6','line x1="3" y1="18" x2="21" y2="18'],
'chevron-left'=>['polyline points="15 18 9 12 15 6'],
'chevron-right'=>['polyline points="9 18 15 12 9 6'],
'chevron-up'=>['polyline points="18 15 12 9 6 15'],
'chevron-down'=>['polyline points="6 9 12 15 18 9'],
'search'=>['circle cx="11" cy="11" r="8','line x1="21" y1="21" x2="16.65" y2="16.65'],
'minus'=>['line x1="5" y1="12" x2="19" y2="12'],
'circle'=>['circle cx="12" cy="12" r="10'],
'square'=>['rect x="3" y="3" width="18" height="18" rx="2" ry="2'],
'triangle'=>['path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
'hexagon'=>['path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z'],
'octagon'=>['path d="M7.86 2h8.28L22 7.86v8.28L16.14 22H7.86L2 16.14V7.86z'],
'diamond'=>['path d="M6 3h12l4 6-10 13L2 9z'],
'cross'=>['line x1="18" y1="6" x2="6" y2="18','line x1="6" y1="6" x2="18" y2="18'],
'hash'=>['line x1="4" y1="9" x2="20" y2="9','line x1="4" y1="15" x2="20" y2="15','line x1="10" y1="3" x2="8" y2="21','line x1="16" y1="3" x2="14" y2="21'],
'percent'=>['line x1="19" y1="5" x2="5" y2="19','circle cx="6.5" cy="6.5" r="2.5','circle cx="17.5" cy="17.5" r="2.5'],
'at'=>['circle cx="12" cy="12" r="4','path d="M16 8v5a3 3 0 006 0v-1a10 10 0 10-3.92 7.94'],
];
?>
<!DOCTYPE html><html lang="zh-CN"><head><script>(function(){var t=localStorage.getItem('starnav_theme');if(!t)t=window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light';document.documentElement.setAttribute('data-theme',t)})();</script><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=esc($data['siteName'])?></title><meta name="application-name" content="<?=esc($data['siteName'])?>"><meta name="theme-color" id="metaTC" content="#ffffff"><link rel="icon" href="https://www.5iehome.cc/favicon.ico"><style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{--font-cn:'PingFang SC','Hiragino Sans GB','Microsoft YaHei','Segoe UI',system-ui,sans-serif;--sidebar-w:220px;--transition:0.2s ease;--danger:#e16162;--success:#00b894;--accent:#888}
[data-theme="dark"]{--bg:#1a1a1a;--sidebar-bg:#1e1e1e;--card:#252525;--card-hover:#2a2a2a;--border:rgba(255,255,255,0.08);--border-hover:rgba(255,255,255,0.15);--text:#ddd;--text2:#888;--text3:#555;--nav-hover:rgba(255,255,255,0.06);--nav-active:rgba(255,255,255,0.1);--input-bg:#333;--input-border:rgba(255,255,255,0.1);--fav-bg:rgba(255,255,255,0.06);--dash-border:rgba(255,255,255,0.12);--shadow:0 2px 8px rgba(0,0,0,0.3);--modal-bg:#252525;--modal-border:rgba(255,255,255,0.1);--toast-bg:#2a2a2a;--action-h:rgba(255,255,255,0.08);--drag-border:#666;--drag-bg:rgba(255,255,255,0.05);--icon-stroke:#aaa}
[data-theme="light"]{--bg:#f5f5f5;--sidebar-bg:#fff;--card:#fff;--card-hover:#f9f9f9;--border:rgba(0,0,0,0.08);--border-hover:rgba(0,0,0,0.15);--text:#333;--text2:#777;--text3:#aaa;--nav-hover:rgba(0,0,0,0.04);--nav-active:rgba(0,0,0,0.06);--input-bg:#f0f0f0;--input-border:rgba(0,0,0,0.12);--fav-bg:rgba(0,0,0,0.04);--dash-border:rgba(0,0,0,0.12);--shadow:0 2px 8px rgba(0,0,0,0.06);--modal-bg:#fff;--modal-border:rgba(0,0,0,0.1);--toast-bg:#fff;--action-h:rgba(0,0,0,0.04);--drag-border:#666;--drag-bg:rgba(0,0,0,0.02);--icon-stroke:#666}
body{font-family:var(--font-cn);background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden;-webkit-font-smoothing:antialiased;transition:background .3s ease,color .3s ease}
.layout{display:flex;min-height:100vh;position:relative}
.sidebar{width:var(--sidebar-w);flex-shrink:0;background:var(--sidebar-bg);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:10;transition:background .3s ease}
.sidebar-logo{padding:24px 20px 20px;display:flex;align-items:center;justify-content:center;border-bottom:1px solid var(--border)}
.sidebar-logo-text{font-size:1.3rem;font-weight:700;letter-spacing:.08em}
.sidebar-clock{padding:14px 16px 12px;border-bottom:1px solid var(--border);text-align:center}
.sidebar-clock .clock{font-family:'SF Mono','JetBrains Mono',monospace;font-size:2rem;font-weight:300;letter-spacing:.06em;margin-bottom:2px;font-variant-numeric:tabular-nums}
.sidebar-clock .clock-date{font-size:.72rem;color:var(--text2);letter-spacing:.04em}
.sidebar-nav{flex:1;overflow-y:auto;padding:8px 8px}
.sidebar-nav::-webkit-scrollbar{width:2px}
.sidebar-nav::-webkit-scrollbar-thumb{background:var(--border);border-radius:2px}
.nav-item{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:6px;cursor:pointer;transition:var(--transition);color:var(--text2);font-size:.82rem;border:none;background:none;width:100%;font-family:var(--font-cn);text-align:left;margin-bottom:2px;position:relative}
.nav-item:hover{background:var(--nav-hover);color:var(--text)}
.nav-item.active{background:var(--nav-active);color:var(--text)}
.nav-item-icon{width:18px;height:18px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.nav-item-icon svg{width:14px;height:14px;stroke:var(--text3);fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.nav-item.active .nav-item-icon svg{stroke:var(--text2)}
.nav-item-name{flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.nav-item-count{font-size:.68rem;color:var(--text3);padding:2px 6px;border-radius:8px;background:var(--card-hover)}
body.admin-mode .nav-item{cursor:grab;user-select:none}
body.admin-mode .nav-item:active{cursor:grabbing}
.nav-item.dragging{opacity:0.5;background:var(--drag-bg)!important}
.nav-item.drag-over{background:var(--drag-bg);border:1px dashed var(--drag-border)}
.nav-add-group{display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 10px;border-radius:6px;cursor:pointer;transition:var(--transition);color:var(--text3);border:1px dashed var(--dash-border);background:none;width:100%;font-family:var(--font-cn);font-size:.78rem;margin-top:6px}
.nav-add-group:hover{border-color:var(--text2);color:var(--text2)}
.sidebar-footer{padding:8px;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:6px}
.sidebar-btn{display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 10px;border-radius:6px;cursor:pointer;transition:var(--transition);color:var(--text2);font-size:.78rem;border:none;background:none;width:100%;font-family:var(--font-cn);text-decoration:none}
.sidebar-btn:hover{background:var(--nav-hover);color:var(--text)}
.sidebar-btn svg{width:14px;height:14px;flex-shrink:0}
.sidebar-btn-row{display:flex;gap:4px}
.sidebar-btn-row .sidebar-btn{flex:1;padding:8px 4px;font-size:.74rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.theme-toggle{display:flex;align-items:center;gap:4px}
[data-theme="dark"] .theme-toggle .sun-icon{display:none}
[data-theme="dark"] .theme-toggle .moon-icon{display:block}
[data-theme="light"] .theme-toggle .sun-icon{display:block}
[data-theme="light"] .theme-toggle .moon-icon{display:none}
.main{flex:1;margin-left:var(--sidebar-w);padding:32px 32px 0}
.groups-container{display:flex;flex-direction:column;gap:20px}
.group{background:var(--card);border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s ease,opacity .2s ease;scroll-margin-top:32px;position:relative}
body.admin-mode .group{cursor:grab;user-select:none}
body.admin-mode .group:active{cursor:grabbing}
.group.dragging{opacity:0.5;border-color:var(--drag-border)!important;background:var(--drag-bg)}
.group.drag-over{border-color:var(--drag-border)!important}
.group-header{text-align:center;padding:12px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:center;gap:8px;position:relative}
.group-title{font-size:1rem;font-weight:500;color:var(--text2);letter-spacing:.06em;flex:1}
.group-actions{display:flex;gap:2px;flex-shrink:0}
.group-actions button{background:none;border:none;cursor:pointer;width:24px;height:24px;border-radius:4px;display:flex;align-items:center;justify-content:center;color:var(--text3);transition:var(--transition);font-size:.8rem}
.group-actions button:hover{background:var(--action-h);color:var(--text2)}
.bookmarks-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(max(180px,calc((100% - 15px)/5)),1fr));gap:0;padding:12px 16px}
.bookmarks-grid:empty::after{content:'拖拽书签到此处';display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:.75rem;padding:30px 0;pointer-events:none}
.group.drag-over{outline:2px dashed var(--accent);outline-offset:-2px;background:var(--drag-bg)}
.bookmark-card{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:6px;cursor:pointer;transition:background .15s ease,opacity .2s ease;position:relative;user-select:none;overflow:hidden;background:none;border:none;width:100%;font-family:var(--font-cn)}
.bookmark-card:hover{background:var(--card-hover)}
.bookmark-card:active{transition-duration:.08s}
body.admin-mode .bookmark-card{cursor:grab;user-select:none}
body.admin-mode .bookmark-card:active{cursor:grabbing}
.bookmark-card.dragging-bm{opacity:0.4}
.bookmark-card.drop-before{border-top:2px solid var(--accent)}
.bookmark-card.drop-after{border-bottom:2px solid var(--accent)}
.bookmark-favicon{width:20px;height:20px;border-radius:4px;background:var(--fav-bg);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0}
.bookmark-favicon img{width:16px;height:16px;object-fit:contain}
.bookmark-favicon .fallback-icon{font-size:.7rem;color:var(--text3)}
.bookmark-name{font-size:.88rem;font-weight:400;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;letter-spacing:.02em}
.bookmark-edit,.bookmark-delete{position:absolute;top:4px;width:20px;height:20px;border-radius:4px;background:var(--card);border:1px solid var(--border);cursor:pointer;display:none;align-items:center;justify-content:center;color:var(--text3);font-size:.65rem;transition:var(--transition)}
.bookmark-edit{right:26px}.bookmark-delete{right:2px}
.bookmark-card:hover .bookmark-edit,.bookmark-card:hover .bookmark-delete{display:flex}
.bookmark-edit:hover{background:var(--accent);color:#fff}
.bookmark-delete:hover{background:var(--danger);color:#fff}
[data-theme="dark"] a{color:inherit;text-decoration:none}
.modal-overlay{position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;padding:20px;animation:fadeIn .2s ease}
.modal-overlay.show{display:flex}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal{background:var(--modal-bg);border:1px solid var(--modal-border);border-radius:12px;padding:28px;width:100%;max-width:420px;box-shadow:var(--shadow);max-height:90vh;overflow-y:auto}
.modal h2{font-size:1.1rem;font-weight:600;margin-bottom:20px;letter-spacing:.04em}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:.8rem;color:var(--text2);margin-bottom:5px;font-weight:500}
.form-group input{width:100%;padding:10px 12px;background:var(--input-bg);border:1px solid var(--input-border);border-radius:6px;color:var(--text);font-size:.88rem;font-family:var(--font-cn);transition:var(--transition);outline:none}
.form-group input:focus{border-color:var(--accent)}
.form-group input::placeholder{color:var(--text3)}
.form-group .input-row{display:flex;gap:8px}
.form-group .input-row input{flex:1}
.fetch-btn{padding:10px 14px;background:var(--input-bg);border:1px solid var(--input-border);border-radius:6px;color:var(--text2);cursor:pointer;font-size:.78rem;font-family:var(--font-cn);white-space:nowrap;transition:var(--transition)}
.fetch-btn:hover{background:var(--card-hover)}
.fetch-btn:disabled{opacity:.5;cursor:not-allowed}
.favicon-preview{display:flex;align-items:center;gap:10px;margin-top:6px}
.favicon-preview img{width:28px;height:28px;border-radius:4px;background:var(--fav-bg)}
.modal-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:24px}
.btn{padding:8px 18px;border-radius:6px;font-size:.84rem;font-weight:500;cursor:pointer;font-family:var(--font-cn);transition:var(--transition);border:none}
.btn-primary{background:var(--text);color:var(--bg)}.btn-primary:hover{opacity:.85}
.btn-secondary{background:var(--input-bg);color:var(--text2);border:1px solid var(--input-border)}.btn-secondary:hover{background:var(--card-hover)}
.btn-danger{background:rgba(225,97,98,.1);color:var(--danger);border:1px solid rgba(225,97,98,.15)}.btn-danger:hover{background:rgba(225,97,98,.2)}
.spinner,.loading-spinner{display:inline-block;width:14px;height:14px;border:2px solid var(--border);border-top-color:var(--text2);border-radius:50%;animation:spin .5s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(16px);background:var(--toast-bg);border:1px solid var(--border);border-radius:8px;padding:10px 20px;font-size:.84rem;box-shadow:var(--shadow);z-index:2000;opacity:0;transition:all .25s ease;pointer-events:none}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.toast.error{border-color:rgba(225,97,98,.2);color:var(--danger)}
.toast.success{border-color:rgba(0,184,148,.2);color:var(--success)}
.icon-grid{display:grid;grid-template-columns:repeat(10,1fr);gap:4px;margin-top:6px;max-height:200px;overflow-y:auto;padding:4px}
.icon-grid::-webkit-scrollbar{width:3px}
.icon-grid::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
.icon-option{width:100%;aspect-ratio:1;border:1px solid var(--border);border-radius:4px;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:var(--transition);padding:4px}
.icon-option svg{width:18px;height:18px;stroke:var(--icon-stroke);fill:none;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round}
.icon-option:hover{background:var(--nav-active);border-color:var(--border-hover)}
.icon-option.active{background:var(--nav-active);border-color:var(--text2)}
.icon-option.active svg{stroke:var(--text)}
.admin-only{display:none!important}
body.admin-mode .admin-only{display:flex!important}
.guest-only{display:none!important}
body:not(.admin-mode) .guest-only{display:flex!important}
.mobile-menu-btn{display:none;position:fixed;top:12px;left:12px;z-index:20;background:var(--card);border:1px solid var(--border);border-radius:6px;padding:8px;cursor:pointer;color:var(--text2)}
.mobile-menu-btn svg{width:18px;height:18px}
.site-footer{text-align:center;padding:24px 0 16px;font-size:.72rem;color:var(--text3);letter-spacing:.03em}
.site-footer a{color:var(--text3);text-decoration:none;transition:color .2s ease}
.export-options{display:flex;flex-direction:column;gap:10px}
.export-option{display:flex;align-items:flex-start;gap:10px;padding:12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;transition:var(--transition)}
.export-option:hover{border-color:var(--border-hover);background:var(--card-hover)}
.export-option input[type="radio"]{margin-top:2px;accent-color:var(--accent)}
.export-title{font-weight:600;font-size:.88rem;margin-bottom:2px}
.export-desc{font-size:.75rem;color:var(--text3)}
.export-limit{font-size:.7rem;color:var(--text3);margin-top:12px;text-align:center}
@media(max-width:768px){.sidebar{transform:translateX(-100%);transition:transform .3s ease;z-index:100}.sidebar.open{transform:translateX(0)}.main{margin-left:0;padding:16px 12px 0}.mobile-menu-btn{display:block}.bookmarks-grid{grid-template-columns:repeat(auto-fill,minmax(max(140px,calc((100% - 10px)/4)),1fr))}.modal{padding:20px;margin:12px}.icon-grid{grid-template-columns:repeat(8,1fr)}}
@media(max-width:640px){.bookmarks-grid{grid-template-columns:repeat(auto-fill,minmax(max(120px,calc((100% - 8px)/3)),1fr))}}
@media(max-width:480px){.bookmarks-grid{grid-template-columns:repeat(2,1fr);gap:0}.bookmark-card{padding:8px 10px}.bookmark-favicon{width:18px;height:18px}.bookmark-favicon img{width:14px;height:14px}.bookmark-name{font-size:.82rem}}
@media(max-width:360px){.bookmarks-grid{grid-template-columns:1fr;gap:0}.main{padding:12px 8px 0}}
.emoji-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:6px;max-height:200px;overflow-y:auto;padding:4px}
.emoji-option{font-size:1.3rem;width:36px;height:36px;border:1px solid var(--border);border-radius:6px;background:var(--card);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:var(--transition)}
.emoji-option:hover{border-color:var(--accent)}
.emoji-option.active{border-color:var(--accent);background:var(--nav-active)}
</style></head><body <?=$adminMode?'class="admin-mode"':''?>><button class="mobile-menu-btn" onclick="document.getElementById('sb').classList.toggle('open')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button><div class="layout"><aside class="sidebar" id="sb"><div class="sidebar-logo"><div class="sidebar-logo-text"><?=esc($data['siteName'])?></div></div><div class="sidebar-clock"><div class="clock" id="clk">00:00</div><div class="clock-date" id="clkD"></div></div><nav class="sidebar-nav" id="sNav"><?php foreach($data['groups'] as $gi=>$g):?><div class="nav-item" data-gid="<?=esc($g['id'])?>" draggable="true" onclick="sG('<?=esc($g['id'])?>')" ondragstart="onNavDragStart(event)" ondragover="onNavDragOver(event)" ondragenter="onNavDragEnter(event)" ondragleave="onNavDragLeave(event)" ondrop="onNavDrop(event)" ondragend="onNavDragEnd(event)"><span class="nav-item-icon"><?=esc($g['emoji']??'📌')?></span><span class="nav-item-name"><?=esc($g['name'])?></span><span class="nav-item-count"><?=count($g['bookmarks'])?></span></div><?php endforeach;?><div class="nav-add-group admin-only" onclick="oGM()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>添加分组</div></nav><div class="sidebar-footer"><div class="sidebar-btn-row"><button class="sidebar-btn" onclick="tT()"><div class="theme-toggle"><svg class="sun-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg><svg class="moon-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></div><span id="tL">日间模式</span></button></div><div class="admin-only" style="display:flex;flex-direction:column;gap:4px"><button class="sidebar-btn" onclick="oEM()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>数据导出</button><form method="POST" action="nav-api.php?action=logout" style="width:100%;margin:0"><button type="submit" class="sidebar-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>退出管理</button></form></div><button class="sidebar-btn guest-only" onclick="oLM()"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>管理员登录</button></div></aside><div class="main"><div class="groups-container" id="gC"><?php foreach($data['groups'] as $gi=>$g):?><div class="group" id="g-<?=esc($g['id'])?>" draggable="true" ondragstart="onGroupDragStart(event)" ondragover="onGroupDragOver(event)" ondragenter="onGroupDragEnter(event)" ondragleave="onGroupDragLeave(event)" ondrop="onGroupDrop(event)" ondragend="onGroupDragEnd(event)"><div class="group-header"><div class="group-title"><?=esc($g['emoji']??'📌')?> <?=esc($g['name'])?></div><div class="group-actions admin-only"><button onclick="oBM('<?=esc($g['id'])?>')" title="添加网站"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button><button onclick="oGM('<?=esc($g['id'])?>')" title="编辑分组"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button><button onclick="cDG('<?=esc($g['id'])?>')" title="删除分组"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button></div></div><div class="bookmarks-grid"><?php foreach($g['bookmarks'] as $bi=>$bm):?><div class="bookmark-card" id="bm-<?=esc($bm['id'])?>" draggable="true" data-gid="<?=esc($g['id'])?>" data-bid="<?=esc($bm['id'])?>" data-url="<?=esc($bm['url'])?>" ondragstart="onBmDragStart(event)" ondragover="onBmDragOver(event)" ondragenter="onBmDragEnter(event)" ondragleave="onBmDragLeave(event)" ondrop="onBmDrop(event)" ondragend="onBmDragEnd(event)" onclick="if(!document.body.classList.contains('admin-mode'))window.open(this.dataset.url,'_blank','noopener')"><span class="bookmark-edit admin-only" onclick="event.stopPropagation();oBM('<?=esc($g['id'])?>','<?=esc($bm['id'])?>')" title="编辑"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></span><span class="bookmark-delete admin-only" onclick="event.stopPropagation();cDB('<?=esc($g['id'])?>','<?=esc($bm['id'])?>')" title="删除"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span><div class="bookmark-favicon" data-url="<?=esc($bm['url'])?>"><img src="<?=esc($bm['favicon']??'https://t0.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://example.com&size=32')?>" alt="" loading="lazy" onerror="tnF(this)"></div><span class="bookmark-name"><?=esc($bm['name'])?></span></div><?php endforeach;?></div></div><?php endforeach;?><p style="color:var(--text3);text-align:center;padding:40px;display:<?=count($data['groups'])===0?'block':'none'?>">暂无分组，点击左侧"添加分组"开始</p></div><footer class="site-footer">Copyright &copy; <a href="https://www.5iehome.cc" target="_blank">5iehome.cc</a></footer></div></div><div class="modal-overlay" id="bmM"><div class="modal"><h2 id="bmMT">添加书签</h2><div class="form-group"><label>网址</label><div class="input-row"><input type="url" id="bmU" placeholder="https://example.com" oninput="onUI()"><button class="fetch-btn" id="fB" onclick="fSI()">自动获取</button></div></div><div class="form-group"><label>名称</label><input type="text" id="bmN" placeholder="网站名称" maxlength="40"></div><div class="form-group"><label>图标地址（可选）</label><div class="input-row"><input type="url" id="bmF" placeholder="留空则自动获取" oninput="onFI()"><button class="fetch-btn" id="rFB" onclick="rFv()">重置</button></div></div><div class="form-group"><label>预览</label><div class="favicon-preview" id="fP"><span style="color:var(--text3)">输入网址后自动获取</span></div></div><div class="modal-actions"><button class="btn btn-secondary" onclick="cM('bmM')">取消</button><button class="btn btn-primary" onclick="sBm()">保存</button></div></div></div><div class="modal-overlay" id="gM"><div class="modal"><h2 id="gMT">添加分组</h2><div class="form-group"><label>分组名称</label><input type="text" id="gN" placeholder="例如：常用工具"></div><div class="form-group"><label>图标</label><div class="emoji-grid" id="emojiGrid"></div></div><div class="form-group"><label>预览</label><div style="font-size:2rem;text-align:center;padding:8px" id="gEP">📌</div></div><div class="modal-actions"><button class="btn btn-secondary" onclick="cM('gM')">取消</button><button class="btn btn-primary" onclick="sGp()">保存</button></div></div></div><div class="modal-overlay" id="lM"><div class="modal"><h2>管理员登录</h2><div class="form-group"><label>管理密码</label><input type="password" id="lP" placeholder="请输入管理密码" onkeydown="if(event.key==='Enter')dL()"></div><div class="modal-actions"><button class="btn btn-secondary" onclick="cM('lM')">取消</button><button class="btn btn-primary" onclick="dL()">登录</button></div></div></div><div class="modal-overlay" id="exportM"><div class="modal"><h2>数据导出</h2><div class="form-group"><label>选择导出格式：</label><div class="export-options"><label class="export-option"><input type="radio" name="exportFormat" value="html" checked><div><div class="export-title">Bookmarks (.html)</div><div class="export-desc">浏览器收藏夹兼容格式</div></div></label><label class="export-option"><input type="radio" name="exportFormat" value="json"><div><div class="export-title">JSON (.json)</div><div class="export-desc">完整备份数据</div></div></label></div></div><div class="modal-actions"><button class="btn btn-secondary" onclick="cM('exportM')">取消</button><button class="btn btn-primary" onclick="doExport()">开始导出</button></div></div></div><div class="toast" id="tst"></div><script>
const TK='starnav_theme',CP='starnav_cache_',CE=604800000;
let eB=null,eG=null,sEmoji='📌';
const gD=u=>{try{return new URL(u).hostname.replace(/^www\./,'')}catch(e){return u}};
const gFU=u=>{try{const d=gD(u);return`https://t0.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://${d}&size=32`}catch(e){return''}};
const gFF=u=>{try{const d=gD(u);return[`https://t0.gstatic.cn/faviconV2?client=SOCIAL&type=FAVICON&fallback_opts=TYPE,SIZE,URL&url=http://${d}&size=32`,`https://ico.kucat.cn/get.php?url=${u}`,`https://icon.horse/icon/${d}`]}catch(e){return[]}};
function tnF(i){const c=i.parentElement,u=c.dataset.url;if(!u||!u.startsWith('http')){c.innerHTML='<span class="fallback-icon">🔗</span>';return}const f=gFF(u),s=parseInt(i.dataset.s||'0'),n=s+1;if(n<f.length){i.dataset.s=n;i.src=f[n]}else{c.innerHTML='<span class="fallback-icon">🔗</span>'}}
async function hP(p){const d=new TextEncoder().encode(p+'_starnav_salt_2024'),h=await crypto.subtle.digest('SHA-256',d);return Array.from(new Uint8Array(h)).map(b=>b.toString(16).padStart(2,'0')).join('')}
function oLM(){document.getElementById('lP').value='';document.getElementById('lM').classList.add('show');setTimeout(()=>document.getElementById('lP').focus(),100)}
async function dL(){const p=document.getElementById('lP').value;if(!p){sT('请输入密码','error');return}const h=await hP(p),f=new FormData();f.append('action','login');f.append('password',h);try{const r=await fetch('nav-api.php',{method:'POST',body:f}),j=await r.json();if(j.success){cM('lM');sT('登录成功','success');setTimeout(()=>location.reload(),500)}else sT('密码错误','error')}catch(e){sT('登录失败','error')}}
function rC(){const n=new Date(),h=String(n.getHours()).padStart(2,'0'),m=String(n.getMinutes()).padStart(2,'0');document.getElementById('clk').textContent=`${h}:${m}`;const w=['日','一','二','三','四','五','六'];document.getElementById('clkD').textContent=`${n.getFullYear()}年${n.getMonth()+1}月${n.getDate()}日 星期${w[n.getDay()]}`}
function uFP(u){const p=document.getElementById('fP');if(u)p.innerHTML=`<img src="${u}" alt="" onerror="this.style.display='none'"><span style="display:none;color:var(--text3)">无法加载</span>`}
async function fSI(){const i=document.getElementById('bmU');let u=i.value.trim();if(!u)return;if(!/^https?:\/\//i.test(u)){u='https://'+u;i.value=u}const b=document.getElementById('fB'),d=gD(u);b.disabled=true;b.innerHTML='<span class="loading-spinner"></span>';uFP(gFU(u));const c=localStorage.getItem(CP+d);if(c){try{const j=JSON.parse(c);if(Date.now()-j.timestamp<CE&&j.title){document.getElementById('bmN').value=j.title;if(j.favicon)uFP(j.favicon);b.disabled=false;b.textContent='自动获取';return}}catch(e){}}const px=[`https://api.allorigins.win/raw?url=`,`https://corsproxy.io/?url=`,`https://api.codetabs.com/v1/proxy?quest=`];let bh='';const rs=await Promise.allSettled(px.map(async p=>{try{const r=await fetch(p+encodeURIComponent(u),{signal:AbortSignal.timeout(8000),headers:{'Accept':'text/html','Accept-Language':'zh-CN,zh;q=0.9'}});if(r.ok){const t=await r.text();if(t.length>100)return t}}catch(e){}return''}));for(const r of rs)if(r.status==='fulfilled'&&r.value&&r.value.length>bh.length)bh=r.value;let t='',fv='';if(bh.length>100){const ur=new URL(u),p=new DOMParser().parseFromString(bh,'text/html');for(const s of['link[rel="icon"][type="image/svg+xml"]','link[rel="icon"][sizes="192x192"]','link[rel="apple-touch-icon"]','link[rel="icon"]']){const el=p.querySelector(s);if(el&&el.getAttribute('href')){let h=el.getAttribute('href');if(h.startsWith('//'))h='https:'+h;else if(h.startsWith('/'))h=ur.origin+h;else if(!h.startsWith('http'))h=ur.origin+'/'+h;fv=h;break}}t=(p.querySelector('meta[property="og:site_name"]')?.content||p.querySelector('meta[property="og:title"]')?.content||p.querySelector('title')?.textContent||'').trim().replace(/\s+/g,' ');const fi=document.getElementById('bmF').value.trim();if(fv&&!fi){uFP(fv)}else if(fi){uFP(fi)}try{localStorage.setItem(CP+d,JSON.stringify({title:t,favicon:fv,timestamp:Date.now()}))}catch(e){}}document.getElementById('bmN').value=t||d;b.disabled=false;b.textContent='自动获取'}
let fT=null;
function onFI(){const v=document.getElementById('bmF').value.trim();if(v){uFP(v)}else{const u=document.getElementById('bmU').value.trim();if(u&&/^https?:\/\//i.test(u))uFP(gFU(u))}}
function onUI(){clearTimeout(fT);const u=document.getElementById('bmU').value.trim();const fi=document.getElementById('bmF').value.trim();if(u&&/^https?:\/\//i.test(u)&&!fi)uFP(gFU(u));fT=setTimeout(()=>{if(u.length>5&&fi.length===0)fSI()},1200)}
function rFv(){const u=document.getElementById('bmU').value.trim();if(!u){document.getElementById('bmF').value='';document.getElementById('fP').innerHTML='<span style="color:var(--text3)">输入网址后自动获取</span>';return}document.getElementById('bmF').value='';uFP(gFU(u))}
function oBM(gid,bid){eB={groupId:gid,bookmarkId:bid||null};if(bid){fetch(`nav-api.php?action=getBookmark&groupId=${encodeURIComponent(gid)}&bookmarkId=${encodeURIComponent(bid)}`).then(r=>r.json()).then(bm=>{document.getElementById('bmMT').textContent='编辑书签';document.getElementById('bmU').value=bm.url;document.getElementById('bmN').value=bm.name;const rawFv=bm.favicon||'';const isDirectImg=rawFv&&/\.(png|ico|svg|jpg|jpeg|webp|gif)(\?|$)/i.test(rawFv);document.getElementById('bmF').value=isDirectImg?rawFv:'';uFP(rawFv||gFU(bm.url));document.getElementById('bmM').classList.add('show');setTimeout(()=>document.getElementById('bmU').focus(),100)})}else{document.getElementById('bmMT').textContent='添加书签';document.getElementById('bmU').value='';document.getElementById('bmN').value='';document.getElementById('bmF').value='';document.getElementById('fP').innerHTML='<span style="color:var(--text3)">输入网址后自动获取</span>';document.getElementById('bmM').classList.add('show');setTimeout(()=>document.getElementById('bmU').focus(),100)}}
async function sBm(){const u=document.getElementById('bmU').value.trim(),n=document.getElementById('bmN').value.trim(),cf=document.getElementById('bmF').value.trim();if(!u||!n){sT('请输入网址和名称','error');return}const fu=/^https?:\/\//i.test(u)?u:'https://'+u;let fv=cf;const isDirectImg=fv&&/\.(png|ico|svg|jpg|jpeg|webp|gif)(\?|$)/i.test(fv);if(!isDirectImg)fv='';if(!fv)fv=gFU(fu);const f=new FormData();f.append('action','saveBookmark');f.append('groupId',eB.groupId);f.append('bookmarkId',eB.bookmarkId||'');f.append('url',fu);f.append('name',n);f.append('desc','');f.append('favicon',fv);try{const r=await fetch('nav-api.php',{method:'POST',body:f}),j=await r.json();if(j.success){cM('bmM');sT(eB.bookmarkId?'书签已更新':'书签已添加','success');setTimeout(()=>location.reload(),500)}else sT('保存失败','error')}catch(e){sT('保存失败','error')}}
const EMOJIS=['📌','⭐','❤️','🔥','🎯','📚','💼','🔧','🎨','📊','💡','🌟','🎵','📷','🗂️','💻','🛠️','📦','🏠','🚀','🎮','📱','🌐','🔐','💰','🎬','🍔','✈️','🏀','🎁','⚡','🌈'];
function buildEmojiPicker(){const c=document.getElementById('emojiGrid');c.innerHTML=EMOJIS.map(e=>`<button type="button" class="emoji-option${e===sEmoji?' active':''}" onclick="selEmoji('${e}')">${e}</button>`).join('')}
function selEmoji(e){sEmoji=e;document.getElementById('gEP').textContent=e;document.querySelectorAll('.emoji-option').forEach(b=>b.classList.toggle('active',b.textContent===e))}
function oGM(gid){eG=gid||null;buildEmojiPicker();if(gid){fetch(`nav-api.php?action=getGroup&groupId=${encodeURIComponent(gid)}`).then(r=>r.json()).then(g=>{document.getElementById('gMT').textContent='编辑分组';document.getElementById('gN').value=g.name;sEmoji=g.emoji||'📌';document.getElementById('gEP').textContent=sEmoji;buildEmojiPicker();document.getElementById('gM').classList.add('show');setTimeout(()=>document.getElementById('gN').focus(),100)})}else{document.getElementById('gMT').textContent='添加分组';document.getElementById('gN').value='';sEmoji='📌';document.getElementById('gEP').textContent='📌';buildEmojiPicker();document.getElementById('gM').classList.add('show');setTimeout(()=>document.getElementById('gN').focus(),100)}}
async function sGp(){const n=document.getElementById('gN').value.trim();if(!n){sT('请输入分组名称','error');return}const f=new FormData();f.append('action','saveGroup');f.append('groupId',eG||'');f.append('name',n);f.append('emoji',sEmoji);try{const r=await fetch('nav-api.php',{method:'POST',body:f}),j=await r.json();if(j.success){cM('gM');sT(eG?'分组已更新':'分组已添加','success');setTimeout(()=>location.reload(),500)}else sT('保存失败','error')}catch(e){sT('保存失败','error')}}
function cDG(id){const n=prompt('确认删除分组？输入分组名确认：');if(!n)return;fetch(`nav-api.php?action=getGroup&groupId=${encodeURIComponent(id)}`).then(r=>r.json()).then(g=>{if(g.name===n){const f=new FormData();f.append('action','deleteGroup');f.append('groupId',id);fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('分组已删除','success');setTimeout(()=>location.reload(),500)}else sT('删除失败','error')})}else sT('分组名不匹配','error')})}
function cDB(g,b){fetch(`nav-api.php?action=getBookmark&groupId=${encodeURIComponent(g)}&bookmarkId=${encodeURIComponent(b)}`).then(r=>r.json()).then(bm=>{if(confirm(`确定要删除书签"${bm.name}"吗？`)){const f=new FormData();f.append('action','deleteBookmark');f.append('groupId',g);f.append('bookmarkId',b);fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('书签已删除','success');setTimeout(()=>location.reload(),500)}else sT('删除失败','error')})}})}
function tT(){const h=document.documentElement,c=h.getAttribute('data-theme'),n=c==='dark'?'light':'dark';h.setAttribute('data-theme',n);localStorage.setItem(TK,n);uTL()}
function uTL(){const tc=document.getElementById('metaTC');if(tc)tc.content=document.documentElement.getAttribute('data-theme')==='dark'?'#1a1a1a':'#ffffff';const el=document.getElementById('tL');if(el)el.textContent=document.documentElement.getAttribute('data-theme')==='dark'?'日间模式':'夜间模式'}
function iT(){const t=document.documentElement.getAttribute('data-theme');uTL()}
function sG(id){const e=document.getElementById('g-'+id);if(e)e.scrollIntoView({behavior:'smooth',block:'start'})}
function cM(id){document.getElementById(id).classList.remove('show')}
function sT(m,t=''){const e=document.getElementById('tst');e.textContent=m;e.className='toast'+(t?' '+t:'');requestAnimationFrame(()=>e.classList.add('show'));setTimeout(()=>e.classList.remove('show'),2500)}
// ===== Group drag (right side) =====
let dragGroup=null;
function onGroupDragStart(e){if(!document.body.classList.contains('admin-mode'))return;dragGroup=e.currentTarget;e.currentTarget.classList.add('dragging');e.dataTransfer.effectAllowed='move';e.dataTransfer.setData('text/plain','');e.dataTransfer.setData('drag-type','group')}
function onGroupDragOver(e){if(!dragGroup&&!dragBm)return;e.preventDefault();e.dataTransfer.dropEffect='move'}
function onGroupDragEnter(e){if(!dragGroup&&!dragBm)return;e.preventDefault();const t=e.currentTarget;if(dragGroup&&t===dragGroup)return;t.classList.add('drag-over')}
function onGroupDragLeave(e){e.currentTarget.classList.remove('drag-over')}
function onGroupDrop(e){e.preventDefault();e.stopPropagation();const t=e.currentTarget;t.classList.remove('drag-over');const dt=e.dataTransfer.getData('drag-type');if(dt==='group'){if(!dragGroup||t===dragGroup)return;const c=document.getElementById('gC'),all=[...c.querySelectorAll('.group')],fi=all.indexOf(dragGroup),ti=all.indexOf(t);if(fi<ti)c.insertBefore(dragGroup,t.nextSibling);else c.insertBefore(dragGroup,t);sGOrder()}else if(dt==='bookmark'){if(!dragBm)return;const srcGid=e.dataTransfer.getData('bm-gid'),srcBid=e.dataTransfer.getData('bm-bid'),destGid=t.id.replace('g-','');const f=new FormData();f.append('action','moveBookmark');f.append('srcGroupId',srcGid);f.append('bookmarkId',srcBid);f.append('destGroupId',destGid);f.append('insertBefore','0');f.append('beforeBookmarkId','');fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('书签已移动','success');setTimeout(()=>location.reload(),500)}else sT('移动失败','error')}).catch(()=>sT('移动失败','error'))}}
function onGroupDragEnd(e){e.currentTarget.classList.remove('dragging');dragGroup=null;document.querySelectorAll('.drag-over').forEach(x=>x.classList.remove('drag-over'))}
function sGOrder(){const order=[...document.querySelectorAll('.group')].map(g=>g.id.replace('g-',''));const f=new FormData();f.append('action','reorderGroups');f.append('order',JSON.stringify(order));fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('分组顺序已保存','success');syncGroupsToNav(order)}else sT('保存顺序失败','error')}).catch(()=>sT('保存顺序失败','error'))}
function syncGroupsToNav(order){const sn=document.getElementById('sNav');const ns=[...sn.querySelectorAll('.nav-item')];const ao=[...sn.querySelectorAll('.nav-add-group')];if(ns.length===0)return;const sorted=ns.slice().sort((a,b)=>order.indexOf(a.dataset.gid)-order.indexOf(b.dataset.gid));sorted.forEach(n=>sn.insertBefore(n,ao[0]))}
// ===== Sidebar nav drag =====
let dragNav=null;
function onNavDragStart(e){if(!document.body.classList.contains('admin-mode'))return;dragNav=e.currentTarget;dragNav.classList.add('dragging');e.dataTransfer.effectAllowed='move';e.dataTransfer.setData('text/plain','');e.dataTransfer.setData('drag-type','nav')}
function onNavDragOver(e){if(!dragNav)return;e.preventDefault();e.dataTransfer.dropEffect='move'}
function onNavDragEnter(e){if(!dragNav)return;e.preventDefault();const t=e.currentTarget;if(t!==dragNav)t.classList.add('drag-over')}
function onNavDragLeave(e){e.currentTarget.classList.remove('drag-over')}
function onNavDrop(e){e.preventDefault();const t=e.currentTarget;t.classList.remove('drag-over');if(!dragNav||t===dragNav)return;const dt=e.dataTransfer.getData('drag-type');if(dt==='nav'){const c=document.getElementById('sNav'),all=[...c.querySelectorAll('.nav-item')],fi=all.indexOf(dragNav),ti=all.indexOf(t);if(fi<ti)c.insertBefore(dragNav,t.nextSibling);else c.insertBefore(dragNav,t);sNavOrder()}}
function onNavDragEnd(e){e.currentTarget.classList.remove('dragging');dragNav=null;document.querySelectorAll('#sNav .nav-item.drag-over').forEach(x=>x.classList.remove('drag-over'))}
function sNavOrder(){const order=[...document.querySelectorAll('#sNav .nav-item')].map(n=>n.dataset.gid);const f=new FormData();f.append('action','reorderGroups');f.append('order',JSON.stringify(order));fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('分组顺序已保存','success');syncNavToGroups(order)}else sT('保存顺序失败','error')}).catch(()=>sT('保存顺序失败','error'))}
function syncNavToGroups(order){const gc=document.getElementById('gC');const gs=[...gc.querySelectorAll('.group')];if(gs.length===0)return;const sorted=gs.slice().sort((a,b)=>order.indexOf(a.id.replace('g-',''))-order.indexOf(b.id.replace('g-','')));sorted.forEach(g=>gc.appendChild(g))}
// ===== Bookmark drag (within & between groups) =====
let dragBm=null;
function onBmDragStart(e){if(!document.body.classList.contains('admin-mode'))return;const card=e.currentTarget;dragBm=card;card.classList.add('dragging-bm');e.dataTransfer.effectAllowed='move';e.dataTransfer.setData('text/plain','');e.dataTransfer.setData('drag-type','bookmark');e.dataTransfer.setData('bm-gid',card.dataset.gid);e.dataTransfer.setData('bm-bid',card.dataset.bid);e.dataTransfer.setDragImage(card,card.offsetWidth/2,card.offsetHeight/2);e.stopPropagation()}
function onBmDragOver(e){if(!dragBm)return;e.preventDefault();e.stopPropagation();e.dataTransfer.dropEffect='move';const card=e.currentTarget.closest('.bookmark-card');if(!card||card===dragBm)return;card.classList.remove('drop-before','drop-after');const rect=card.getBoundingClientRect();const mid=rect.top+rect.height/2;if(e.clientY<mid)card.classList.add('drop-before');else card.classList.add('drop-after')}
function onBmDragEnter(e){e.preventDefault();e.stopPropagation()}
function onBmDragLeave(e){const card=e.currentTarget.closest('.bookmark-card');if(card)card.classList.remove('drop-before','drop-after')}
function onBmDrop(e){e.preventDefault();e.stopPropagation();const card=e.currentTarget.closest('.bookmark-card');if(!card||!dragBm||card===dragBm)return;card.classList.remove('drop-before','drop-after');const dt=e.dataTransfer.getData('drag-type');if(dt!=='bookmark')return;const srcGid=e.dataTransfer.getData('bm-gid'),srcBid=e.dataTransfer.getData('bm-bid'),destGid=card.dataset.gid;const rect=card.getBoundingClientRect(),mid=rect.top+rect.height/2;const insertBefore=e.clientY<mid;const f=new FormData();f.append('action','moveBookmark');f.append('srcGroupId',srcGid);f.append('bookmarkId',srcBid);f.append('destGroupId',destGid);f.append('insertBefore',insertBefore?'1':'0');f.append('beforeBookmarkId',card.dataset.bid);fetch('nav-api.php',{method:'POST',body:f}).then(r=>r.json()).then(j=>{if(j.success){sT('书签已移动','success');setTimeout(()=>location.reload(),500)}else sT('移动失败','error')}).catch(()=>sT('移动失败','error'))}
function onBmDragEnd(e){const card=e.currentTarget.closest('.bookmark-card');if(card)card.classList.remove('dragging-bm');dragBm=null;document.querySelectorAll('.drop-before,.drop-after').forEach(x=>x.classList.remove('drop-before','drop-after'))}
function oEM(){document.getElementById('exportM').classList.add('show')}
function doExport(){const f=document.querySelector('input[name="exportFormat"]:checked').value;if(f==='json'){fetch('nav-api.php?action=exportJSON').then(r=>r.blob()).then(b=>{const u=URL.createObjectURL(b),a=document.createElement('a');a.href=u;a.download='starnav-backup.json';a.click();URL.revokeObjectURL(u);cM('exportM');sT('JSON导出成功','success')}).catch(()=>sT('导出失败','error'))}else{fetch('nav-api.php?action=exportHTML').then(r=>r.blob()).then(b=>{const u=URL.createObjectURL(b),a=document.createElement('a');a.href=u;a.download='starnav-bookmarks.html';a.click();URL.revokeObjectURL(u);cM('exportM');sT('HTML导出成功','success')}).catch(()=>sT('导出失败','error'))}}
iT();
setInterval(rC,1000);rC();
</script></body></html>
