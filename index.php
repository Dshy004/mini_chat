<?php 
    require './assets/php/connexion.php';

    if(isset($_POST["login"])) {
        if(!empty($_POST["pseudo"]) && !empty($_POST["password"])) {
            $pseudo = htmlspecialchars($_POST["pseudo"]);
            $pass = htmlspecialchars($_POST['password']);
            
            $select_account = $bdd->prepare("SELECT * FROM users WHERE pseudo = ?");
            $select_account->execute([$pseudo]);
            
            if($result_sa = $select_account->fetch()) {
                if(password_verify($pass, $result_sa['password'])) {
                    $_SESSION["id"] = $result_sa["id"];
                    $_SESSION["pseudo"] = $result_sa["pseudo"];
                    $_SESSION["email"] = $result_sa["email"];
                    $_SESSION["password"] = $result_sa["password"];
                    $_SESSION["type"] = $result_sa["type"];
                    $_SESSION["online"] = $result_sa["online"];
                    $_SESSION["discute"] = $result_sa["discute"];

                    if($result_sa["type"] == "inactif") {
                        $error[] = "Compte inactif !";
                    }else {
                        $up_online = $bdd->prepare("UPDATE users SET online = ? WHERE id = ?");
                        $up_online->execute(["oui", $result_sa["id"]]);
    
                        $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
                        $add_history->execute([$result_sa["id"], "Connexion"]);
    
                        header('Location: ./account/');
                        exit();
                    }
                }else {            
                    $error[] = "Pseudo ou mot de passe incorrect !";
                }
            }else {
                $error[] = "Pseudo ou mot de passe incorrect !";
            }
        }else {
            $error[] = "Veuillez remplir tous les champs !";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini_chat - by Webdshy</title>
    <link rel="shortcut icon" href="./assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/style.css">
<?php 
    if(isset($_POST["login"]) || isset($error) || isset($_GET["sms"])) {
?>
    <style>
        .load {
            display: none;
        }
    </style>
<?php
    }else {
?>
    <style>
        .load {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--white);
        }
        .load img {
            width: 150px;
            height: 150px;
            animation: slideImg 1s linear infinite;
        }
        @keyframes slideImg {
            0% { transform: translateY(10px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(10px); }
        }
    </style>
    <script>
        setTimeout(() => {
            document.querySelector('.load').style.display = "none";
        }, 1500);
    </script>
<?php
    }
?>
</head>
<body>
    <div class="load"><img src="./assets/img/load_logo.png" alt="Load Logo"></div>
    <div class="container">
        <a href="./" class="left"><img src="./assets/img/logo.png" alt="Mini_chat Logo"> <h1>Mini_chat</h1></a>
        <form method="POST" class="right">
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <div class="input">
                <label for="password">Votre mot de passe</label>
                <input type="password" name="password" id="password" required />
                <a href="./forgot_password">Mot de passe oublié ?</a>
            </div>
            <button type="submit" name="login">Se connecter</button>
<?php
    if(isset($error)) {
        foreach($error as $err) {
?>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;"><?= htmlspecialchars($err); ?></div>
<?php
        }
    }else if(isset($_GET["sms"])) {
?>
            <div style="background-color: var(--green); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;"><?= htmlspecialchars($_GET["sms"]); ?></div>
<?php
    }
?>
        </form>
    </div>
</body>
</html>