<?php

	// データベースに接続
	$dns = 'mysql:dbname=oneline_bbs;host=localhost';
	$user = 'root';
	$password = '';
	$dbh = new PDO($dns,$user,$password);
	$dbh->query('SET NAMES utf8');

	// POST送信されたらINSERT文を実行
	if (isset($_POST) && !empty($_POST)){

		//SQL文作成（INSERT文）
		$sql = 'INSERT INTO `posts` ( `nickname`, `comment`, `created`) VALUES ( ?, ?, now())';

		$param[] = $_POST['nickname'];
		$param[] = $_POST['comment'];

		// INSERT文実行
		$stmt = $dbh->prepare($sql);
		$stmt->execute($param);
	}

	//SQL文作成（SELECT文）
	$sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';

	// SELECT文実行
	$stmt = $dbh->prepare($sql);
	$stmt->execute();

	//格納する変数の初期化
	$posts = array();

	// 繰り返し分でデータの取得
	while(1){
		$rec = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($rec == false){
			//データを最後まで取得した印なので終了
			break;
		}
		// 取得したデータを配列に格納しておく
		$posts[] = $rec;
	}

	// データベースから切断
	$dbh = null;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
</head>
<body>
    <form method="post" action="">
      <p><input type="text" name="nickname" placeholder="nickname"></p>
      <p><textarea type="text" name="comment" placeholder="comment"></textarea></p>
      <p><button type="submit" >つぶやく</button></p>

    </form>
    <?php //var_dump($posts);
     ?>
    <ul>
    	<?php 
    	 	foreach ($posts as $post_each) {
    	 		echo '<li>';

    	 		echo 'nickname:'.$post_each['nickname'];
    	 		echo 'comment:'.$post_each['comment'];
    	 		echo 'created:'.$post_each['created'];
    	 		echo '</li>';
    	 	}

    	?>
    </ul>
    <!-- ここにニックネーム、つぶやいた内容、日付を表示する -->

</body>
</html>