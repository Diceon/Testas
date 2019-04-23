<?php
// Starting session
session_start();

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
// Checking if user is logged in
if (!isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] != TRUE) {
    // If not, redirecting to index.php
    header('location: index.php');
    exit();
}

// If user_id is not set, destroying session
if (!isset($_SESSION["user_id"])) {
    session_destroy();
    header('location: index.php');
    exit();
}

// Establishing connection to database
include_once './inc/connection.php';
$connection = new connection('localhost', 'root', '', 'testas');

// Preparing error array
$errors = array();

// Checking if start POST received
if (filter_has_var(INPUT_POST, "start")) {

    // Getting all available categories into array
    $categories = $connection->getCategories();

    // Checking if we got atleast one category
    if (!$categories) {
        array_push($errors, "Nėra kategorijų!");
    }
} else if (filter_has_var(INPUT_POST, "restart")) {

    // Filtering & sanitizing received category name into variable
    $category = filter_input(INPUT_POST, "restart", FILTER_SANITIZE_STRING);

    // Making sure received category name is valid
    if (empty($category)) {
        array_push($errors, "Nepasirinkta kategorija!");
    } else if (!$connection->validateCategory($category)) {
        array_push($errors, "Tokios kategorijos nėra!");
    }

    if (count($errors) == 0) {
        //$connection->restartTest($user_id, $category);
    }
}

// Checking if category POST received
if (filter_has_var(INPUT_POST, "category")) {

    // Filtering & sanitizing received category name into variable
    $category = filter_input(INPUT_POST, "category", FILTER_SANITIZE_STRING);

    // Making sure received category name is valid
    if (empty($category)) {
        array_push($errors, "Nepasirinkta kategorija!");
    } else if (!$connection->validateCategory($category)) {
        array_push($errors, "Tokios kategorijos nėra!");
    }

    // Checking how many errors we got
    if (count($errors) == 0) {

        $questions = $connection->getQuestions($category);

        // Checking if selected category has any questions
        if ($questions && count($questions) > 0) {

            // Starting new test
            $start_result = $connection->startTest($_SESSION["user_id"], $category);

            // Checking test start result, TRUE = started, FALSE = already started
            if ($start_result) {

                // Setting session variables
                $_SESSION["started"] = TRUE;
                $_SESSION["category"] = $category;
                $_SESSION["test_id"] = $start_result;
            } else {
                //$restart = TRUE;
                array_push($errors, "Pasirinkta kategorija jau atsakyta!");
            }
        } else {
            array_push($errors, "Pasirinkta kategorija neturi užtektinai klausimų / atsakymų duomenų bazėję!");
        }
    }
}

// Checking if test is started
if (isset($_SESSION["started"])) {

    // Making sure we got category stored in session variable
    if (!isset($_SESSION["category"]) || empty($_SESSION["category"])) {
        unset($_SESSION["started"]);
    }

    // Checking if answer POST received
    if (filter_has_var(INPUT_POST, "answer")) {

        // Filtering and storing answer into variable
        $answer = filter_input(INPUT_POST, "answer");

        // Checking if answer is valid
        if (empty($answer)) {
            array_push($errors, "Atsakymas tuščias!");
        } else if (!$connection->validateAnswer($answer)) {
            array_push($errors, "Tokio atsakymo nėra!");
        }

        if (count($errors) == 0) {
            $connection->answerTestQuestion($_SESSION["test_id"], $_SESSION["question_id"], $answer);
        }
    }

    // Getting 1 unanswered user question
    $question = $connection->getUnansweredQuestion($_SESSION["user_id"], $_SESSION["category"]);

    // Checking if we got the question
    if ($question) {

        $_SESSION["question_id"] = $question["id"];

        // Getting answers for current question
        $answers = $connection->getAnswers($question["id"]);

        // Shuffling answers
        shuffle($answers);
    } else {
        $finished = $connection->finishTest($_SESSION["test_id"], $_SESSION["user_id"], $_SESSION["category"]);
        if ($finished) {
            $success = TRUE;
            unset($_SESSION["started"]);
        } else {
            array_push($errors, "FINISH TEST ERROR");
        }
    }
}

// checking if 'result' POST received
if (filter_has_var(INPUT_POST, "result")) {

    // if received POST is empty - getting test categories to choose from
    if (empty(filter_input(INPUT_POST, "result", FILTER_SANITIZE_STRING))) {
        $user_tests = $connection->getUserTests($_SESSION["user_id"]);

        // if there is not tests, displaying error
        if (!$user_tests) {
            array_push($errors, "Pirma pradėkite bent vieną testą!");
        }

        // if POST is not empty, attempting to display results
    } else {
        $user_answers = $connection->getUserAnswers($_SESSION["user_id"], filter_input(INPUT_POST, "result", FILTER_SANITIZE_STRING));
    }
}

// checking if 'logout' POST received
if (filter_has_var(INPUT_POST, "logout")) {
    session_destroy();
    header('location: index.php');
    exit();
}

$connection->close();

// Displaying page
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Testas</title>
        <link rel="shortcut icon" href="img/favicon.ico" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="container h-100 d-flex flex-column">
            <div class="w-100">
                <nav class="navbar navbar-dark bg-dark">
                    <a class="navbar-brand text-white">Testas</a>
                    <form method="post" class="form-inline">
                        <?php if (isset($_SESSION["access"]) && $_SESSION["access"] > 0) { ?><a href="admin/"><button type="button" name="admin" class="btn btn-light mx-2">Admin</button></a> <?php } ?>
                        <button type="submit" name="logout" class="btn btn-light mx-2">Atsijungti</button>
                    </form>
                </nav>
            </div>
            <div class="row h-100">
                <div class="m-auto zoom-in">
                    <?php if (isset($success)) { ?>
                        <div class="alert alert-success" role="alert">Testo pabaiga!</div>
                        <div class="text-center">
                            <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-success">Okay...</button></a>
                        </div>
                    <?php } else if (isset($errors) && count($errors) != 0) { ?>
                        <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-dark btn-sm my-3">&larr;Atgal</button></a>
                        <?php foreach ($errors AS $error) { ?>
                            <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
                        <?php } ?>
                        <div class="text-center">
                            <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-warning">Okay</button></a>
                        </div>
                    <?php } else if (isset($categories)) { ?>
                        <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-dark btn-sm my-3">&larr;Atgal</button></a>
                        <h1>Pasirinkite kategoriją:</h1>
                        <form method="post">
                            <?php foreach ($categories AS $category) { ?>
                                <button type="submit" name="category" value="<?php echo $category["name"]; ?>" class="btn btn-primary mx-1"><?php echo $category["name"]; ?></button>
                            <?php } ?>
                        </form>
                    <?php } else if (isset($restart)) { ?>
                        <h2>Pasirinkta kategorija jau atsakyta</h2>
                        <h3>Ar norite pakartoti?</h3>
                        <form method="post">
                            <button type="submit" name="restart" value="<?php echo filter_input(INPUT_POST, "category"); ?>" class="btn btn-primary">Taip</button>
                            <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" name="restart" value="no" class="btn btn-primary mx-1">Ne</button></a>
                        </form>
                    <?php } else if (isset($_SESSION["started"]) && isset($question) && isset($answers)) { ?>
                        <h1><?php echo $question["question"]; ?></h1>
                        <form method="post">
                            <?php foreach ($answers AS $answer) { ?>
                                <button type="submit" name="answer" value="<?php echo $answer["answer"]; ?>" class="btn btn-primary mx-1"><?php echo $answer["answer"]; ?></button>
                            <?php } ?>
                        </form>
                    <?php } else if (isset($user_tests)) { ?>
                        <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-dark btn-sm my-3">&larr;Atgal</button></a>
                        <h1>Pasirinkite testą:</h1>
                        <form method="post">
                            <?php foreach ($user_tests AS $test) { ?>
                                <button type="submit" name="result" value="<?php echo $test["category"]; ?>" class="btn btn-primary mx-1"><?php echo $test["category"]; ?><div><?php echo $test["date"]; ?></div></button>
                            <?php } ?>
                        </form>
                    <?php } else if (isset($user_answers)) { ?>
                        <a href="<?php $_SERVER["PHP_SELF"] ?>"><button type="button" class="btn btn-dark btn-sm my-3">&larr;Atgal</button></a>
                        <h3>Testo rezultatai:</h3>
                        <?php foreach ($user_answers AS $answer) { ?>
                            <?php if ($answer["correct"] == 1) { ?>
                                <div class="alert alert-success my-3" role="alert">
                                    <div class="my-1">Klausimas: <?php echo $answer["question"]; ?></div>
                                    <div class="my-1">Jūsų atsakymas: <?php echo $answer["user_answer"]; ?></div>
                                    <div class="my-1">Teisingas atsakymas: <?php echo $answer["correct_answer"]; ?></div>
                                    <div class="my-1">Atsakymo data: <?php echo $answer["answer_date"]; ?></div>
                                </div>
                            <?php } else { ?>
                                <div class="alert alert-danger" role="alert">
                                    <div class="my-1">Klausimas: <?php echo $answer["question"]; ?></div>
                                    <div class="my-1">Jūsų atsakymas: <?php echo $answer["user_answer"]; ?></div>
                                    <div class="my-1">Teisingas atsakymas: <?php echo $answer["correct_answer"]; ?></div>
                                    <div class="my-1">Atsakymo data: <?php echo $answer["answer_date"]; ?></div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    <?php } else { ?><h1>Sveiki, <?php echo $_SESSION["username"]; ?></h1>
                        <form method="post">
                            <button type="submit" name="start" class="btn btn-primary mx-1">Pradėti testą</button>
                            <button type="submit" name="result" class="btn btn-primary mx-1">Peržiurėti rezultatą</button>
                        </form>
                    <?php } ?></div>
            </div>
        </div>
    </body>
</html>
