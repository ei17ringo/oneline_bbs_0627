<?php
// ここにDBに登録する処理を記述する
$dsn = 'mysql:dbname=oneline_bbs;host=localhost';
$user = 'root';
$password = '';
$dbh = new PDO($dsn,$user,$password);
$dbh->query('SET NAMES utf8');

// POST送信された時のみ登録処理を実行
if (isset($_POST) && !empty($_POST)) {
  if (isset($_GET["id"]) == true){
    //データを更新する
    $sql = 'UPDATE `posts` SET `nickname` = ?,`comment` = ? WHERE `id` = ?';
  
    $data[] = $_POST['nickname'];
    $data[] = $_POST['comment'];
    $data[] = $_GET['id'];
    //var_dump($sql);
  }else{
    // データを登録する
    $sql = 'INSERT INTO `posts`(`nickname`, `comment`, `created`) VALUES (?, ?, now())';
    $data[] = $_POST['nickname'];
    $data[] = $_POST['comment'];
  }


  

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute($data);
}

$nickname = "";
$comment = "";
$id = 0;


//歯車が押されたか確認
if (isset($_GET['action']) && ($_GET['action'] == 'edit')){
  //編集したいデータを取得
  $sql = 'SELECT * FROM `posts` WHERE `id`='.$_GET['id'];

  // SQL実行
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  //一行分フェッチ
  $rec = $stmt->fetch(PDO::FETCH_ASSOC);

  $nickname = $rec['nickname'];
  $comment = $rec['comment'];
  $id = $rec['id'];
}

// データの表示
$sql = 'SELECT * FROM `posts` ORDER BY `created` DESC';
// SQL実行
$stmt = $dbh->prepare($sql);
$stmt->execute();

// データ格納用変数
$data = array();

// データを取得
while (1) {
  $rec = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($rec == false) {
    break;
  }
  // 1レコードずつデータを格納
  $data[] = $rec;
}

// データベースを切断
$dbh = null;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/form.css">
  <link rel="stylesheet" href="assets/css/timeline.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
  <!-- ナビゲーションバー -->
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="#page-top"><span class="strong-title"><i class="fa fa-linux"></i> Oneline bbs</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <!-- Bootstrapのcontainer -->
  <div class="container">
    <!-- Bootstrapのrow -->
    <div class="row">

      <!-- 画面左側 -->
      <div class="col-md-4 content-margin-top">
        <!-- form部分 -->
        <?php if (empty($nickname) && empty($comment)){ ?>
          <form action="bbs.php" method="post">
        <?php }else{ ?>
          <form action="bbs.php?id=<?php echo $id;?>" method="post">
        <?php } ?>


          <!-- nickname -->
          <div class="form-group">
            <div class="input-group">
              <input type="text" name="nickname" class="form-control" id="validate-text" placeholder="nickname" value="<?php echo $nickname; ?>" required>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- comment -->
          <div class="form-group">
            <div class="input-group" data-validate="length" data-length="4">
              <textarea type="text" class="form-control" name="comment" id="validate-length" placeholder="comment" required><?php echo $comment; ?></textarea>
              <span class="input-group-addon danger"><span class="glyphicon glyphicon-remove"></span></span>
            </div>
          </div>
          <!-- つぶやくボタン -->
          <button type="submit" class="btn btn-primary col-xs-12" disabled>
            <?php if (empty($nickname) && empty($comment)){ ?>
              つぶやく
            <?php }else{ ?>
              編集する
            <?php  } ?>
          </button>
        </form>
      </div>

      <!-- 画面右側 -->
      <div class="col-md-8 content-margin-top">
        <div class="timeline-centered">
        <?php foreach($data as $d): ?>
          <article class="timeline-entry">
              <div class="timeline-entry-inner">
                  <a href="bbs.php?action=edit&id=<?php echo $d["id"]; ?>">
                    <div class="timeline-icon bg-success">
                        <i class="entypo-feather"></i>
                        <i class="fa fa-cogs"></i>
                    </div>
                  </a>
                  <div class="timeline-label">
                    <?php
                      // いったん日時型に変換する（String型からDatetime型へ変換）
                      $created = strtotime($d['created']);
                      // 書式の変換
                      $created = date('Y/m/d', $created);
                    ?>
                      <h2><a href="#"><?php echo $d['nickname']; ?></a> <span><?php echo $created; ?></span></h2>
                      <p><?php echo $d['comment']; ?></p>
                  </div>
              </div>
          </article>
        <?php endforeach; ?>

          <article class="timeline-entry begin">
              <div class="timeline-entry-inner">
                  <div class="timeline-icon" style="-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);">
                      <i class="entypo-flight"></i> +
                  </div>
              </div>
          </article>
        </div>
      </div>

    </div>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/form.js"></script>
</body>
</html>
