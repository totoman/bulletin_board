<?php

session_start();

require 'database.php';

if( isset($_SESSION['user_id']) ){

    $records = $conn->prepare('SELECT id,email,password FROM users WHERE id = :id');
    $records->bindParam(':id', $_SESSION['user_id']);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    $user = NULL;

    if( count($results) > 0) {
        $user = $results;
    }
}

    if(isset($_POST['search_word']) ){
          $search_word = $_POST['search_word'];
          $like_search_word = "'%".$search_word."%'";
          $sql = "SELECT * FROM threads WHERE title LIKE " . $like_search_word . " order by created_at desc";
          $result = $conn->query($sql);
    }else{
          $sql = "SELECT * FROM threads order by created_at desc";
          $result = $conn->query($sql);

          $type = (isset($_POST['type']))? $_POST['type'] : null;
          $id = (isset($_POST['id']))? $_POST['id'] : null;

          if($type=='delete' && isset($id)) {

            $sql_thread = "DELETE FROM threads WHERE id=' . $id '";
            $result_thread = $conn->query($sql_thread);

            header("Location: index.php");
          }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
    <title>TOP</title>
</head>

<body>
    <div class="header">
        <a href="/">Bulletin Board</a>
    </div>

    <?php if( !empty($user) ): ?>

        <br />Welcome <?= $user['email']; ?>
        <br /><br />You are successfully logged in!
        <br /><br />

        <a href="logout.php" onclick="return confirm('Are you sure?');">Logout?</a> /
        <a href="user_edit.php">Change profile?</a> /
        <a href="unsubscribe.php" onclick="return confirm('Are you sure?\nIt cannot be undone.');">Unsubscribe?</a> /
        <a href="user_list.php">User list</a>

    <?php else: ?>

        <h1>Please Login or Register</h1>
        <a href="login.php">Login</a> or
        <a href="register.php">Register</a>

    <?php endif; ?>

    <form method="post" action="index.php">

      <input type="text" placeholder="search word here." name="search_word">
      <input type="submit" value="検索">

    </form>

    <p><a href="thread_new.php">スレッド作成</a></p>

    <table align="center">
    <?php while($thread = $result->fetch() ): ?>
        <tr>
            <td class="thread-list">
                <a href="thread.php?id=<?php echo $thread['id'];?>"><?php echo $thread['title'];?></a>
            </td>
            <td><?php echo $thread['created_at'];?></td>
            <td>
                <form method='post' action='thread_delete.php' onsubmit="return confirm('Are you sure?\nIt cannot be undone.');">
                    <td><input type="hidden" name="type" value="delete" /></td>
                    <td><input type="hidden" name="id" value=<?php echo $thread['id'];?> /></td>
                    <td><input class="delete-button" type="submit" name="submit" value="削除" /></td>
                </form>
            </td>
        </tr>
    <?php endwhile;?>
    </table>
</body>
</html>