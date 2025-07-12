<?php 
    require './assets/php/connexion.php';
    require './assets/php/fonctions.php';

    if(isset($_POST["valid_code"])) {
        if(!empty($_POST["pseudo"]) && !empty($_POST["code"])) {
            $pseudo = htmlspecialchars($_POST["pseudo"]);
            $code = htmlspecialchars($_POST["code"]);

            $select_v_compte = $bdd->prepare("SELECT * FROM users WHERE pseudo = ? AND code_valid = ?");
            $select_v_compte->execute([$pseudo, $code]);

            if ($rsvc = $select_v_compte->fetch()) {
                $hashed_pass = password_hash($ms_code, PASSWORD_BCRYPT);

                $up_mdp = $bdd->prepare("UPDATE users SET `password` = ? WHERE id = ?");
                $up_mdp->execute([$hashed_pass, $rsvc["id"]]);

                $to = $rsvc["email"];
                $subject = "Mini_chat";
                $message = '<html>
                                <head>
                                    <title>Code de validation duc ompte</title>
                                </head>
                                <body>
                                    <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                        <h3>Voici votre nouveau mot de passe: <b>'. $new_mdp .'</b></h3>
                                        <a href="lien_du_site">Connectez-vous</a> pour accéder à votre compte
                                    </div>
                                </body>
                            </html>';

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: slash@vomoto.com" . "\r\n";
                $headers .= "CC: ".$to;

                $send = mail($to,$subject,$message,$headers);

                if ($send) {
                    $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
                    $add_history->execute([$rsvc["id"], "Changement de mot de passe"]);

                    header('Location: ./index?sms='.htmlspecialchars('Nouveau mot de passe envoyé dans votre mail').'');
                    exit();
                }else {
                    $error_code[] = '<div class="input">
                        <label for="pseudo">Votre pseudo</label>
                        <input type="text" name="pseudo" id="pseudo" value="'. $pseudo. '" required />
                    </div>
                    <div class="input">
                        <label for="code">Code d\'obtention d\'un nouveau mot de passe</label>
                        <input type="text" name="code" id="code" required />
                    </div>
                    <button type="submit" name="valid_code">Valider</button>
                    <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Mot de passe non envoyé</div>';
                }
            }else {
                $error_code[] = '<div class="input">
                    <label for="pseudo">Votre pseudo</label>
                    <input type="text" name="pseudo" id="pseudo" value="'. $pseudo .'" required />
                </div>
                <div class="input">
                    <label for="code">Code d\'obtention d\'un nouveau mot de passe</label>
                    <input type="text" name="code" id="code" required />
                </div>
                <button type="submit" name="valid_code">Valider</button>
                <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Pseudo ou code incorrect</div>';
            }
        }else {
            $error_code[] = '<div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <div class="input">
                <label for="code">Code d\'obtention d\'un nouveau mot de passe</label>
                <input type="text" name="code" id="code" value="'. $pseudo .'" required />
            </div>
            <button type="submit" name="valid_code">Valider</button>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Veuillez remplir tous les champs</div>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini_chat - Mot de passe oublié</title>
    <link rel="shortcut icon" href="./assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/style.css">
<?php 
    if(isset($_POST["valid"]) || isset($error_code) || isset($_POST["valid_code"])) {
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
            <h3>Mot de passe oublié</h3>
<?php 
    if(isset($_POST["valid"])) {
        if(!empty($_POST["pseudo"])) {
            $pseudo = htmlspecialchars($_POST["pseudo"]);
            
            $select_account = $bdd->prepare("SELECT * FROM users WHERE pseudo = ?");
            $select_account->execute([$pseudo]);
            
            if ($result_sa = $select_account->fetch()) {
                if($result_sa["type"] == "inactif") {
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <button type="submit" name="valid">Valider</button>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Compte inactif</div>
<?php
                }else {
                    $newcv = rand(100000,1000000);

                    $upcv = $bdd->prepare("UPDATE users SET code_valid = ? WHERE id = ?");
                    $upcv->execute([$newcv, $result_sa["id"]]);

                    $to = $result_sa["email"];
                    $subject = "Mini_chat";
                    $message = '<html>
                                    <head>
                                        <title>Code de validation duc ompte</title>
                                    </head>
                                    <body>
                                        <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                            <h3>Voici votre code d\'obtention d\'un nouveau mot de passe: <b>'. $newcv .'</b></h3>      
                                        </div>
                                    </body>
                                </html>';

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: slash@vomoto.com" . "\r\n";
                    $headers .= "CC: ".$to;

                    $send = mail($to,$subject,$message,$headers);

                    if ($send) {
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" value="<?= $pseudo; ?>" required />
            </div>
            <div class="input">
                <label for="code">Code d'obtention d'un nouveau mot de passe</label>
                <input type="text" name="code" id="code" required />
            </div>
            <button type="submit" name="valid_code">Valider</button>
<?php
                    }else {
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" value="<?= $pseudo; ?>" required />
            </div>
            <div class="input">
                <label for="code">Code d'obtention d'un nouveau mot de passe</label>
                <input type="text" name="code" id="code" required />
            </div>
            <button type="submit" name="valid_code">Valider</button>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Code non envoyé</div>
<?php
                    }
                }
            }else {            
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <button type="submit" name="valid">Valider</button>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Ce pseudonyme n'existe pas</div>
<?php
            }
        }else {
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <button type="submit" name="valid">Valider</button>
            <div style="background-color: var(--red); color:var(--white); padding: 10px; border-radius: 5px; font-size: .8rem;">Veuillez entrer votre pseudo</div>
<?php
        }
    }else if(isset($error_code)) {
        foreach($error_code as $err) {
            echo $err;
        }
    }else {
?>
            <div class="input">
                <label for="pseudo">Votre pseudo</label>
                <input type="text" name="pseudo" id="pseudo" required />
            </div>
            <button type="submit" name="valid">Valider</button>
<?php
    }
?>
        </form>
    </div>
</body>
</html>