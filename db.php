<?php


$hostname = 'localhost';
$dbname = 'cooltime';
$user = 'root';
$pass = '';

session_start();

function dbh($pass, $user, $hostname, $dbname) {
    try {
        return new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pass);
    }
    catch(PDOException $e) {
        echo $e->getMessage();
        die();
    }
}


function printItems($sql, $exp) {
    foreach ($sql as $row) {
        print '<div class="item">
                        <div class="item-header">
                            <div class="item-header-info">
                                <span class="item-title">'.$row['event_title'].'</span>
                                <span class="item-address">'.$row['event_address'].'</span>
                            </div>

                            <div class="item-header-info">
                                <span class="item-date">'.$row['event_date'].'</span>
                                <span class="item-time">'.$row['event_time_start'].'-'.$row['event_time_end'].'</span>
                            </div>
                        </div>

                        <div class="item-img">
                            <img src="'.$row['event_imgs'].'" alt="'.$row['event_title'].'">
                        </div>

                        <div class="item-booked">
                            <span class="item-booked-by">Забронировано: '.$row['users_fio'].'</span>
                            <span class="item-booked-visiters">'.$row['booking_olders'].' Взрослых, '.$row['booking_children'].' ребенка ('.$row['booking_ages'].'+)</span>
                            <span>';
        if ($row['services_birthday']) print 'День рождения';
        if ($row['services_cake']) print ', ';
        if ($row['services_cake']) print 'Торт';
        if ($row['services_animators'] && ($row['services_birthday'] || $row['services_cake'])) print ', ';
        if ($row['services_animators']) print 'Аниматор';

        print                        '</span>
                        </div>

                        <div class="item-footer">
                            <span class="item-price">'.($row['booking_olders']*$row['event_price_old']+$row['booking_children']*$row['event_price_child']).' рублей</span>';

        if (!$exp) {
            print               '<a href="booking_management.php?id='.$row['booking_id'].'" class="item-btn">
                                    <span class="item-btn-text">Управление бронированием</span>
                                </a>';
        }
        print           '</div>
                    </div>';
    }
}


function printEvents($sql) {
    foreach ($sql as $row) {
        print '<div class="item">
                    <div class="item-header">
                        <div class="item-header-info">
                            <span class="item-title">'.$row['event_title'].'</span>
                            <span class="item-address">'.$row['event_address'].'</span>
                        </div>

                        <div class="item-header-info">
                            <span class="item-date">'.$row['event_date'].'</span>
                            <span class="item-time">'.$row['event_time_start'].'-'.$row['event_time_end'].'</span>
                        </div>
                    </div>

                    <div class="item-img">
                        <img src="'.$row['event_imgs'].'" alt="'.$row['event_title'].'">
                    </div>

                    <div class="item-booked">
                        <span class="item-booked-by">'.(($row['event_type'] == 1) ? "Мероприятие" : "Объект").'</span>
                        <span class="item-booked-visiters">Взрослый: '.$row['event_price_old'].', Детский: '.$row['event_price_child'].'</span>
                        <span>'.$row['event_description'].'</span>
                    </div>

                    <div class="item-footer">
                        <a href="booking.php?id='.$row['event_id'].'" class="item-btn">
                            <span class="item-btn-text">Бронировать</span>
                        </a>
                   </div>
                </div>';
    }
}


class Profile {
    public $dbh;

    function __construct($dbh) {
        $this->dbh = $dbh;
    }

    public function isAuth() {
        if (isset($_SESSION["is_auth"])) {
            return $_SESSION["is_auth"];
        }
        else return false;
    }

    public function out() {
        $_SESSION = array();
        session_destroy();
    }

    function getName($id) {
        return $this->dbh->query('SELECT users_fio FROM users WHERE users_id = '.$id)->fetch()['users_fio'];
    }

    function getLogin($id) {
        return $this->dbh->query('SELECT users_login FROM users WHERE users_id = '.$id)->fetch()['users_login'];
    }

    function getBooked($id) {
        $sql = $this->dbh->query('SELECT `event`.*, `booking`.*, `users`.`users_id`, `users`.`users_fio`, `services`.`services_booking`, `services`.*
        FROM `event` 
            LEFT JOIN `booking` ON `booking`.`booking_event` = `event`.`event_id` 
            LEFT JOIN `users` ON `booking`.`booking_user` = `users`.`users_id` 
            LEFT JOIN `services` ON `services`.`services_booking` = `booking`.`booking_id`
        WHERE `booking`.`booking_user` = '.$id.' AND `services`.`services_booking` = `booking`.`booking_id` AND `event`.`event_time_end` > CURRENT_TIMESTAMP')->fetchAll();
        
        printItems($sql, FALSE);
    }

    function getVisited($id) {
        $sql = $this->dbh->query('SELECT `event`.*, `booking`.*, `users`.`users_id`, `users`.`users_fio`, `services`.`services_booking`, `services`.*
        FROM `event` 
            LEFT JOIN `booking` ON `booking`.`booking_event` = `event`.`event_id` 
            LEFT JOIN `users` ON `booking`.`booking_user` = `users`.`users_id` 
            LEFT JOIN `services` ON `services`.`services_booking` = `booking`.`booking_id`
        WHERE `booking`.`booking_user` = '.$id.' AND `services`.`services_booking` = `booking`.`booking_id` AND `event`.`event_time_end` < CURRENT_TIMESTAMP')->fetchAll();
        
        printItems($sql, TRUE);
    }
}


class Booking {
    public $dbh;

    function __construct($dbh) {
        $this->dbh = $dbh;
    }

    function getEvent($id) {
        return $this->dbh->query('SELECT `event_title`, `event_date`, `event_imgs`, `event_price_old`, `event_price_child` FROM `event` WHERE `event_id` = '.$id)->fetch();
    }

    function insertBooked($e) {        
        $birthday = 0;
        $cake = 0;
        $animators = 0;

        if (isset($e['birthday'])) $birthday = 1;
        if (isset($e['cake'])) $cake = 1;
        if (isset($e['animators'])) $animators = 1;

        $this->dbh->query("BEGIN;
        INSERT INTO `booking` (`booking_id`, `booking_event`, `booking_user`, `booking_phone`, `booking_olders`, `booking_children`, `booking_ages`, `booking_description`) 
        VALUES (NULL, '".$e['event']."', '".$e['user']."', '".$e['phone']."', '".$e['olders']."', '".$e['children']."', '".$e['age']."', '".$e['desc']."');
        INSERT INTO `services` (`services_id`, `services_booking`, `services_birthday`, `services_cake`, `services_animators`) VALUES (NULL, LAST_INSERT_ID(), '".$birthday."', '".$cake."', '".$animators."');
        COMMIT;");
    }

    function getBooked($id) {
        return $this->dbh->query('SELECT `booking`.*, `event`.`event_title`, `event`.`event_date`, `services`.*, `booking`.`booking_id`, `event`.`event_imgs`
        FROM `booking` 
            LEFT JOIN `event` ON `booking`.`booking_event` = `event`.`event_id` 
            LEFT JOIN `services` ON `services`.`services_booking` = `booking`.`booking_id`
        WHERE `booking`.`booking_event` = `event`.`event_id` AND `services`.`services_booking` = `booking`.`booking_id` AND `booking`.`booking_id` = '.$id)->fetch();
    }

    function updateBooked($e) {
        $this->dbh->query('UPDATE `booking` SET `booking_phone` = "'.$e['phone'].'", `booking_olders` = '.$e['olders'].', `booking_children` = '.$e['children'].', `booking_ages` = '.$e['age'].', `booking_description` = "'.$e['desc'].'" WHERE `booking`.`booking_id` = '.$e['id']);
        
        $birthday = 0;
        $cake = 0;
        $animators = 0;

        if (isset($e['birthday'])) $birthday = 1;
        if (isset($e['cake'])) $cake = 1;
        if (isset($e['animators'])) $animators = 1;
        
        $this->dbh->query('UPDATE `services` SET `services_birthday` = '.$birthday.', `services_cake` = '.$cake.', `services_animators` = '.$animators.' WHERE `services`.`services_booking` = '.$e['id']);
    }
}


class Search {
    public $dbh;

    function __construct($dbh) {
        $this->dbh = $dbh;
    }

    function searchEvent($e) {
        if (!empty($e['title']) && !empty($e['date'])) {
            printEvents($this->dbh->query('SELECT * FROM `event` WHERE `event_title` LIKE "%'.$e['title'].'%" AND `event_date` = "'.$e['date'].'"')->fetchAll());
        } elseif (!empty($e['title'])) {
            printEvents($this->dbh->query('SELECT * FROM `event` WHERE `event_title` LIKE "%'.$e['title'].'%"')->fetchAll());
        } elseif (!empty($e['date'])) {
            printEvents($this->dbh->query('SELECT * FROM `event` WHERE `event_date` = "'.$e['date'].'"')->fetchAll());
        }
    }

    function getEvent() {
        printEvents($this->dbh->query('SELECT * FROM `event` WHERE `event_date` >= CURRENT_DATE')->fetchAll());
    }
}