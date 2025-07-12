<?php
    session_set_cookie_params([
        'lifetime' => 0, // La session est détruite lorsque le navigateur est fermé
        'path' => '/',
        'domain' => '', // Par défaut, utilise le domaine actuel
        'secure' => isset($_SERVER['HTTPS']), // Utilise HTTPS si disponible
        'httponly' => true, // Empêche l'accès par JavaScript
        'samesite' => 'Lax' // Protection contre les attaques CSRF
    ]);
    
    require '../assets/php/connexion.php';
    include('../assets/php/fonctions.php');

    if (!isset($_SESSION["email"])) {
        // Détruire la session si l'utilisateur n'est pas connecté
        session_unset();
        session_destroy();
        header('Location: ../'); exit();
    }

    $smc = $bdd->prepare("SELECT id, pseudo, email, `type`, discute FROM users WHERE id = ?");
    $smc->execute([$_SESSION["id"]]);

    if ($rsmc = $smc->fetch()) {
        // Gestion de la déconnexion
        if (isset($_POST["logout"])) {
            $up_stat = $bdd->prepare("UPDATE users SET `online` = ?, discute = ? WHERE id = ?");
            $up_stat->execute(["non", null, $rsmc["id"]]);

            $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
            $add_history->execute([$rsmc["id"], "Déconnexion"]);

            session_unset();
            session_destroy();
            header('Location: ../'); exit();
        }

        // Définir les rôles
        $users = "users";
        $admin = "admin";

        if ($rsmc["type"] === $users) {
            include('index_users.php');
        } elseif ($rsmc["type"] === $admin) {
            if (isset($_POST["user"])) {
                $update_admin_mail = $bdd->prepare("UPDATE users SET email = ? WHERE id = ?");
                $update_admin_mail->execute(["user", $_SESSION["id"]]);

                header("Location: ./"); exit();
            }else if (isset($_POST["clock"])) {
                $update_admin_mail = $bdd->prepare("UPDATE users SET email = ? WHERE id = ?");
                $update_admin_mail->execute(["clock", $_SESSION["id"]]);
                header("Location: ./"); exit();
            }else if (isset($_POST["user_slash"])) {
                $update_admin_mail = $bdd->prepare("UPDATE users SET email = ? WHERE id = ?");
                $update_admin_mail->execute(["user_slash", $_SESSION["id"]]);

                header("Location: ./"); exit();
            }

            if($rsmc["email"] === "user") {
                include('index_admin.php');
            }else if($rsmc["email"] === "clock") {
                include('history.php');
            }else if($rsmc["email"] === "user_slash") {
                include('compte_delete.php');
            }
        } else {
            // Déconnecter les utilisateurs avec un rôle invalide
            session_unset();
            session_destroy();
            header('Location: ../'); exit();
        }
    } else {
        // Déconnecter si aucun utilisateur n'est trouvé
        session_unset();
        session_destroy();
        header('Location: ../'); exit();
    }
?>