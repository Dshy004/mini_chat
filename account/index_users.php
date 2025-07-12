<?php
    // Demander amitié
    if(isset($_POST["dev_amis"])) {
        if(!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;

            $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
            $select_exist_users->execute([$id_users]);

            if ($rsese = $select_exist_users->fetch()) {
                $select_mail = $bdd->prepare("SELECT email FROM users WHERE id = ?");
                $select_mail->execute([$id_users]);

                if($rsm = $select_mail->fetch()) {
                    $to = $rsm["email"];
                    $subject = "Eslash";
                    $message = '<html>
                                    <head>
                                        <title>Code de validation duc ompte</title>
                                    </head>
                                    <body>
                                        <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                            <h2><b>@' . $rsmc["pseudo"] . '</b> vous a envoyé une invitation.</h2>
                                            <a href="https://eslash.alwaysdata.net">Connectez-vous</a> pour valider ou non la demande.
                                        </div>
                                    </body>
                                </html>';
    
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: slash@vomoto.com" . "\r\n";
                    $headers .= "CC: ".$to;
    
                    $send = mail($to,$subject,$message,$headers);
    
                    if ($send) {
                        $add_history = $bdd->prepare("INSERT INTO amis(idu_one, idu_two) VALUES(?,?)");
                        $add_history->execute([$rsmc["id"], $rsese["id"]]);
        
                        header('Location: ./');
                        exit();
                    } else {
                        $error[] = "Demande d'amis échouée !";
                    }
                } else {
                    $error[] = "Demande d'amis échouée !";
                }
            } else {
                $error[] = "Demande d'amis échouée !";
            }
        }
    }
    // Accepter amitié
    if(isset($_POST["acc_reçu"])) {
        if(!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;

            $select_exist_users = $bdd->prepare("SELECT * FROM amis WHERE idu_one = ? AND idu_two = ? AND valide = ?");
            $select_exist_users->execute([$id_users, $rsmc["id"], "non"]);

            if ($rsese = $select_exist_users->fetch()) {
                $select_mail = $bdd->prepare("SELECT email FROM users WHERE id = ?");
                $select_mail->execute([$id_users]);

                if($rsm = $select_mail->fetch()) {
                    $to = $rsm["email"];
                    $subject = "Eslash";
                    $message = '<html>
                                    <head>
                                        <title>Code de validation duc ompte</title>
                                    </head>
                                    <body>
                                        <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                            <h2><b>@' . $rsmc["pseudo"] . '</b> a accepté votre invitation</h2>
                                        </div>
                                    </body>
                                </html>';
    
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= "From: slash@vomoto.com" . "\r\n";
                    $headers .= "CC: ".$to;
    
                    $send = mail($to,$subject,$message,$headers);
    
                    if ($send) {
                        $add_history = $bdd->prepare("UPDATE amis SET valide = ? WHERE id = ?");
                        $add_history->execute(["oui", $rsese["id"]]);
        
                        header('Location: ./');
                        exit();
                    } else {
                        $error[] = "Echouée !";
                    }
                } else {
                    $error[] = "Echouée !";
                }
            } else {
                $error[] = "Echouée !";
            }
        }
    }
    // Annuler la demande d'amitié
    if(isset($_POST["ann_reçu"])) {
        if(!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;
    
            $select_exist_users = $bdd->prepare("SELECT * FROM amis WHERE idu_one = ? AND idu_two = ?");
            $select_exist_users->execute([$id_users, $rsmc["id"]]);
    
            if ($rsese = $select_exist_users->fetch()) {
                $add_history = $bdd->prepare("DELETE FROM amis WHERE idu_one = ? AND idu_two = ?");
                $add_history->execute([$rsese["idu_one"], $rsmc["id"]]);
    
                header('Location: ./');
                exit();
            } else {
                $error[] = "Echouée !";
            }
        }
    }
    // Annuler ma demande d'amitié
    if(isset($_POST["ann_amis"])) {
        if(!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;
    
            $select_exist_users = $bdd->prepare("SELECT * FROM amis WHERE idu_one = ? AND idu_two = ?");
            $select_exist_users->execute([$rsmc["id"], $id_users]);
    
            if ($rsese = $select_exist_users->fetch()) {
                $add_history = $bdd->prepare("DELETE FROM amis WHERE idu_one = ? AND idu_two = ?");
                $add_history->execute([$rsmc["id"], $rsese["idu_two"]]);
    
                header('Location: ./');
                exit();
            } else {
                $error[] = "Echouée !";
            }
        }
    }
    // Retirer amitié
    if(isset($_POST["ret_amis"])) {
        if (!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;

            $select_exist_users = $bdd->prepare("SELECT * FROM amis WHERE (idu_one = ? OR idu_one = ?) AND (idu_two = ? OR idu_two = ?)");
            $select_exist_users->execute([$rsmc["id"], $id_users, $id_users, $rsmc["id"]]);

            if ($rsese = $select_exist_users->fetch()) {
                $add_history = $bdd->prepare("DELETE FROM amis WHERE (idu_one = ? OR idu_one = ?) AND (idu_two = ? OR idu_two = ?)");
                $add_history->execute([$rsmc["id"], $rsese["idu_one"], $rsese["idu_two"], $rsmc["id"]]);

                $up_history = $bdd->prepare("UPDATE users SET discute = ? WHERE id = ?");
                $up_history->execute([null, $rsmc["id"]]);

                header('Location: ./');
                exit();
            } else {
                $error[] = "Echouée !";
            }
        }
    }
    // Lancer la discussion
    if(isset($_POST["dis_amis"])) {
        if (!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;

            $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
            $select_exist_users->execute([$id_users]);

            if ($rsese = $select_exist_users->fetch()) {
                $add_history = $bdd->prepare("UPDATE users SET discute = ? WHERE id = ?");
                $add_history->execute([$rsese["pseudo"], $rsmc["id"]]);
                
                header('Location: ./');
                exit();
            } else {
                $error[] = "Echouée !";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eslash - <?= htmlspecialchars($rsmc["pseudo"]) ?></title>
    <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/account.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
<?php
    if(!empty($error)) {
?>
    <style>
        .notif {
            position: fixed;
            top: 25px;
            left: 50%;
            background-color: var(--red);
            color: var(--white);
            font-size: .7rem;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 4px 0 var(--black);
            padding: 10px;
            animation: slideNotif 0.2s ease-out;
        }
        @keyframes slideNotif {
            0% { opacity: 0; transform: translateY(-200px); display: none; }
            100% { opacity: 1; transform: translateY(0); display: block; }
        }
        .notif.returnNotif {
            display: none;
            animation: returnN 0.5s ease-out;
        }
        @keyframes returnN {
            0% { opacity: 1; transform: translateY(0); display: block; }
            100% { opacity: 0; transform: translateY(-200px); display: none; }
        }
    </style>
<?php
    }else if(!empty($success)) {
?>
    <style>
        .notif {
            position: fixed;
            top: 25px;
            left: 50%;
            background-color: var(--green);
            color: var(--white);
            font-size: .7rem;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 4px 0 var(--black);
            padding: 10px;
            animation: slideNotif 0.2s ease-out;
        }
        @keyframes slideNotif {
            0% { opacity: 0; transform: translateY(-200px); display: none; }
            100% { opacity: 1; transform: translateY(0); display: block; }
        }
        .notif.returnNotif {
            display: none;
            animation: returnN 0.5s ease-out;
        }
        @keyframes returnN {
            0% { opacity: 1; transform: translateY(0); display: block; }
            100% { opacity: 0; transform: translateY(-200px); display: none; }
        }
    </style>
<?php
    }
?>
</head>
<body>
    <div id="app" class="container">
<?php
    if(!empty($error)) {
        foreach ($error as $err) {
?>
        <div class="notif"><?= htmlspecialchars($err) ?></div>
        <script>
            setTimeout(() => {
                document.querySelector('.notif').classList.add('returnNotif');
            }, 3000);
        </script>
<?php
        }
    }else if(!empty($success)) {
        foreach ($success as $suc) {
?>
        <div class="notif"><?= htmlspecialchars($suc) ?></div>
        <script>
            setTimeout(() => {
                document.querySelector('.notif').classList.add('returnNotif');
            }, 3000);
        </script>
<?php
        }
    }
?>
        <div class="head">
            <a href="./" class="profil">
                <img src="../assets/img/logo.png" alt="Eslash Logo">
                <h2>@<?= htmlspecialchars($rsmc["pseudo"]) ?></h2>
            </a>
            <div style="display: flex; flex-direction: row; gap: 10px;">
                <button v-show="btnU" @click="showListU" style="background-color: var(--blue); padding: 10px; border-radius: 5px; color: var(--white);"><i class="fas fa-users"></i></button>
                <form method="post" class="form_logout"><button type="submit" name="logout"><i class="fas fa-sign-out-alt"></i></button></form>
            </div>
        </div>
        <div class="users_discussion">
            <div class="users" v-show="divU">
                <h3>Utilisateurs</h3>
                <form method="post" class="form_search">
                    <input type="text" name="search" placeholder="Rechercher..." required />
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <div class="list" id="list">
<?php include('./list.php'); ?>
                </div>
            </div>
            <div class="discussion">
<?php 
    if($rsmc["discute"] !== null) {
        $with = $rsmc["discute"];

        $select_exist_with = $bdd->prepare("SELECT * FROM users WHERE pseudo = ? AND `type` != ?");
        $select_exist_with->execute([$with, "inactif"]);

        if ($rsew = $select_exist_with->fetch()) {
            $update_chat = $bdd->prepare("UPDATE chat SET vue = ? WHERE idu = ? AND vue = ?");
            $update_chat->execute(["oui", $rsew["id"], "non"]);
?>
                <h3>
                    Vous discutez avec <?= htmlspecialchars($rsew["pseudo"]) ?>

<?php 
            if($rsew["online"] == "non") {
?>
                    <span style="color: var(--red);">(hors ligne)</span>
<?php
            }else {
?>
                    <span style="color: var(--green);">(en ligne)</span>
<?php 
            }
?>
                </h3>
                <div class="contain" id="messages_container">
<?php include('./chat_users.php'); ?>
                </div>
<?php
            //Modifier le message 
            if(isset($_POST["modify_sms"])) {
                if (!empty($_POST["id"])) {
                    $encrypted = $_POST["id"];
                    $decrypted = decrypt($encrypted, $key);
                    $id_chat = $decrypted;

                    $select_exist_message = $bdd->prepare("SELECT * FROM chat WHERE id = ?");
                    $select_exist_message->execute([$id_chat]);

                    if ($rsem = $select_exist_message->fetch()) {
?>
                <form method="post" class="form_chat">
                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rsem["id"], $key)); ?>">
                    <textarea name="new_text" placeholder="Tapez votre message..." required><?= htmlspecialchars_decode(trim($rsem["message"])); ?></textarea>
                    <button type="submit" name="app_modify_sms"><i class="fas fa-check"></i> <span>Enregistrer</span> <img src="../assets/img/logo.png" alt=""></button>
                </form>
<?php
                    } else {
                        $error[] = "Modification échouée !";
                    }
                }
            }else {
?>
                <div v-show="divDocs" style="margin: 0 0 10px 0;">
                    <span :style="{ color: isInvalidFile ? 'var(--red)' : 'var(--black)', fontSize: '.8rem' }">{{ docsName }}</span>
                    <button @click="HideDocs" style="color: var(--red); margin: 0 0 0 10px; background-color: transparent; font-size: 1rem;"><i class="fas fa-xmark"></i></button>
                </div>
                <form method="post" class="form_chat" enctype="multipart/form-data">
                    <div class="others_btn">
                        <div class="btn">
                            <label for="docs"><i class="fas fa-file"></i></label>
                            <input type="file" name="docs" id="docs" accept=".png, .jpg, .jpeg, .webp, .webm, .zip, .xlsx, .xls, .docx, .doc, .pptx, .ppt, .pdf, .txt, .csv, .mp3, .mp4" @change="onFileChange">
                        </div>
                    </div>
                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rsew["id"], $key)); ?>">
                    <textarea name="message" placeholder="Tapez votre message..."></textarea>
                    <button type="submit" name="send_sms"><i class="fas fa-paper-plane"></i> <span>Envoyer</span> <img src="../assets/img/logo.png" alt=""></button>
                </form>
<?php
            }
        }else {
?>
                <div class="home">
                    <img src="../assets/img/logo.png" alt="Eslash Logo">
                </div>
<?php
        }
    }else {
?>
                <div class="home">
                    <img src="../assets/img/logo.png" alt="Eslash Logo">
                </div>
<?php
    }
?>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script>
        function confirmer(text) {
            return confirm(text);
        }
    </script>
    <script>
        let inactivityTime = 60 * 60 * 1000;
        let timer;

        function resetTimer() {
            clearTimeout(timer);
            timer = setTimeout(logout, inactivityTime);
        }

        function logout() {
            fetch("./lg.php")
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        window.location.href = "../";
                    }
                });
        }

        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
        document.onscroll = resetTimer;
    </script>
    <?php include('./ajax.php'); ?>
    <?php include('./script.php'); ?>
</body>
</html>