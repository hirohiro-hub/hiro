<html>
	<head>
		<title>Board</title>
		<meta charset = "utf-8">
	</head>
<body>
	<h1 id="midashi_1"> 
		<section class="title">
			<p><font face="arial">簡易掲示板</font></p>
		</section>
	</h1>
<?php

	$edit0 = null;
	$edit1 = null;
	$edit2 = null;

	$dsn = 'データベース名';
	$username = 'ユーザー名';
	$password = 'パスワード';
	$option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);

	$pdo = new PDO($dsn, $username, $password, $option);

	$sql = "CREATE TABLE IF NOT EXISTS table"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	."time TEXT,"
	. "password TEXT"
	.");";
	$stmt = $pdo->query($sql);

	if(!empty($_POST["submit_com"]) || !empty($_POST["submit_edi"]) || !empty($_POST["submit_del"])) {//
		$password = htmlspecialchars($_POST["password"], ENT_QUOTES, "UTF-8");

		if($password != "") {

			if($password == "world") {

				if(!empty($_POST["submit_com"])) {
					$name = htmlspecialchars($_POST["name"], ENT_QUOTES, "UTF-8");
					$comment = htmlspecialchars($_POST["comment"], ENT_QUOTES, "UTF-8");

					if($name != "" && $comment != "") {

						if(!empty($_POST["edit_num"])) {
							$date = date("Y/m/d H:i:s")."　※編集済み";
							$id = htmlspecialchars($_POST["edit_num"], ENT_QUOTES, "UTF-8");
							$sql = 'update table set name=:name,comment=:comment,time=:time where id=:id';
							$stmt = $pdo->prepare($sql);
							$stmt->bindParam(':name', $name, PDO::PARAM_STR);
							$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
							$stmt->bindParam(':time', $date, PDO::PARAM_STR);
							$stmt->bindParam(':id', $id, PDO::PARAM_INT);
							$stmt->execute();
						} else {//
							$date = date("Y/m/d H:i:s");
							$sql = $pdo -> prepare("INSERT INTO table (name, comment, time) VALUES (:name, :comment, :time)");
							$sql -> bindParam(':name', $name, PDO::PARAM_STR);
							$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
							$sql -> bindParam(':time', $time, PDO::PARAM_STR);
							$name = $name;
							$comment = $comment;
							$time = $date;
							$sql -> execute();
						}
					} else {
						echo "<span style='color:#FF0000;'>！入力されていない項目があります！</span>";
					}
				}

				if(!empty($_POST["submit_edi"])) {
					$edit = htmlspecialchars($_POST["edit"], ENT_QUOTES, "UTF-8");
					$edit_on = 0;

					if($edit >=1) {

						$sql = 'SELECT * FROM table';
						$stmt = $pdo->query($sql);
						$results = $stmt->fetchAll();
						foreach ($results as $row){
							if ($row['id'] == $edit) {
								$edit_on = 1;
								$edit0 = $row['id'];
								$edit1 = $row['name'];
								$edit2 = $row['comment'];
							} else {
								;
							}
						}

						if($edit_on == 0) {
							echo "<span style='color:#FF0000;'>！入力した編集番号のコメントはありません！</span>";
						}

					} else {
						echo "<span style='color:#FF0000;'>！編集番号は1以上の整数を入力してください！</span>";
					}
				}

				if(!empty($_POST["submit_del"])) {
					$delete = htmlspecialchars($_POST["delete"], ENT_QUOTES, "UTF-8");
					$delete_on = 0;

					if($delete >=1) {

						$sql = 'SELECT * FROM table';
						$stmt = $pdo->query($sql);
						$results = $stmt->fetchAll();
						foreach ($results as $row){
							if ($row['id'] == $delete) {
								$delete_on = 1;
								$id = $delete;
								$sql = 'delete from table where id=:id';
								$stmt = $pdo->prepare($sql);
								$stmt->bindParam(':id', $id, PDO::PARAM_INT);
								$stmt->execute();

								echo $delete. "番のコメントを削除しました。";

							} else {
								;
							}
							
							if ($delete_on == 1) {
								$sql = 'ALTER TABLE table drop column id';
								$sql = 'ALTER TABLE table add id int(11) primary key not null auto_increment first';
								$sql = 'ALTER TABLE table AUTO_INCREMENT =1';
								$stmt = $pdo->query($sql);
							}
						}
						if($delete_on == 0) {
							echo "<span style='color:#FF0000;'>！入力した削除番号のコメントはありません！</span>";
						}

					} else {
						echo "<span style='color:#FF0000;'>！削除番号は1以上の整数を入力してください！</span>";
					}
				}/////

			} else {////
				echo "<span style='color:#FF0000;'>！パスワードが一致していません！</span>";
			}////
		} else {///
			echo "<span style='color:#FF0000;'>！パスワードを入力してください！</span>";
		}///
	}//
	echo "<br>";
?>

	<form method="POST" action="Board.php">
		<input type = "text" name = "name" value = '<?php if($edit0 != null) echo $edit1; ?>' size = "20" maxlength = "40" placeholder="Name">
		<input type = "hidden" name = " "><br>
		<input type = "text" name = "comment" value = '<?php if($edit0 != null) echo $edit2; ?>' size = "20" maxlength = "40" placeholder="Comment"><br>
		<input type = "password" name = "password" size = "20" maxlength = "40" placeholder="Password">
		<input type = "submit" name = "submit_com" value = "Submit">
		<input type = "hidden" name = "edit_num" value = '<?php if($edit0 != null) echo $edit0; ?>'><br>
	</form>

	<form method="POST" action="Board.php">
		<input type = "number" name = "edit"　min = "1" placeholder="Number">
		<input type = "hidden" name = " "><br>
		<input type = "password" name = "password" size = "20" maxlength = "40" placeholder="Password">
		<input type = "submit" name = "submit_edi" value = "Edit">
	</form>

	<form method="POST" action="Board.php">
		<input type = "number" name = "delete"　min = "1" placeholder="Number">
		<input type = "hidden" name = " "><br>
		<input type = "password" name = "password" size = "20" maxlength = "40" placeholder="Password">
		<input type = "submit" name = "submit_del" value = "Delete">
	</form>
	
<hr>
<h2>コメント履歴</li></h2>

<?php
	$sql = 'SELECT * FROM table';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].'. ';
		echo $row['name'].'：';
		echo $row['comment'].'　';
		echo $row['time'].'<br>';
	echo "<hr>";
	}
?>

</body>
</html>