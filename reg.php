<?php
session_start();
require_once 'db.php';
$dbh = dbh($pass, $user, $hostname, $dbname);
$sql = 'INSERT INTO `users` (`users_fio`, `users_login`, `users_password`, `users_age`, `users_email`) VALUES (:fio, :login, :pass, :age, :email)';

$stmt = $dbh ->prepare($sql);
$stmt->bindParam(':fio', $_POST['fio']);
$stmt->bindParam(':login', $_POST['login']);
$stmt->bindParam(':pass', $_POST['pass']);
$stmt->bindParam(':age', $_POST['age']);
$stmt->bindParam(':email', $_POST['email']);
$stmt->execute();

$sql = $dbh->query('SELECT * FROM users WHERE users_login = "'.strval($_POST['login']).'"');

foreach ($sql as $row) {
    if ($_POST['login'] == $row['users_login']) {
        $_SESSION['id'] = $row['users_id'];
        $_SESSION['is_auth'] = TRUE;
        $dbh = null;
        header("Location: /profile.php");
    }
}


