<?php

require_once 'db.php';

$dbh = dbh($pass, $user, $hostname, $dbname);

$booking = new Booking($dbh);
$profile = new Profile($dbh);

if (!$profile->isAuth()) {
    header('Location: /login.html');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking->insertBooked($_POST);
    header('Location: /profile.php');
} else {
    parse_str($_SERVER['QUERY_STRING'], $url);
    $e = $booking->getEvent($url['id']);
}

?>



<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Забронировать мероприятие</title>
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="page-wrapper bg-red p-t-180">
        <div class="wrapper wrapper--w960">
            <div class="card card-2">
                <div class="card-heading" style="background: url('<?php print $e['event_imgs']; ?>') top left/cover no-repeat;"></div>
                <div class="card-body">
                    <h2 class="title">Забронировать мероприятие</h2>
                    <div class="event">
                        <span><?php print $e['event_title']; ?></span>
                        <span>Дата: <?php print $e['event_date']; ?></span>
                    </div>
                    <form action="booking.php" method="POST">
                        <?php
                            print '<input type="hidden" name="event" value="'.$url['id'].'">';
                            print '<input type="hidden" name="user" value="'.$_SESSION['id'].'">';
                        ?>
                        <div class="input-group">
                            <label for="phone">
                                <span>Телефон</span>
                                <input class="input--style-2" type="text" placeholder="Телефон" name="phone"  id="phone" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="olders">
                                <span>Количество взрослых</span>
                                <input class="input--style-2" id="olders" type="number" placeholder="Количество взрослых" name="olders" min="1" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="children">
                                <span>Количество детей</span>
                                <input class="input--style-2" id="children" type="number" placeholder="Количество детей" name="children" min="1" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="age">
                                <span>Возраст детей</span>
                                <input class="input--style-2" id="age" type="number" placeholder="Возраст детей" name="age" min="1" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="desc">
                                <span>Описание</span>
                                <input class="input--style-2" type="text" placeholder="Описание" name="desc" id="desc" minlength="5">
                                <span class="error" aria-live="polite"></span>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="birthday" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="birthday" id="birthday">
                                День рождения
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="cake" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="cake" id="cake">
                                Торт
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="animators" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="animators" id="animators">
                                Аниматоры
                            </label>
                        </div>
                        <div class="p-t-30">
                            <input class="btn btn--radius btn--green" type="submit" value="Далее">
                        </div>
                    </form>
                    <div class="links">
                        <a href="#">Правила посещения</a>
                        <a href="#">Актуальные инструкции по противодействию новой короновирусной инфекции Covid-19</a>
                        <a href="profile.php">Вернуться в личный кабинет</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>

</html>
