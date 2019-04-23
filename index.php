<?php
// Starting session
session_start();

// Checking if user is already logged in, redirecting if true
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == TRUE) {
    header('location: testas.php');
} else if (filter_has_var(INPUT_POST, "username") && filter_has_var(INPUT_POST, "password")) {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $errors = array();

    if (empty($username)) {
        array_push($errors, "Prisijungimo vardas negali būti tuščias!");
    }

    if (empty($password)) {
        array_push($errors, "Slaptažodis negali būti tuščias!");
    }

    if (count($errors) == 0) {

        // Establishing connection to database
        include_once './inc/connection.php';
        $connection = new connection('localhost', 'root', '', 'testas');

        $login = $connection->validateLogin($username, $password);


        if ($login) {

            $unfinished_test = $connection->getUnfinishedTests($login["id"]);

            if ($unfinished_test) {
                $_SESSION["started"] = TRUE;
                $_SESSION["test_id"] = $unfinished_test["id"];
                $_SESSION["category"] = $unfinished_test["name"];
            }

            $_SESSION["logged_in"] = TRUE;
            $_SESSION["user_id"] = $login["id"];
            $_SESSION["username"] = $username;
            $_SESSION["access"] = $login["access"];

            $connection->close();
            header('location: testas.php');
        } else {
            $connection->close();
            array_push($errors, "Neteisingas prisijungimo vardas arba slaptažodis!");
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Prisijungimas | Testas</title>
        <link rel="shortcut icon" href="img/favicon.ico">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container h-100 d-flex flex-column">
            <div class="w-100">
                <nav class="navbar navbar-dark bg-dark">
                </nav>
            </div>
            <div class="row h-100">
                <div class="m-auto login-form zoom-in">
                    <h1>Testas</h1>
                    <?php if (isset($errors)) { ?>
                        <?php foreach ($errors as $value) { ?>
                            <div class="alert alert-danger" role="alert"><?php echo $value ?></div>
                        <?php } ?>
                    <?php } ?><form method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" aria-describedby="username" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
