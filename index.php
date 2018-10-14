<?php
require_once 'idiorm.php';
ORM::configure('sqlite:./example.db');
ORM::configure('return_result_sets', true);

if(isset($_GET['func'])){

if($_GET['func'] == "ins"){
	$str = "";
	$str .= "<form action='index.php?func=ins_exe' method='post'>";
	$str .= "<input type='text' name='title'><br>";
	$str .= "<textarea name='text'></textarea>";
	$str .= "<br>";
	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";
	echo $str;

}else if($_GET['func'] == "ins_exe"){
	$result = ORM::for_table('test')->create();
	$result->title = $_POST['title'];
	$result->text = $_POST['text'];
	$result->save();
	header('Location: index.php');

}else if($_GET['func'] == "upd"){
	$results = ORM::for_table('test')->find_one($_GET['id']);
	//foreach($results as $result){
	$str = "";
	$str .= "<form action='index.php?func=upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . $_GET['id'] . ">";
	//$str .= "&id=" . $_GET['id'] . "'>";
	$str .= "<input type='text' name='title' value='";
	//$str .= $results["title"];
	$str .= $results->title;
	$str .= "'><br>";
	$str .= "<textarea name='text'>";
	//$str .= $results["text"];
	$str .= $results->text;
	$str .= "</textarea>";
	$str .= "<br>";
	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";
	echo $str;

}else if($_GET['func'] == "upd_exe"){
	//$results = ORM::for_table('test')->where('id',$_GET['id'])->find_one($_GET['id']);
	$results = ORM::for_table('test')->find_one($_POST['id']);
	//$results = ORM::for_table('test')->where('id',$_POST['id'])->find_many();
	$results->title = $_POST['title'];
	$results->text = $_POST['text'];
	$results->save();
	header('Location: index.php');

}else if($_GET['func'] == "del"){
	$results = ORM::for_table('test')->find_one($_GET['id']);
	$results->delete();
	header('Location: index.php');
}

}else{
	echo "<a href='index.php?func=ins'>insert</a><br>";

	$results = ORM::for_table('test')->find_many();
	foreach($results as $result){
		//echo $result['id'];
		echo $result->id;
		echo "<br>";
		echo $result->title;
		echo "<br>";
		echo $result->text;
		echo "<br>";
		echo "<a href='index.php?id=" . $result->id . "&func=upd'>update</a>";
		echo "<br>";
		echo "<a href='index.php?id=" . $result->id . "&func=del'>delete</a>";
		echo "<br>";
	}
}
