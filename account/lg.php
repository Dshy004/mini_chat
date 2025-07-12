<?php
    require '../assets/php/connexion.php';

    if (isset($_SESSION["id"])) {
        $up_stat = $bdd->prepare("UPDATE users SET `online` = ?, discute = ? WHERE id = ?");
        $up_stat->execute(["non", null, $_SESSION["id"]]);

        session_unset();
        session_destroy();

        echo json_encode(["status" => "success"]);
        exit();
    } else {
        echo json_encode(["status" => "no_session"]);
        exit();
    }
?>