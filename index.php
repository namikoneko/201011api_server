<?php
require_once 'idiorm.php';
ORM::configure('sqlite:./data.db');
ORM::configure('return_result_sets', true);
require 'flight/Flight.php';

// users ##################################################
Flight::route('/users', function(){
    $rows = ORM::for_table('users')->find_many();
    $i = 0;
    foreach($rows as $row){
        $list[$i]["id"] = $row["id"];
        $list[$i]["name"] = $row["name"];
        //$list[$i]["text"] = $row["text"];
        $i++;
    }
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($list);
    echo $arr;
//    echo "hello";
});

// user_tasks ##################################################
Flight::route('/users/@name', function($name){
    $user = ORM::for_table('users')->where('name',$name)->find_one();
    $userid = $user->id;
    $page = $_GET['p'];
    if(isset($page)){
	//$per_page = 3;
	$per_page = 15;
	$offset = ($page - 1) * $per_page;
      $rows = ORM::for_table('tasks')->where('userid',$userid)->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
    }else{
      $rows = ORM::for_table('tasks')->where('userid',$userid)->order_by_desc('updated')->find_many();
    }
	$i = 0;
	foreach($rows as $row){
	    $str[$i]["id"] = $row["id"];
	    $str[$i]["title"] = $row["title"];
	    $str[$i]["text"] = $row["text"];
	    $i++;
	}
    $list = $str;
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($list);
    echo $arr;
});

// updtask ##################################################
Flight::route('/users/@name/upd/@id', function($name,$id){
    $row = ORM::for_table('tasks')->find_one($id);
    $str["id"] = $row->id;
    $str["text"] = $row->text;
    $str["name"] = $name;
    //$str["page"] = $page;
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($str);
    echo $arr;
});

// updtaskexe ##################################################
Flight::route('/tasks/updexe', function(){
    $id = $_POST['id'];
    $row = ORM::for_table('tasks')->find_one($id);
    $row->text = $_POST['text'];
    $row->updated = time();
    $row->save();
    //Flight::redirect('/users/' . $_POST['name']);
});

// deltask ##################################################
Flight::route('/deltask/@name/@id', function($name,$id){
    $row = ORM::for_table('tasks')->find_one($id);
    $row->delete();
    Flight::redirect('/users/' . $name . "?p=1");
});

// instask ##################################################
Flight::route('/instask', function(){
    $user = ORM::for_table('users')->where('name',$_POST['name'])->find_one();
    $userid = $user->id;
    $row = ORM::for_table('tasks')->create();
    $row->title = $_POST['title'];
    $row->text = $_POST['text'];
    $row->userid = $userid;
    $row->updated = time();
    $row->save();
    $name = $_POST['name'];
    Flight::redirect('/users/' . $name . "?p=1");
});

// uptask ##################################################
Flight::route('/uptask/@name/@id', function($name,$id){
    $row = ORM::for_table('tasks')->find_one($id);
    $row->updated = time();
    $row->save();
    Flight::redirect('/users/' . $name . "?p=1");
});

// findclname ##################################################
Flight::route('/findclname/@name/@findtext', function($name,$findtext){
    $user = ORM::for_table('users')->where('name',$name)->find_one();
    $userid = $user->id;
    $rows = ORM::for_table('tasks')->where('userid',$userid)->where_like('title',"%" . $findtext. "%")->find_many();
    $i = 0;
    foreach($rows as $row){
        $str[$i]["id"] = $row["id"];
        $str[$i]["title"] = $row["title"];
        $str[$i]["text"] = $row["text"];
        $i++;
    }
    $list = $str;
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($list);
    echo $arr;
});

// findtask ##################################################
Flight::route('/findtask/@name/@findtext', function($name,$findtext){
    $user = ORM::for_table('users')->where('name',$name)->find_one();
    $userid = $user->id;
    $rows = ORM::for_table('tasks')->where('userid',$userid)->where_like('text',"%" . $findtext. "%")->find_many();
    $i = 0;
    foreach($rows as $row){
        $str[$i]["id"] = $row["id"];
        $str[$i]["title"] = $row["title"];
        $str[$i]["text"] = $row["text"];
        $i++;
    }
    $list = $str;
    header("Content-Type: application/json; charset=utf-8");
    $arr = Flight::json($list);
    echo $arr;
});

// copytask ##################################################
Flight::route('/copytask/@name/@id', function($name,$id){
    $user = ORM::for_table('users')->where('name',$name)->find_one();
    $userid = $user->id;
    $row = ORM::for_table('tasks')->find_one($id);

    $newrow = ORM::for_table('tasks')->create();
    $newrow->title = $row->title;
    $newrow->text = $row->text;
    $newrow->userid = $userid;
    $newrow->updated = time();
    $newrow->save();
    Flight::redirect('/users/' . $name . "?p=1");
});
// cls ##################################################
//Flight::route('/cls', function(){
//    $rows = ORM::for_table('cl')->order_by_desc('updated')->find_many();
//    $i = 0;
//    foreach($rows as $row){
//        $list[$i]["id"] = $row["id"];
//        $list[$i]["name"] = $row["name"];
//        //$list[$i]["text"] = $row["text"];
//        $i++;
//    }
//    header("Content-Type: application/json; charset=utf-8");
//    $arr = Flight::json($list);
//    echo $arr;
//});

// ins_exe ##################################################
Flight::route('/ins_exe', function(){
	$result = ORM::for_table('test')->create();
	//$result->title = $_POST['title'];
	$result->date = date('Y-m-d');
	$result->title = Flight::request()->data->title;
	//$result->text = $_POST['text'];
	$result->text = Flight::request()->data->text;
	$result->updated = time();
	$result->archive = 0;
	$result->save();
	//header('Location: index.php');
	//Flight::redirect('/?page=' . $page);
	Flight::redirect('/');

});

//}else if($_GET['func'] == "upd"){

// upd ##################################################
Flight::route('/upd', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	//foreach($results as $result){
	$str = "";
	//$str .= "<form action='index.php?func=upd_exe' method='post'>";
	$str .= "<form action='upd_exe' method='post'>";
	$str .= "<input type='hidden' name='id' value=" . Flight::request()->query->id . ">";

	if(isset(Flight::request()->query->page)){
		$str .= "<input type='hidden' name='page' value=" . Flight::request()->query->page . ">";
	}

	//$str .= "&id=" . $_GET['id'] . "'>";
	$str .= "<input type='text' name='title' value='";
	//$str .= $results["title"];
	$str .= $results->title;
	$str .= "'><br>";
	$str .= "<textarea class='textUpd' name='text'>";
	//$str .= $results["text"];
	$str .= $results->text;
	$str .= "</textarea>";
	$str .= "<br>";
	$str .= "<input type='submit' value='send'>";
	$str .= "</form>";
	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'Home Page'));
});

//}else if($_GET['func'] == "upd_exe"){

// upd_exe ##################################################
Flight::route('/upd_exe', function(){
	//$results = ORM::for_table('test')->where('id',$_GET['id'])->find_one($_GET['id']);
	$results = ORM::for_table('test')->find_one(Flight::request()->data->id);
	//$results = ORM::for_table('test')->where('id',$_POST['id'])->find_many();
	$results->title = Flight::request()->data->title;
	$results->text = Flight::request()->data->text;
	$results->updated = time();
	$results->save();
	//header('Location: index.php');
	//Flight::redirect('/');
	if(isset(Flight::request()->data->page)){
		Flight::redirect('/?page=' . Flight::request()->data->page);
	}else{
		Flight::redirect('/');
	}
});

//}else if($_GET['func'] == "del"){
// del ##################################################
Flight::route('/del', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	$results->delete();
	//header('Location: index.php');
	//echo "del";
	Flight::redirect('/');
});

// arc_exe ##################################################
Flight::route('/arc_exe', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	$results->archive_date = date('Y-m-d');
	$results->archive = time();
	$results->save();
	//header('Location: index.php');
	Flight::redirect('');
	//echo "here";
});

// arc ##################################################
Flight::route('/arc', function(){
	// ページング
	if(isset(Flight::request()->query->page)){
		$page = Flight::request()->query->page;
	}else{
		$page = 1;
	}

	$records = ORM::for_table('test')->where_not_equal('archive',0)->count();
	$per_page = 5;
	$offset = ($page - 1) * $per_page;

	$q_single = Flight::request()->query->q_single;
	if(isset($q_single)){
	//single入力w検索
		$results = ORM::for_table('test')->where_not_equal('archive',0)->where_raw('("title" like ? or "text" like ?)',array("%" . $q_single . "%","%" . $q_single . "%"))->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}else{
		$results = ORM::for_table('test')->where_not_equal('archive',0)->limit($per_page)->offset($offset)->order_by_desc('archive')->find_many();
	}

	$str = "";
	$str .=<<<EOD
	<form action='ins_exe' method='post'>
	<input type='text' name='title' id='titleInput'><br>
	<textarea name='text' cols=30 rows=10></textarea>
	<br>
	<input type='submit' value='send'>
	<a class='button' href='list'>list</a>
	<a class='button' href='arc'>archive</a>
	</form>

	<form action='arc' method='get'>
		<input type='text' name='q_single'>
		<input type='submit' value='send'>
	</form>

	<table>
		<thead>
			<tr>
				<th>id</th>
				<th>date</th>
				<th>arc_date</th>
				<th>title</th>
				<th>text</th>
				<th>up</th>
				<th>return</th>
				<th>delete</th>
			</tr>
		</thead>
		<tbody>
EOD;
	foreach($results as $result){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $result->id;
		$str .= "</td><td>";
		$str .= $result->date;
		$str .= "</td><td>";
		$str .= $result->archive_date;
		$str .= "</td><td class='titleLink'>";
		$str .= $result->title;
		$str .= "</td><td>";
		$str .= $result->text;
		$str .= "</td><td>";
		//$str .= $result->updated;
		//$str .= $result->archive;
		$str .= "<a href='up_arc?id=" . $result->id . "'>up</a>";
		$str .= "</td><td>";
		$str .= "<a href='arc_exe_ret?id=" . $result->id . "'>return</a>";
		$str .= "</td><td>";
		$str .= "<a href='del?id=" . $result->id . "'>delete</a>";
		$str .= "</td>";
		$str .= "</tr>";
	}
	$str .=<<<EOD
	</tbody>
	</table>
EOD;

	// ページング
	if($page > 1){
		$str .= "<a class='button' href='arc?page=" . ($page - 1) . "'>previous</a>";
	}
	if($page < ceil($records/$per_page)){
		$str .= "<a class='button' href='arc?page=" . ($page + 1) . "'>after</a>";
	}
	//echo $str;
	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'Home Page'));
});

// arc_exe_ret ##################################################
Flight::route('/arc_exe_ret', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	$results->archive = 0;
	$results->archive_date = 0;
	$results->updated = time();
	$results->save();
	Flight::redirect('/arc');
});

// up ##################################################
flight::route('/up', function(){
	$results = orm::for_table('test')->find_one(flight::request()->query->id);
	$results->updated = time();
	$results->save();
	flight::redirect('');
});

// up_arc ##################################################
Flight::route('/up_arc', function(){
	$results = ORM::for_table('test')->find_one(Flight::request()->query->id);
	$results->archive = time();
	$results->save();
	Flight::redirect('/arc');
});
//}else{
// list ##################################################
Flight::route('/*', function(){
	//echo "<a href='index.php?func=ins'>insert</a><br>";
	$str = "";
	//$str .= "<a href='ins'>insert</a><br>";

	// ページング
	if(isset(Flight::request()->query->page)){
		$page = Flight::request()->query->page;
	}else{
		$page = 1;
	}

	$records = ORM::for_table('test')->count();
	$per_page = 5;
	$offset = ($page - 1) * $per_page;

	// クエリ
	//$results = ORM::for_table('test')->find_many();
	$q_single = Flight::request()->query->q_single;
	$q_title = Flight::request()->query->q_title;
	$q_text = Flight::request()->query->q_text;
	if(isset($q_single)){
	//single入力w検索
	$results = ORM::for_table('test')->where('archive',0)->where_raw('("title" like ? or "text" like ?)',array("%" . $q_single . "%","%" . $q_single . "%"))->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}else if(!empty($q_title)){
		//単純検索
		$results = ORM::for_table('test')->where('archive',0)->where_like('title',"%" . Flight::request()->query->q_title . "%")->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}else if(!empty($q_text)){
		//単純検索
		$results = ORM::for_table('test')->where('archive',0)->where_like('text',"%" . Flight::request()->query->q_text . "%")->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}else{
		//全検索
		$results = ORM::for_table('test')->where('archive',0)->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();
	}
	//w入力w検索
	//$results = ORM::for_table('test')->where('archive',0)->where_raw('("title" like ? or "text" like ?)',array("%" . Flight::request()->query->q_title . "%","%" . Flight::request()->query->q_text . "%"))->limit($per_page)->offset($offset)->order_by_desc('updated')->find_many();

	$str .=<<<EOD
	<form action='ins_exe' method='post'>
				<input type='text' name='title' id='titleInput'><br>
				<textarea name='text' cols=30 rows=10></textarea>
				<br>
				<input type='submit' value='send'>
		<a class='button' href='list'>list</a>
		<a class='button' href='arc'>archive</a>
	</form>
	<div class='row'>
		<div class="three columns">
			<form action='' method='get'>
				<input type='text' name='q_single'>
				<input type='submit' value='send'>
			</form>
		</div>
		<div class="three columns">
			<button id='slideBtn'>slide</button>
		</div>
	</div>
	<div id='w_query'>
	<form action='' method='get'>
		<input type='text' name='q_title'>
		<input type='text' name='q_text'>
		<input type='submit' value='send'>
	</form>
	</div>
	<table>
		<thead>
			<tr>
				<th>id</th>
				<th>date</th>
				<th>title</th>
				<th>text</th>
				<th>up</th>
				<th>update</th>
				<th>archive</th>
				<th>delete</th>
			</tr>
		</thead>
		<tbody>
EOD;
	foreach($results as $result){
		$str .= "<tr>";
		$str .= "<td>";
		$str .= $result->id;
		$str .= "</td><td>";
		$str .= $result->date;
		$str .= "</td><td class='titleLink'>";
		$str .= $result->title;
		$str .= "</td><td>";
		$str .= $result->text;
		$str .= "</td><td>";
		//$str .= $result->updated;
		//$str .= "</td><td>";
		//$str .= $result->archive;
		//$str .= "</td><td>";
		$str .= "<a href='up?id=" . $result->id . "'>up</a>";
		$str .= "</td><td>";
		if(isset(Flight::request()->query->page)){
			$str .= "<a href='upd?id=" . $result->id . "&page=" . Flight::request()->query->page . "'>update</a>";
		}else{
			$str .= "<a href='upd?id=" . $result->id . "'>update</a>";
		}
		$str .= "</td><td>";
		$str .= "<a href='arc_exe?id=" . $result->id . "'>archive</a>";
		$str .= "</td><td>";
		$str .= "<a href='del?id=" . $result->id . "'>delete</a>";
		$str .= "</td>";
		$str .= "</tr>";
	}

	$str .=<<<EOD
	</tbody>
	</table>
EOD;

	// ページング
	if($page > 1){
		$str .= "<a class='button' href='?page=" . ($page - 1) . "'>previous</a>";
	}
	if($page < ceil($records/$per_page)){
		$str .= "<a class='button' href='?page=" . ($page + 1) . "'>after</a>";
	}
	//echo $str;
	//Flight::render('result.php', array('str' => $str));

	Flight::render('header', array('heading' => 'Hello'), 'header_content');
	Flight::render('body', array('str' => $str), 'body_content');
	Flight::render('layout', array('title' => 'Home Page'));

//}
});
//if(isset($_GET['func'])){//仮
//if($_GET['func'] == "upd"){//仮
Flight::start();
