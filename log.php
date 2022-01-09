<?php

require_once 'db.php';
session_start();
$dbh = dbh($pass, $user, $hostname, $dbname);

$sql = $dbh->query('SELECT * FROM users WHERE users_login = "'.strval($_POST['login']).'"');

foreach ($sql as $row) {
    if ($_POST['login'] == $row['users_login'] & $_POST['pass'] == $row['users_password']) {
        $_SESSION['is_auth'] = TRUE;
        $_SESSION['id'] = $row['users_id'];
        print "Добро пожаловать, ".$row['users_fio'];
        $dbh = null;
        header("Location: /profile.php");

    } else {
        print "Скройся, немафиозник</br>";
        print '<a href="login.html">Вернуться</a>';
        $dbh = null;
        $_SESSION['is_auth'] = FALSE;
    }
}

exit();