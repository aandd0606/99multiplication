<?php
require 'setup.php';
use Symfony\Component\HttpFoundation\Request;
//資料庫操作
use Illuminate\Database\Capsule\Manager as Capsule;
$userInfo=showGoogleId();

function index($left="",$right=""){
	global $math99_arr;
	$read99=read99();
	$main="<div class='row'>
		<div class='col-md-3'>{$left}</div>
		<div class='col-md-9'>{$right}</div>
		</div>";
	return $main;
}

function read99(){
	global $math99_arr;
	$content="";
	$read99="";
	$n=0;
	foreach ($math99_arr as $k => $v) {
		$h2=substr($k,0,1);
		$read99.="<h3>{$k}={$v}</h3>";
		$n++;
		if($n%9==0){
			$content.="<div class='col-md-4'>
				    <div class='panel panel-default'>
  						<div class='panel-heading'>
  							<h2 class='panel-title'>{$h2}的九九乘法表</h2>
							<div class='panel-body'>
						        {$read99}
							</div>
						</div>
					</div>
					</div>";
			$read99="";
		}
	}
	$main=plus99Table();
	$main.="<div class='row'>{$content}</div>";
	
	return $main;
}

function plus99Table(){
	$main="
	<h1>九九乘法表</h1>
	<table class='table table-striped table-bordered table-condensed table-hover'>";
	$main.="<tr><th></th><th>1</th><th>2</th><th>3</th><th>4</th><th>5</th><th>6</th><th>7</th><th>8</th><th>9</th></tr>";
	for($n=1;$n<=9;$n++){
		$main.="<tr><th>{$n}</th>";
		for($i=1;$i<=9;$i++){
			$plus=$n*$i;
			$class=($n==$i)?"class='danger'":"";
			$main.="<td {$class}>{$plus}</td>";
		}
		$main.="</tr>";
	}
	$main.="</table>";
	return $main;
}


$num_arr=[2,3,4,5,6,7,8,9];
function orderQuizForm($order="orderQuiz"){
	global $num_arr;
	$main="<form action='{$_SERVER['PHP_SELF']}' method='post' class='form-inline'>";
	$main.=arrayToCheckbox($num_arr,"num[]","",true);
	$main.="
	<input type='hidden' name='op' value='{$order}'>
	<input type='submit' value='開始測驗'>
	</form>";
	return $main;
}

function leaveTime(){
	$main='
		<form name="form1" method="post" action="">
		<input name="textarea" type="text" value="" class="form-control input-lg">
		</form>
		<script> 
		$(function(){
			// interval();
  				var totalSecond=0;
				var second=0; 
				var minute=0; 
				var hour=0; 
			setInterval(function(){	
				second++; 
				totalSecond++; 
				if(second==60) 
				{ 
				second=0;minute+=1; 
				} 
				if(minute==60) 
				{ 
				minute=0;hour+=1; 
				}
				document.form1.textarea.value = hour+"時"+minute+"分"+second+"秒";  
				document.form.totalSecond.value = totalSecond;  
			}, 1000);

			
			//document.form1.textarea2.value = ""+totalSecond+"秒"; 
			// window.setTimeout("interval();",1000); 	
		})
 
		</script>
	';
	return $main;
}

function orderQuiz($num=array(),$order=false,$op="orderQuiz"){
	global $session;
	// var_dump($num);
	// var_dump(num99Arr($num,true));
	$num99Arr=num99Arr($num,$order);
	$numImplode=implode(",",$num);
	// var_dump($num99Arr);
	$main="
	<script>
		$(function(){
			$('input.answer').first().focus();
			$('input.answer').keydown(function(event){
			  if(event.which==13){
			  	$(this).parent().parent().parent().hide();
			  	$('input:visible:eq(1)').focus();
			  };
			});

		})
	</script>";
	$main.="<form name='form' action='{$_SERVER['PHP_SELF']}' method='post' onkeydown='if(event.keyCode==13)return false;'>";
	foreach($num99Arr as $k1 =>$v1){
		foreach($v1 as $k2 => $v2){
			$main.="
				<div class='panel panel-default'>
				  <div class='panel-body'>
				    <h1> {$k2} = <input type='number' name='answer[$k2]' class='answer' ></h1>
				  </div>
				</div>
			";
		}

	}
	$main.="
		<div class='panel panel-default'>
		  <div class='panel-body'>
		    <input type='hidden' name='op' value='save'>
		    <input type='hidden' name='totalSecond' value=''>
		    <input type='hidden' name='numImplode' value='{$numImplode}'>
		    <input type='hidden' name='quizType' value='{$op}'>
		    <input type='submit' value='送出儲存'>
		  </div>
		</div>
		</form>
	";
	// $session->set('num99Arr', $num99Arr);
	return $main;
}

//產出選擇數目的九九乘法表(可亂數、不亂數選項)
function num99Arr($num=array(),$rand=false){
	$returnNum99Arr=array();
	$num99Arr=array();
	$pickArr=array();
	$m=1;
	foreach ($num as $v){
		for($i=1;$i<=9;$i++){
			// $num99Arr[$m]["{$v}X{$i}"]=$v*$i;
			if($rand){
				$num99Arr["{$v}X{$i}"]=$v*$i;
			}else{
				$num99Arr[$m]["{$v}X{$i}"]=$v*$i;
			}
			$m++;
		}
	}
	if($rand){
		$n=1;
		while(count($num99Arr) > 0){
			$pickkey=array_rand($num99Arr);
			$pickArr[$n][$pickkey]=$num99Arr[$pickkey];
			// $num99Arr=array_diff($num99Arr,$pickArr);
			unset($num99Arr[$pickkey]);
			
			// var_dump($pickArr);
			// var_dump($num99Arr);
			// echo "<h1>{$n}XXXXXX</h1>";
			$n++;
		}
		return $pickArr;	
	}else{
		return $num99Arr;	
	}
	
}
function save($answer=array(),$totalSecond="",$numImplode="",$quizType=""){
	global $session,$math99_arr;
	$numYes=$numNo=$numAll=0;
	$insertArr=array();
	$main="<table class='table table-striped table-bordered table-condensed col-md-6'>
		<tr><td>九九乘法</td><td>你的答案</td><td>正確解答</td></tr>
	";
	@$date=date("Y-m-d H:i:s");
	$avgtime=round(($totalSecond/count($answer)),1);
	$insertQuizArr=array(
		'gmail'=>$_SESSION['gmail'], 'datetime'=>$date, 
		'avgtime'=>$avgtime, 'totaltime'=>$totalSecond,
		'numImplode'=>$numImplode,'quizType'=>$quizType
		);
	// die(var_dump($insertQuizArr);
	$id=Capsule::table('quiz')->insertGetId($insertQuizArr);

	$lastQuiz=$answer;
	foreach($lastQuiz as $k=>$v){
		$numAll++;
		if($v!=$math99_arr[$k]){
			$main.="<tr class='danger'><td>{$k}</td><td>{$v}</td><td>{$math99_arr[$k]}</td></tr>";
			$yesOrNo="no";
			$numNo++;
		}else{
			$main.="<tr class='success'><td>{$k}</td><td>{$v}</td><td>{$math99_arr[$k]}</td></tr>";
			$yesOrNo="yes";
			$numYes++;
		}
		$insertArr[]=array(
			'id'=>$id, 
			'plus'=>$k, 'answer'=>$v, 
			'yesOrNo'=>$yesOrNo
		);
	}
	// $session->getFlashBag()->add('notice', 'Profile updated');
	// foreach ($session->getFlashBag()->get('notice', array()) as $message) {
 //    echo '<div class="flash-notice">'.$message.'</div>';
	$yesRate=round(($numYes/$numAll),3)*100;
	$yesRate.="%";
	$main.="</table>";
	// var_dump($avgtime);
	$content="
				<div class='panel panel-default'>
				  <div class='panel-body'>
				    <h1>{$date}測驗結果報告</h1>
				    <p>總測驗題數：{$numAll}</p>
				    <p>答對題數：{$numYes}</p>
				    <p>答錯題數：{$numNo}</p>
				    <p>答對率：{$yesRate}</p>
				    <p>總共時間：{$totalSecond}秒</p>
				    <p>平均每題時間：{$avgtime}秒</p>
				  </div>
				</div>
	";
	$content.=$main;
	Capsule::table('log')->insert($insertArr);
	return $content; 
}

function report(){
	global $quizTypeArr;
	$main="<table class='table table-striped table-bordered table-condensed table-hover'>
		<tr><th>測驗時間</th><th>測驗範圍</th><th>測驗方式</th><th>每題平均時間</th><th>總時間</th><th>答對題數</th>
		<th>答錯題數</th><th>正確率</th></tr>
	";
	$quizArrs=Capsule::table('quiz')
		->where("gmail",$_SESSION['gmail'])
		->orderBy('datetime', 'desc')
		->get();
	foreach($quizArrs as $quizArr){
		// die(var_dump($quizArr));
		foreach($quizArr as $k => $v){
			$$k=$v;
		}

		$numyesOrNo=Capsule::table('log')->where('id',$id)->count();
		$numYes=Capsule::table('log')->where('yesOrNo','yes')->where('id',$id)->count();
		$numNo=Capsule::table('log')->where('yesOrNo','no')->where('id',$id)->count();
		$yesRate=(round($numYes/$numyesOrNo,3))*100;
		$yesRate.="%";


		$main.="<tr><td>{$datetime}</td><td>{$numImplode}</td><td>{$quizTypeArr[$quizType]}</td>
			<td>{$avgtime}</td><td>{$totaltime}</td><td>{$numYes}</td>
			<td>{$numNo}</td><td>{$yesRate}</td></tr>";

	}
	$main.="</table>";
	return $main;

}

function report2(){
	global $math99_arr;
	$main="<table class='table table-striped table-bordered table-condensed table-hover'>
		<tr><th>九九乘法表項目</th><th>測驗次數</th><th>答對次數</th><th>答錯次數</th><th>答對率</th></tr>
	";
	foreach($math99_arr as $k => $v){
		$testNum=Capsule::table('log')
		->join('quiz', 'log.id', '=', 'quiz.id')
		->where("quiz.gmail",$_SESSION['gmail'])
		->where("log.plus",$k)
		->get()
		->count();
		// var_dump($testNum);

		$yesNum=Capsule::table('log')
			->join('quiz', 'log.id', '=', 'quiz.id')
			->where("quiz.gmail",$_SESSION['gmail'])
			->where("log.plus",$k)
			->where("log.yesOrNo","yes")
			->get()
			->count();	
		// var_dump($yesNum);

		$noNum=Capsule::table('log')
			->join('quiz', 'log.id', '=', 'quiz.id')
			->where("quiz.gmail",$_SESSION['gmail'])
			->where("log.plus",$k)
			->where("log.yesOrNo","no")
			->get()
			->count();

		// var_dump($noNum);
			// if(empty($testNum)) $testNum=0;
		@$yesRate=round(($yesNum/$testNum),3)*100;
		$yesRate.="%";
		$main.="<tr><td>{$k}</td><td>{$testNum}</td><td>{$yesNum}</td><td>{$noNum}</td><td>{$yesRate}</td></tr>";
	}
	$main.="</table>";
	return $main;
}


$page="
	<li>閱讀學習：就是看九九乘法</li>
	<li>按序測驗：選擇要測驗的九九乘法，會按照順序測驗，給正在背誦的小朋友使用！</li>
	<li>亂序測驗：選擇要測驗的九九乘法，不會按照順序測驗，給進階背誦的小朋友使用！</li>
	<li>測驗結果統計：顯示每一次測驗結果，顯示各項資訊包括平均時間、正確率等等!</li>
	<li>九九乘法測驗結果統計：統計所以測驗結果，顯示每一個九九乘法的測驗結果，了解孩子那個錯最多！!</li>
";


$request = Request::createFromGlobals();
$op = $request->get('op');
$num = $request->get('num');
$answer = $request->get('answer');
$totalSecond = $request->get('totalSecond');
$numImplode = $request->get('numImplode');
$quizType = $request->get('quizType');
switch($op){
	case "report2":
	// var_dump($answer);
	$main=index($userInfo,report2());
	break;	


	case "report":
	// var_dump($answer);
	$main=index($userInfo,report($numImplode));
	break;

	case "save":
	// var_dump($answer);
	$main=index($userInfo,save($answer,$totalSecond,$numImplode,$quizType));
	break;

	case 'orderQuizForm':
	$main=index($userInfo,orderQuizForm());
	break;

	case "orderQuiz":
	$main=index($userInfo.leaveTime(),orderQuiz($num,false,$op));
	break;

	case 'unorderQuizForm':
	$main=index($userInfo,orderQuizForm("unorderQuiz"));
	break;

	case "unorderQuiz":
	$main=index($userInfo.leaveTime(),orderQuiz($num,true,$op));
	break;

	case 'read':
	$main=index($userInfo,read99());
	break;
	
	//預設動作
	default:
		// $main=index();
		$main=index($userInfo,$page);
	break;
}
echo bootstrap($main);


?>

