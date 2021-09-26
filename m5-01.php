<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8" /> 
<title>mission_5-01</title>
</head> 
<body> 
<form action="" method="post">
    名前<br>
    <input type="text" name="name" placeholder="名前" ><br>
    コメント<br>
    <input type="text" name="str" placeholder="コメント"><br>
    <input type="submit" name="submit"><br>
</form>
<form action="" method="post">
    削除対象番号<br>
    <input type="number" name="deletenum" placeholder="削除対象番号"><br>
    <input type="submit" name="delete" value="削除">
</form>
<form action="" method="post">
    名前<br>
    <input type="text" name="rename" placeholder="名前" ><br>
    コメント<br>
    <input type="text" name="restr" placeholder="コメント"><br>
    編集対象番号<br>
    <input type="number" name="editornum" placeholder="編集対象番号">
    <input type="submit" name="editor" value="編集"><br>
</form>
<?php
    //4-2以降でも毎回接続は必要。
    //$dsnの式の中にスペースを入れないこと！

    //【サンプル】
    // ・データベース名：
    // ・ユーザー名：
    // ・パスワード：
    // の学生の場合：

    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブルを作る(m4-2)
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    $date = date("Y/m/d H:i:s");
    //データを登録(m4-5)
    if(!empty($_POST["name"]) && !empty($_POST["str"])){
        $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment) VALUES (:name, :comment)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["str"]; //好きな名前、好きな言葉は自分で決めること
        $sql -> execute();
        //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。
    }
    
    //削除処理
    if(!empty($_POST["deletenum"])){
        $id = $_POST["deletenum"];
        $sql = 'delete from tbtest where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    
    //bindParamの引数（:nameなど）は4-2でどんな名前のカラムを設定したかで変える必要がある。
    //編集処理
    if(!empty($_POST["editornum"])&&!empty($_POST["rename"]) && !empty($_POST["restr"])){
        $id = $_POST["editornum"]; //変更する投稿番号
        $name = $_POST["rename"];
        $comment = $_POST["restr"]; //変更したい名前、変更したいコメントは自分で決めること
        $sql = 'UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    //表示する
    //$rowの添字（[ ]内）は、4-2で作成したカラムの名称に併せる必要があります。
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].'<br>';
    echo "<hr>";
    }
?>
</body> 
</html>