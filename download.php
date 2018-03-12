<?php
  include 'config.php';

  if(!$num = $_GET['num']){
    echo '<script>alert("잘못된 접근입니다."); history.back();</script>';
  }

  $sql = "SELECT * from ftp WHERE id='$num'";
  $result = mysqli_query($conn, $sql);
  if(!$result){
    echo "<p>query error</p>";
    exit;
  }
  $row = mysqli_fetch_array($result);

  $dir = "./files/";
  $filename = $row['name'];
  $filehash = $row['hash'];
  $filedown = $row['down'];

  if(file_exists($dir.$filehash)){
    header("Content-Type: Application/octet-stream");
    header("Content-Disposition: attachment; filename=".$filename); //파일 이름 지정
    header("Content-Transfer-Encoding: binary"); //파일 형식 지정
    header("Content-Length: ".filesize($dir.$filehash)); //파일 크기(다운로드 남은 시간 측정 가능하게 )

    $fp = fopen($dir.$filehash, "rb");
    while(!feof($fp)){
      echo fread($fp, 1024);
    }

    if($row['down'] == ""){
      $sql = "UPDATE ftp SET down='1' WHERE id='$num'";
      mysqli_query($conn, $sql);
    } else{
      $sql = "UPDATE ftp SET down=(down+1) WHERE id='$num';";
      mysqli_query($conn, $sql);
    }
    fclose($fp);
    mysqli_close($conn);
    echo "<script>location.reload();</script>";
  } else{
    echo "<script>alert('파일이 존재하지 않습니다.'); history.back();</script>";
  }

 ?>
