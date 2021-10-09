<!DOCTYPE html> 
<html> 
<head> 
<meta charset="utf-8" /> 
<title>mission_3-5</title>
</head> 
<body> 

<?php
$date = date("Y/m/d H:i:s");
$fn = "m3-5.txt";
$editormessage= "";

if(!empty($_POST["editornum"])){
    $editornum = $_POST["editornum"];
    if(file_exists($fn)){
        $LINE = file($fn, FILE_IGNORE_NEW_LINES);
        foreach($LINE as $line){
            //$lineは投稿番号<>名前<>コメント<>投稿日時という文字列
            //explodeで文字列を配列に変える,
            //$data = array(投稿番号, 名前, コメント, 投稿日時)
            $data = explode("<>", $line);
            if($data[0] == $editornum){
                $numexist = 1;
                if($data[4] == $_POST["editorpassword"]){//!empty($_POST["editorpassword"]) && 
                    $editorpassword = $_POST["editorpassword"];
                    $rename = $data[1];
                    $recomment = $data[2];
                    $reeditorpassword = $data[4];
                    $editormessage = $data[0]."の投稿を編集します。名前とコメントを編集し送信を押して下さい<br>";
                    $numexist = 2;
                }elseif(empty($_POST["editorpassword"])){
                    $editormessage = "パスワードが入力されていません<br>";
                }else{
                    $editormessage = "パスワードが違うので".$data[0]."の投稿は編集できません<br>";
                }
            }
        }
        if(empty($numexist)){
            $editormessage = $editornum."の投稿は存在しません<br>";
        }
    }else{
        $editormessage = "ファイルが存在しません<br>";
    }
}
?>

<form action="" method="post">
    入力フォーム<br>
    <input type="text" name="name" placeholder="名前" 
    value="<?php 
    if(!empty($rename)){
        echo $rename;
    }
    ?>"><br>
    <input type="text" name="str" placeholder="コメント"
    value="<?php 
    if(!empty($recomment)){
        echo $recomment;
    }
    ?>"><br>
    <input type="password" name="password" placeholder="パスワード"
    value="<?php 
    if(!empty($reeditorpassword)){
        echo $reeditorpassword;
    }
    ?>"><br>
    <input type="submit" name="submit">
    <input type="hidden" name="hiddennum" placeholder="編集する番号" 
    value="<?php 
    if(!empty($editornum) && $numexist == 2){
        echo $editornum;
    }else{
        echo "";
    }
    ?>"><br>
</form>
<form action="" method="post">
    削除フォーム<br>
    <input type="number" name="deletenum" placeholder="削除対象番号"><br>
    <input type="password" name="deletepassword" placeholder="パスワード"><br>
    <input type="submit" name="delete" value="削除">
</form>
<form action="" method="post">
    編集番号指定用フォーム<br>
    <input type="number" name="editornum" placeholder="編集対象番号"><br>
    <input type="password" name="editorpassword" placeholder="パスワード"><br>
    <input type="submit" name="editor" value="編集">
</form>

<?php
$message = "";
//フォームに入力された内容をファイルに追記する
if(!empty($_POST["name"]) && !empty($_POST["str"])){
    if(file_exists($fn)){
        $num = count(file($fn))+1;
    }else{
        //初期状態(ファイルが無い状態)だとカウントできないから
        $num = 1;
    }
    $name = $_POST["name"];
    $str = $_POST["str"];
    if(!empty($_POST["password"])){
        $password = $_POST["password"];
    }else{
        $password = "";
    }
    //編集番号が入力されていないとき
    if(empty($_POST["hiddennum"])){
        $fp = fopen($fn, "a");
        fwrite($fp, $num."<>".$name."<>".$str."<>".$date."<>".$password."<>".PHP_EOL);
        $message = "正常に送信されました<br>";
    }else{
        //編集番号が入力されているとき
        $hiddennum = $_POST["hiddennum"];
        $LINE = file($fn, FILE_IGNORE_NEW_LINES);
        $fp = fopen($fn, "w");
        foreach($LINE as $line){
            //$lineは投稿番号<>名前<>コメント<>投稿日時という文字列
            //explodeで文字列を配列に変える,
            //$data = array(投稿番号, 名前, コメント, 投稿日時)
            $data = explode("<>", $line);
            //編集番号と一致しなかったら$lineを書き込む. 
            if($data[0] != $hiddennum){
                fwrite($fp, $line.PHP_EOL);
            }else{
                fwrite($fp, $hiddennum."<>".$name."<>".$str."<>".$date."<>".$password."<>"."(編集済み)"."<>".PHP_EOL);
                $message = $data[0]."の投稿は編集されました<br>";
            }
        }
    }
    fclose($fp);
}

$deletemessage= "";
//投稿番号の中で削除対象番号と一致しているものを
//テキストファイルから消す。ブラウザにも表示しない
if(!empty($_POST["deletenum"])){
    $deletenum = $_POST["deletenum"];
    if(!empty($_POST["password"])){
        $password = $_POST["password"];
    }else{
        $password = "";
    }
    if(file_exists($fn)){
        $LINE = file($fn, FILE_IGNORE_NEW_LINES);
        $fp = fopen($fn, "w");
        foreach($LINE as $line){
            //$lineは投稿番号<>名前<>コメント<>投稿日時という文字列
            //explodeで文字列を配列に変える,
            //$data = array(投稿番号, 名前, コメント, 投稿日時)
            $data = explode("<>", $line);
            //削除番号と一致しなかったら$lineを書き込む. 
            if($data[0] == $deletenum){
                $numexist = 1;
                if($data[4] == $_POST["deletepassword"]){
                    fwrite($fp, "この投稿は削除されました".PHP_EOL);
                    $deletemessage = $data[0]."の投稿は削除されました<br>";
                }elseif(empty($_POST["deletepassword"])){
                    $deletemessage = "パスワードが入力されていません<br>";
                    fwrite($fp, $line.PHP_EOL);
                }else{
                    $deletemessage = "パスワードが違うので".$data[0]."の投稿は削除できません<br>";
                    fwrite($fp, $line.PHP_EOL);
                }
            }else{
                fwrite($fp, $line.PHP_EOL);
            }
        }fclose($fp);
        if(empty($numexist)){
            $deletemessage = $deletenum."の投稿は存在しません<br>";
        }
    }else{
        $deletemessage = "ファイルが存在しません<br>";
    }
}
if(empty($editormessage) && empty($deletemessage)){
    if(empty($_POST["name"]) && empty($_POST["str"])){
        $message = "名前とコメントを入力して下さい<br>";
    }elseif(empty($_POST["name"])){
        $message = "名前を入力して下さい<br>";
    }elseif(empty($_POST["str"])){
        $message = "コメントを入力して下さい<br>";
    }
}
echo $editormessage;
echo $deletemessage;
echo $message;
//ファイルの内容をブラウザに表示
//初回読み込み時はファイルが生成されてないのでfile_existsを使う
if(file_exists($fn)){
    //file()は各行を配列に格納する
    //FILE_IGNORE_NEW_LINES 最後の改行を省略する
    $LINE = file($fn, FILE_IGNORE_NEW_LINES);
    foreach($LINE as $line){
        //$lineは投稿番号<>名前<>コメント<>投稿日時という文字列
        //explodeで文字列を配列に変える,
        //$data = array(投稿番号, 名前, コメント, 投稿日時)
        $data = explode("<>", $line);
        //削除された投稿は$data[0]しかない(配列の長さが1)
        if(count($data)>1){
            //投稿番号, 名前, コメント, 投稿日時で4回繰り返す
            for($i=0;$i<=3;$i++){
                echo $data[$i]."  ";
            }if(!empty($data[5])){
                echo $data[5]."  ";
            }
            echo "<br>";
        }
    }
}

?>
</body> 
</html>