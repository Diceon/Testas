<?php

class connection {

    private $db;

    function __construct($hostname, $username, $password, $database) {
        $this->db = mysqli_connect($hostname, $username, $password, $database);

        if (mysqli_connect_errno()) {
            printf("Database connection error: %s\n", mysqli_connect_error());
            exit();
        } else {
            mysqli_set_charset($this->db, "utf8");
        }
    }

    function showError() {
        printf("DB ERROR: %s\n", mysqli_error($this->db));
        exit();
    }

    function close() {
        $this->db->close();
    }

    function validateLogin($username, $password) {
        $login = "SELECT id, access FROM users WHERE username = '" . $username . "' AND password = '" . $password . "' LIMIT 1";
        $login_result = mysqli_query($this->db, $login);

        if ($login_result) {
            if (mysqli_num_rows($login_result) > 0) {
                return mysqli_fetch_array($login_result);
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function getCategories() {
        $query = "SELECT * FROM categories";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                $categories = array();

                while ($category = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    array_push($categories, $category);
                }

                return $categories;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function validateCategory($category) {
        $query = "SELECT * FROM categories WHERE categories.name = '" . $category . "'";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function getQuestions($category) {
        $query = "SELECT questions.id, questions.question, categories.name FROM questions JOIN question_categories ON question_categories.question = questions.id JOIN categories ON categories.id = question_categories.category JOIN question_answers ON question_answers.question = questions.id WHERE categories.name = '" . $category . "' GROUP BY questions.id HAVING COUNT(question_answers.id) >= 3";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                $questions = array();

                while ($question = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    array_push($questions, $question);
                }

                return $questions;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function getAnswers($question_id) {
        $query = "SELECT answers.id, answers.answer from question_answers JOIN answers ON answers.id = question_answers.answer JOIN questions ON questions.id = question_answers.question WHERE questions.id = '" . $question_id . "'";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                $questions = array();

                while ($question = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    array_push($questions, $question);
                }

                return $questions;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function validateAnswer($answer) {
        $query = "SELECT * FROM answers WHERE answers.answer = '" . $answer . "'";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function startTest($user_id, $category) {

// Checking if user hasn't already started test in that category
        $check = "SELECT * FROM user_tests JOIN categories ON categories.id = user_tests.category WHERE user = '" . $user_id . "' AND categories.name = '" . $category . "'";
        $check_result = mysqli_query($this->db, $check);

// Checking if querry was successful
        if ($check_result) {

// If we got 0 rows, it means user hasn't started test for that category
            if (mysqli_num_rows($check_result) == 0) {

// Inserting new test for user
                $query = "INSERT INTO user_tests (user, category) VALUES ('" . $user_id . "', (SELECT categories.id FROM categories WHERE categories.name = '" . $category . "' LIMIT 1))";
                $res = mysqli_query($this->db, $query);

// Checking if querry was successful
                if ($res) {
                    return mysqli_insert_id($this->db);
                } else {
                    $this->showError();
                }
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function getUnfinishedTests($user_id) {
        $query = "SELECT user_tests.id, categories.name FROM user_tests JOIN categories ON categories.id = user_tests.category WHERE user_tests.user = '" . $user_id . "' AND user_tests.finished = 0 LIMIT 1";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                return mysqli_fetch_array($res, MYSQLI_ASSOC);
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function finishTest($test_id, $user_id, $category) {
        $finish = "UPDATE user_tests SET finished = 1 WHERE id = '" . $test_id . "' AND user = '" . $user_id . "' AND category = (SELECT id FROM categories WHERE categories.name = '" . $category . "')";
        $finish_result = mysqli_query($this->db, $finish);

        if ($finish_result) {
            return TRUE;
        } else {
            $this->showError();
        }
    }

    function getUnansweredQuestion($user_id, $category) {
        $query = "SELECT questions.id, questions.question, categories.name FROM questions JOIN question_categories ON question_categories.question = questions.id JOIN categories ON categories.id = question_categories.category JOIN question_answers ON question_answers.question = questions.id WHERE categories.name = '" . $category . "' AND questions.id NOT IN ( SELECT user_test_answers.question FROM user_tests JOIN user_test_answers ON user_test_answers.test = user_tests.id WHERE user = '" . $user_id . "') GROUP BY questions.id HAVING COUNT(question_answers.id) >= 3 LIMIT 1";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                return mysqli_fetch_array($res, MYSQLI_ASSOC);
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function answerTestQuestion($test_id, $question_id, $answer) {
        $query = "INSERT INTO user_test_answers (test, question, answer) VALUES ('" . $test_id . "', '" . $question_id . "', (SELECT answers.id FROM answers WHERE answers.answer = '" . $answer . "' LIMIT 1))";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            return TRUE;
        } else {
            $this->showError();
        }
    }

    function getUserTests($user_id) {
        $query = "SELECT categories.name as 'category', user_tests.date FROM user_tests JOIN categories ON categories.id = user_tests.category JOIN user_test_answers ON user_test_answers.test = user_tests.id WHERE user_tests.user = '" . $user_id . "' GROUP BY user_tests.id HAVING COUNT(user_test_answers.id) > 0";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                $user_tests = array();

                while ($test = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    array_push($user_tests, $test);
                }
                return $user_tests;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

    function getUserAnswers($user_id, $category) {
        $query = "SELECT questions.question, user_answer.answer AS 'user_answer', correct_answer.answer AS 'correct_answer', IF(user_test_answers.answer = questions.answer, TRUE, FALSE) AS 'correct', user_test_answers.answer_date FROM user_test_answers JOIN user_tests ON user_tests.id = user_test_answers.test JOIN questions ON questions.id = user_test_answers.question JOIN answers AS user_answer ON user_answer.id = user_test_answers.answer JOIN answers AS correct_answer ON correct_answer.id =  questions.answer WHERE user_tests.user = '" . $user_id . "' AND user_tests.category = (SELECT id FROM categories WHERE name = '" . $category . "' LIMIT 1) GROUP BY user_test_answers.id";
        $res = mysqli_query($this->db, $query);

        if ($res) {
            if (mysqli_num_rows($res) > 0) {
                $user_answers = array();

                while ($answer = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
                    array_push($user_answers, $answer);
                }
                return $user_answers;
            } else {
                return FALSE;
            }
        } else {
            $this->showError();
        }
    }

// ADMIN

    function addQuestion($category, $question, $answer_1, $answer_2, $answer_3) {
        try {
            $this->db->begin_transaction();

            $query = "INSERT INTO answers (answer) VALUES ('" . $answer_1 . "'); "
                    . "SET @correct_answer_id = LAST_INSERT_ID(); "
                    . "INSERT INTO questions (question, answer) VALUES ('" . $question . "', @correct_answer_id); "
                    . "SET @question_id = LAST_INSERT_ID(); "
                    . "INSERT INTO question_answers (question, answer) VALUES (@question_id, @correct_answer_id); "
                    . "INSERT INTO answers (answer) VALUES ('" . $answer_2 . "'); "
                    . "INSERT INTO question_answers (question, answer) VALUES (@question_id, LAST_INSERT_ID()); "
                    . "INSERT INTO answers (answer) VALUES ('" . $answer_3 . "'); "
                    . "INSERT INTO question_answers (question, answer) VALUES (@question_id, LAST_INSERT_ID()); "
                    . "INSERT INTO question_categories (question, category) VALUES (@question_id, (SELECT id FROM categories WHERE name = '" . $category . "' LIMIT 1));"
                    . "COMMIT;";

            mysqli_multi_query($this->db, $query);
            mysqli_commit($this->db);

            return TRUE;
        } catch (Exception $ex) {
            mysqli_rollback($this->db);
            printf($ex);
            exit();
        }
    }

}
