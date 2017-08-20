<?php
//系統基本資料
//session_start();
require 'vendor/autoload.php';
use Symfony\Component\HttpFoundation\Session\Session;
$session = new Session();
$session->start();
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => '99',
    'username'  => 'root',
    'password'  => 'aandd!@#$%^aandd0606',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Set the event dispatcher used by Eloquent models... (optional)
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$menu_arr=[
  '首頁'=>'index.php',
  '閱讀學習'=>'index.php?op=read',
  '按序測驗'=>'index.php?op=orderQuizForm',
  '亂序測驗'=>'index.php?op=unorderQuizForm',
  '測驗結果統計'=>'index.php?op=report',
  '測驗列表統計'=>'index.php?op=report2',
  ];

 $quizTypeArr=array(
 	"orderQuiz"=>"有序測驗",
 	"unorderQuiz"=>"無序測驗"
 	);

$math99_arr=array();
for($i=1;$i<=9;$i++){
  for($j=1;$j<=9;$j++){
    $math99_arr[$i."X".$j]=$i*$j;
  }
}


$top_nav=dy_nav($menu_arr);

/********************* 自訂函數 *********************/
function arrayToSelect($arr,$option=true,$default_val="",$use_v=false,$validate=false){
  if(empty($arr))return;
  $opt=($option)?"<option value=''>請選擇</option>\n":"";
  foreach($arr as $i=>$v){
    //false則以陣列索引值為選單的值，true則以陣列的值為選單的值
    $val=($use_v)?$v:$i;
    $selected=($val==$default_val)?'selected="selected"':"";        //設定預設值
    $validate_check=($validate)?"class='required'":"";
    $opt.="<option value='$val' $selected $validate_check>$v</option>\n";
  }
  return  $opt;
}

function arrayToRadio($arr,$use_v=false,$name="default",$default_val=""){
      if(empty($arr))return;
      $opt="";
      foreach($arr as $i=>$v){
        $val=($use_v)?$v:$i;
        $checked=($val==$default_val)?"checked='checked'":"";
        $opt.="<input type='radio' name='{$name}' id='{$val}' value='{$val}' $checked><label for='{$val}' style='display:inline;margin-right:15px;'> $v</label>";
      }
      return $opt;
}

function arrayToRadioBS2($arr,$use_v=false,$name="default",$default_val=""){
      if(empty($arr))return;
      $opt="";
      foreach($arr as $i=>$v){
        $val=($use_v)?$v:$i;
        $checked=($val==$default_val)?"checked='checked'":"";
        $opt.="<label class='radio inline'><input type='radio' name='{$name}' id='{$val}' value='{$val}' $checked>$v</label>";
      }
      return $opt;
}

function arrayToCheckbox($arr,$name,$default_val="",$use_v=false){
  $opt="";
  //<input type="checkbox" name="option1" value="Milk">\
  $default_valarr=explode(",",$default_val);
  //die(var_dump($default_valarr));
  if(empty($arr))return;
  foreach($arr as $i=>$v){
    //false則以陣列索引值為選單的值，true則以陣列的值為選單的值
    $val=($use_v)?$v:$i;
    $selected=(in_array($val,$default_valarr))?"checked":"";        //設定預設值
    $opt.="
    <div class='form-group'>
    <label class='checkbox' for='stu_{$val}'> <h2>{$v}</h2> </label> <input type='checkbox' name='{$name}' value='{$val}' id='stu_{$val}' class='form-control' {$selected}>  
    </div>
    ";
  }
  return  $opt;
}


//自定輸出錯誤訊息
function die_content($content=""){
    $main="
    <!DOCTYPE html>
    <html lang='zh-Hant-tw'>
    <head>
    <meta charset='utf-8'>
    <title>輸出錯誤訊息</title>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta name='description' content='輸出錯誤訊息'>
    <meta name='author' content='aandd'>
    <!--引入JQuery CDN-->
    <script src='https://code.jquery.com/jquery-2.1.4.min.js'></script>
    <!--引入Bootstrap 3 CDN---->
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css'>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
    </head>
    <body>
    <!--放入網頁主體-->
    <div class='container'>
      <!-- 主要內容欄位開始 -->
      <div class='row'>
      <div class='col-md-12 col-sm-12'>
        <div class='jumbotron'>
          <h1>輸出錯誤訊息</h1>
          <p>{$content}</p>
        </div>
      </div>
      </div>
      <!-- 主要內容欄位結束 -->
    </div> 
    <!-- 主要內容欄位結束 -->
    </body>
    </html>
  ";
    die($main);
}

//產生動態導覽列
function dy_nav($page_menu=array()){
    global $title;
    $main="
    <!-- Fixed navbar -->
    <nav class='navbar navbar-default navbar-fixed-top'>
      <div class='container'>
        <div class='navbar-header'>
          <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='#navbar' aria-expanded='false' aria-controls='navbar'>
            <span class='sr-only'>Toggle navigation</span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
            <span class='icon-bar'></span>
          </button>
          <a class='navbar-brand' href='#'>{$title}</a>
        </div>
        <div id='navbar' class='navbar-collapse collapse'>
          <ul class='nav navbar-nav'>";
      //$file_name=basename($_SERVER['PHP_SELF']);
      $file_name=basename($_SERVER['REQUEST_URI']);
      foreach($page_menu as $i=>$v){
        $class=($file_name==$v)?"class='active'":"";
        $main.="<li {$class}><a href='{$v}'>{$i}</a></li>";
      }
          $main.="</ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>  
  
  
  ";
  
  
  
  
    return $main;
}

function bootstrap($content="",$js_link="",$css_link="",$js_fun=""){
    global $top_nav,$title;
  $main="
  <!DOCTYPE html>
  <html lang='zh-Hant-tw'>
  <head>
  <meta charset='utf-8'>
  <title>{$title}</title>
   <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <meta name='description' content='{$title}'>
        <meta name='author' content='aandd'>
        <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link href='https://maxcdn.bootstrapcdn.com/bootswatch/3.3.5/cerulean/bootstrap.min.css' rel='stylesheet'>  
    <script src='https://code.jquery.com/jquery-2.1.4.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'></script>
        <style type='text/css'>
          body {
            padding-top: 60px;
            padding-bottom: 20px;
          }
        </style>
        <!--引入額外的css檔案以及js檔案開始-->
        {$js_link}
        {$css_link}
        <!--引入額外的css檔案以及js檔案結束-->
        <!--jquery語法開始-->
        {$js_fun}
        <!--jquery語法結束-->
        </head>
        <body>
  <!--放入網頁主體-->
  {$top_nav}
  <div class='container'>
    <!-- 主要內容欄位開始 -->
    {$content}
    <!-- 主要內容欄位結束 -->
  </div> 
  <!-- 主要內容欄位結束 -->
  </body>
  </html>
  
  ";

    return $main;
}
function showGoogleId(){
  if(isset($_SESSION['picture']) AND isset($_SESSION['name']) AND isset($_SESSION['gmail'])){
    $main="
    <img class='img-circle center-block' src='{$_SESSION['picture']}'>
    <a href='http://aandd.idv.tw/~aanddweb/99/login.php?logout=logout'><img src='img/logout.png' style='width:100px' class='center-block'></a>
    Hellow：{$_SESSION['name']}。Mail：{$_SESSION['gmail']}
  	";
    return $main;
  }else{
    header("location:http://aandd.idv.tw/~aanddweb/99/login.php");
  }
}
?>