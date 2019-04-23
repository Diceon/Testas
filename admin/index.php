<?php
// Starting session
session_start();

echo "<pre>";
print_r($_POST);
echo "</pre>";

// Making sure the user is logged in and has sufficient access level
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == TRUE && isset($_SESSION["access"]) && $_SESSION["access"] > 0) {

    if (filter_has_var(INPUT_POST, "action")) {

        // Establishing connection to database
        include_once '../inc/connection.php';
        $connection = new connection('localhost', 'root', '', 'testas');

        // Preparing error array
        $errors = array();

        if (filter_input(INPUT_POST, "action") == "add_question") {

            if (filter_has_var(INPUT_POST, "question_category") && filter_has_var(INPUT_POST, "question_name") && filter_has_var(INPUT_POST, "question_answer_1") && filter_has_var(INPUT_POST, "question_answer_2") && filter_has_var(INPUT_POST, "question_answer_3")) {

                $category = filter_input(INPUT_POST, "question_category", FILTER_SANITIZE_STRING);
                $question = filter_input(INPUT_POST, "question_name", FILTER_SANITIZE_STRING);
                $answer_1 = filter_input(INPUT_POST, "question_answer_1", FILTER_SANITIZE_STRING);
                $answer_2 = filter_input(INPUT_POST, "question_answer_2", FILTER_SANITIZE_STRING);
                $answer_3 = filter_input(INPUT_POST, "question_answer_3", FILTER_SANITIZE_STRING);

                if (empty($category)) {
                    array_push($errors, "Nepasirinkta kategorija!");
                }

                if (empty($question)) {
                    array_push($errors, "Klausimo laukelis yra tuščias!");
                }

                if (empty($answer_1)) {
                    array_push($errors, "Neirašytas pirmas atsakymas!");
                }

                if (empty($answer_2)) {
                    array_push($errors, "Neirašytas antras atsakymas!");
                }

                if (empty($answer_3)) {
                    array_push($errors, "Neirašytas trečias atsakymas!");
                }

                if (count($errors) == 0) {
                    if ($connection->addQuestion($category, $question, $answer_1, $answer_2, $answer_3)) {
                        $success = "Klausimas pridėtas!";
                    }
                }
            } else {
                $categories = $connection->getCategories();
                $show_add_question_form = TRUE;
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title>Administracija </title>
            <link rel="shortcut icon" href="../img/favicon.ico" />
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
            <link rel="stylesheet" href="../css/style.css">
        </head>
        <body>
            <div class="container h-100 d-flex flex-column">
                <div>
                    <nav class="navbar navbar-dark bg-dark">
                        <a class="navbar-brand text-white">Testas</a>
                        <h4 class="text-white">Administracijos pultas</h4>
                        <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="form-inline">
                            <a href="../testas.php"><button type="button" name="logout" class="btn btn-light mx-2">Atgal</button></a>
                            <button type="submit" name="logout" class="btn btn-light mx-2">Atsijungti</button>
                        </form>
                    </nav>
                </div>
                <div class="container">
                    <?php if (isset($errors) && count($errors) > 0) { ?>
                        <?php foreach ($errors AS $error) { ?>
                            <div class="alert alert-danger my-1" role="alert"><?php echo $error; ?></div>
                        <?php } ?>
                    <?php } else if (isset($success)) { ?>
                        <div class="alert alert-success my-1" role="alert"><?php echo $success; ?></div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-3 col-sm-12 border">
                            <h3 class="text-center my-2">Veiksmas:</h3>
                            <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" class="text-center my-1">
                                <div><button type="submit" name="action" value="add_category" class="btn btn-primary my-1">Kurti nauja kategoriją</button></div>
                                <div><button type="submit" name="action" value="add_question" class="btn btn-primary my-1">Kurti nauja klausimą</button></div>
                                <div><button type="submit" name="action" value="add_answer" class="btn btn-primary my-1">Kurti naujus atsakymus</button></div>
                            </form>
                        </div>
                        <div class="container-fluid col-md-9 col-sm-12 border">
                            <?php if (isset($show_add_question_form) && isset($categories)) { ?>
                                <div class="my-2">
                                    <h2 class="text-center ">Naujas klausimas</h2>
                                </div>
                                <form method="post">
                                    <div class="container row">
                                        <div class="col-md-4 col-sm-12 border">
                                            <h3 class="text-center my-2">Kategorija:</h3>
                                            <select name="question_category" class="w-100" required>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?php echo $category["name"]; ?>"><?php echo $category["name"]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-sm-12 border">
                                            <h3 class="text-center my-2">Klausimas:</h3>
                                            <input type="text" name="question_name" class="w-100" placeholder="Klausimas" required>
                                        </div>
                                        <div class="col-md-4 col-sm-12 border">
                                            <h3 class="text-center my-2">Atsakymai:</h3>
                                            <div>
                                                <input type="text" name="question_answer_1" class="w-100 my-1" placeholder="Teisingas atsakymas" required>
                                                <input type="text" name="question_answer_2" class="w-100 my-1" placeholder="Papildomas atsakymas 1" required>
                                                <input type="text" name="question_answer_3" class="w-100 my-1" placeholder="Papildomas atsakymas 2" required>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-block" name="action" value="add_question">Kurti nauja klausimą</button>
                                    </div>
                                </form>
                            <?php } else { ?>
                                <div class="text-center h-100 row">
                                    <h3 class="m-auto">Pasirinkite veiksmą</h3>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </html>
    <?php
} else {
    header('location: ../index.php');
}