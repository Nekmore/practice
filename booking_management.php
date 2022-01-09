<?php
session_start();
require_once 'db.php';

$dbh = dbh($pass, $user, $hostname, $dbname);

$booking = new Booking($dbh);
$profile = new Profile($dbh);

if (!$profile->isAuth()) {
    header('Location: /login.html');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking->updateBooked($_POST);
    header('Location: /profile.php');
} else {
    parse_str($_SERVER['QUERY_STRING'], $url);
    $e = $booking->getBooked($url['id']);
}

?>



<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление бронированием
    </title>
    <link href="css/main.css" rel="stylesheet" media="all">
</head>

<body>
    <div class="page-wrapper bg-red p-t-180">
        <div class="wrapper wrapper--w960">
            <div class="card card-2">
                <div class="card-heading" style="background: url('<?php print $e['event_imgs']; ?>') top left/cover no-repeat;"></div>
                <div class="card-body">
                    <h2 class="title">Управление бронированием</h2>
                    <div class="event">
                        <span><?php print $e['event_title']; ?></span>
                        <span>Дата: <?php print $e['event_date']; ?></span>
                    </div>
                    <form action="booking_management.php" method="POST">
                        <?php
                            print '<input type="hidden" name="id" value="'.$url['id'].'">';
                        ?>
                        <div class="input-group">
                            <label for="phone">
                                <span>Телефон</span>
                                <input class="input--style-2" type="text" placeholder="Телефон" name="phone"  id="phone" value="<?php  print $e['booking_phone']; ?>" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="olders">
                                <span>Количество взрослых</span>
                                <input class="input--style-2" id="olders" type="number" placeholder="Количество взрослых" name="olders" min="1" value="<?php print $e['booking_olders']; ?>" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="children">
                                <span>Количество детей</span>
                                <input class="input--style-2" id="children" type="number" placeholder="Количество детей" name="children" min="1" value="<?php print $e['booking_children']; ?>" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="age">
                                <span>Возраст детей</span>
                                <input class="input--style-2" id="age" type="number" placeholder="Возраст детей" name="age" min="1" value="<?php print $e['booking_ages']; ?>" required>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="desc">
                                <span>Описание</span>
                                <input class="input--style-2" type="text" placeholder="Описание" name="desc" id="desc" minlength="5" value="<?php print $e['booking_description']; ?>">
                                <span class="error" aria-live="polite"></span>
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="birthday" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="birthday" id="birthday" <?php if($e['services_birthday']) print ' checked'; ?>>
                                День рождения
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="cake" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="cake" id="cake" <?php if($e['services_cake']) print ' checked'; ?>>
                                Торт
                            </label>
                        </div>
                        <div class="input-group">
                            <label for="animators" class="input-checkbox">
                                <input class="input--style-2" type="checkbox" name="animators" id="animators" <?php if($e['services_animators']) print ' checked'; ?>>
                                Аниматоры
                            </label>
                        </div>
                        <div class="p-t-30">
                            <input class="btn btn--radius btn--green" type="submit" placeholder="Далее">
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
