<?php
session_start();
require_once 'db.php';

$dbh = dbh($pass, $user, $hostname, $dbname);
$profile = new Profile($dbh);

if (!$profile->isAuth()) {
    header('Location: /login.html');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile->out();
    header('Location: /index.html');
}

parse_str($_SERVER['QUERY_STRING'], $url);
$id = $_SESSION['id'];

?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет CoolTime</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <header>
        <div class="wrapper">
            <div class="find">
                <img src="/img/icons/search.svg" alt="Найти мероприятия">
                <a href="search.php">Найти мероприятия</a>
            </div>
    
            <div class="user">
                <span><?php print $profile->getName($id).' ('.$profile->getLogin($id).')'; ?></span>
                <form action="profile.php" method="POST">
                    <input type="hidden" name="out">
                    <input type="submit" value="Выход">
                </form>
            </div>
        </div>
    </header>

    <main>
        <div class="planned">
            <h2>Запланированные мероприятия</h2>
            <div class="items">
                <?php $profile->getBooked($id); ?>
            </div>
        </div>

        <div class="visited">
            <h2>Посещенные мероприятия</h2>
            <div class="items">
                <?php $profile->getVisited($id); ?>
            </div>
        </div>
    </main>
</body>
</html>


