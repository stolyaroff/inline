<?php
include_once 'dbconnect.php';

// Функция сравнивает массивы из БД и Сервера по ID. Возвращает массив ID которых нет в БД
function getDiffId($array, $arrayDB)
{
    if (!empty($arrayDB)) {
        foreach ($array as $v) {
            $p[] = $v['id'];
        }
        foreach ($arrayDB as $v) {
            $p2[] = intval($v['id']);
        }

        $resultID = array_diff($p, $p2);
        foreach ($array as $value) {
            foreach ($resultID as $id) {
                if ($value['id'] == $id) {
                    $arrayResult[] = $value;
                }
            }
        }
        return $arrayResult;
    } else {
        return $array;
    }
}

//Получаем массив данных с сервера
$posts = json_decode(file_get_contents("https://jsonplaceholder.typicode.com/posts"), true);

//Получаем массив данных из БД
$queryPosts = $pdo->prepare('SELECT * FROM posts');
$queryPosts->execute();
$postsDB = $queryPosts->fetchAll();

//Находим разницу данных в БД и на сервере по ID
$postsResult = getDiffId($posts, $postsDB);

//Добавляем недостающие записи в БД
if (isset($postsResult) && (count($postsResult)) != 0) {
    foreach ($postsResult as $v) {
        $userId = $v['userId'];
        $idPost = $v['id'];
        $titlePost = $v['title'];
        $bodyPost = $v['body'];
        $sql = 'INSERT INTO posts (id, userID, title, body) VALUES (:id, :userID, :title, :body)';
        $params = [':id' => $idPost, ':userID' => $userId, ':title' => $titlePost, ':body' => $bodyPost];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}

//Получаем массив данных с сервера
$comments = json_decode(file_get_contents("https://jsonplaceholder.typicode.com/comments"), true);

//Получаем массив данных из БД
$queryComments = $pdo->prepare('SELECT * FROM comments');
$queryComments->execute();
$commentsDB = $queryComments->fetchAll();

//Находим разницу данных в БД и на сервере по ID
$commentsResult = getDiffId($comments, $commentsDB);

//Добавляем недостающие записи в БД
if (isset($commentsResult) && (count($commentsResult)) != 0) {
    foreach ($commentsResult as $v) {
        $postId = $v['postId'];
        $id = $v['id'];
        $name = $v['name'];
        $email = $v['email'];
        $body = $v['body'];
        $sql = 'INSERT INTO comments (postId, id, name, email, body) VALUES (:postId, :id, :name, :email, :body)';
        $params = ['postId' => $postId, ':id' => $id, ':name' => $name, ':email' => $email, ':body' => $body];
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
}

//Выводим сообщение в консоль
if ($postsResult && $commentsResult) {
    echo "<script>console.log('Добавлено записей-" . count($postsResult) . " и комментариев-" . count($commentsResult) . "');</script>";
} elseif ($postsResult) {
    echo "<script>console.log('Добавлено записей-" . count($postsResult) . " и комментариев-0');</script>";
} elseif ($commentsResult) {
    echo "<script>console.log('Добавлено записей-0 и комментариев-" . count($commentsResult) . "');</script>";
} else {
    echo "<script>console.log('Новых записей нет');</script>";
}