<?php

require_once 'db.php';

$dbh = dbh($pass, $user, $hostname, $dbname);

$search = new Search($dbh);

?>




<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/profile.css">
</head>
<body>
    <main>
    <section class="view">
			<div class="search">
				<form action="search.php" class="search">
					<input class="search__inp" type="text" name="title">
					<div class="date">
						<input type="date" name="date" id="date">
					</div>
					<input type="submit" value="Найти" class="search__btn">
				</form>
			</div>
		</section>
        
        <section class="present">
            <div class="items">
                <?php
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $search->searchEvent($_POST);
                    } else {
                        parse_str($_SERVER['QUERY_STRING'], $url);
                        if ((isset($url['title']) && !empty($url['title'])) || (isset($url['date']) && !empty($url['title']))) {
                            $search->searchEvent($url);
                        } else {
                            $search->getEvent();
                        }
                    }
                ?>
            </div>
        </section>
    </main>
</body>
</html>