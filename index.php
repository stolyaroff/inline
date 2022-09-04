<?php
include_once 'dbconnect.php';

//Минимальное колличество символов для поиска
const MIN_CHARACTERS_SEARCH = 3;


if($_POST['submit']){
  if (iconv_strlen($_POST['find'])>=MIN_CHARACTERS_SEARCH){
      $find = htmlspecialchars($_POST['find']);
      $query = $pdo->prepare('SELECT p.title, c.body FROM posts as p LEFT JOIN comments c on p.id = c.postId WHERE c.body LIKE :keyword');
      $query->bindValue(':keyword', '%'.$find.'%', PDO::PARAM_STR);
      $query->execute();
      $results = $query->fetchAll();
      $rowsSearch = $query->rowCount();
  }else{
    echo '<p style="color: red">Минимальная длина поискового запроса 3 символа</p>';
  }
}
?>

<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>find</title>
</head>
<body style="font-family: sans-serif">
<form action="" method="post">
    <input type="text" name="find">
    <input type="submit" name="submit">
</form>
<?php
if ($_POST['submit'] && iconv_strlen($_POST['find'])>=MIN_CHARACTERS_SEARCH ){
  if($rowsSearch != 0){?>
    <p>По запросу "<?= $_POST['find']?>" найдено:</p>
    <?php for ($i=0;$i<$rowsSearch;$i++){?>
        <p><span style="font-size: 1.25em">Заголовок записи:</span> <?= $results[$i]['title'] ?></p>
      <p><span style="font-size: 1.25em">Комментарий:</span> <?= $results[$i]['body'] ?></p>
      <?php }
  } else {?>
    <p>По запросу "<?= $_POST['find']?>" ничего не найдено</p>
<?php }}?>
</body>
</html>