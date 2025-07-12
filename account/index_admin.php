<?php 
    // Ajout d'un utilisateur
    if (isset($_POST["ajouter"])) {
        if (!empty($_POST["email"])) {
            $email = htmlspecialchars($_POST["email"]);

            $select_eu = $bdd->prepare("SELECT * FROM users WHERE email = ?");
            $select_eu->execute([$email]);

            if (!$select_eu->fetch()) {
                $pseudo = substr($email, 0, 6);
                $password = password_hash($ms_code, PASSWORD_BCRYPT);

                $to = $email;
                $subject = "Eslash";
                $message = '<html>
                                <head>
                                    <title>Code de validation duc ompte</title>
                                </head>
                                <body>
                                    <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                        <h3>Compte créée avec succès, Voici les indentifiants de votre compte:</h3>
                                        <ul>
                                            <li>Pseudo: ' . $pseudo . '</li>
                                            <li>Mot de passe: ' . $ms_code . '</li>
                                        </ul>
                                        <a href="https://eslash.alwaysdata.net">Connectez-vous</a> pour accéder à votre compte
                                    </div>
                                </body>
                            </html>';

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: slash@vomoto.com" . "\r\n";
                $headers .= "CC: ".$to;

                $send = mail($to,$subject,$message,$headers);

                if($send) {
                    $insert_users = $bdd->prepare("INSERT INTO users(pseudo, email, `password`) VALUES(?,?,?)");
                    $insert_users->execute([$pseudo, $email, $password]);
    
                    $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
                    $add_history->execute([$rsmc["id"], "a ajouté un nouvel utilisateur"]);
    
                    echo '<script>alert("Utilisateur ajouté avec succès !");</script>';
                }else {
                    echo '<script>alert("Inscription échouée !");</script>';
                }
            } else {
                echo '<script>alert("Cet email existe déjà !");</script>';
            }
        } else {
            header("Location: ./");
            exit();
        }
    }

    // Modification d'un utilisateur {
    if(isset($_POST["app_modify"])) {
        if(!empty($_POST["email"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_users = $decrypted;
            
            $email = htmlspecialchars($_POST["email"]);

            $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
            $select_exist_users->execute([$id_users]);

            if($rsete=$select_exist_users->fetch()) {
                $pseudo = substr($email, 0, 6);

                $update_user = $bdd->prepare("UPDATE users SET pseudo = ?, email = ? WHERE id = ?");
                $update_user->execute([$pseudo, $email, $id_users]);

                $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
                $add_history->execute([$rsmc["id"], "a modifié un utilisateur"]);

                ?><script>alert('Utilisateur modifié avec succès');</script><?php
            }else { ?><script>alert('Cet utilisateur n\'existe pas');</script><?php }
        } else {
            header("Location: ./");
            exit();
        }
    }

    // Suppression d'un utilisateur
    if (isset($_POST["delete"])) {
        $encrypted = $_POST["id"];
        $decrypted = decrypt($encrypted, $key);
        $id_users = $decrypted;

        $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
        $select_exist_users->execute([$id_users]);

        if ($rsese = $select_exist_users->fetch()) {
            $update_users = $bdd->prepare("UPDATE users SET `type` = ? WHERE id = ?");
            $update_users->execute(["inactif", $id_users]);

            $add_history = $bdd->prepare("INSERT INTO historique(id_users, `action`) VALUES(?,?)");
            $add_history->execute([$rsmc["id"], "a supprimé @" . $rsese["pseudo"]]);

            echo '<script>alert("Utilisateur supprimé avec succès !");</script>';
        } else {
            echo '<script>alert("Suppression échouée !");</script>';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eslash - Admin</title>
    <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <style>
        nav .menu form:nth-child(1) button {
            background-color: var(--white);
            color: var(--blue);
            border-radius: 10px;
        }
        section .table_users .users {
            height: 55vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include('./nav.php'); ?>
        <section>
            <h2 style="color: var(--blue);">
                Utilisateurs:
<?php
    $count_users = $bdd->prepare("SELECT COUNT(*) as n_users FROM users WHERE `type` = ?");
    $count_users->execute(["users"]);
    if($result_cu = $count_users->fetch()) {
?>
                <span style="color: var(--red);"><?= htmlspecialchars($result_cu["n_users"]); ?></span>
<?php
    }
?>
            </h2>
            <form method="post" class="form_add">
<?php 
    if(isset($_POST["modify"])) {
        $encrypted = $_POST["id"];
        $decrypted = decrypt($encrypted, $key);
        $id_users = $decrypted;
        
        $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
        $select_exist_users->execute([$id_users]);
        if($rsete=$select_exist_users->fetch()) {
?>
                <h3>Modifier <b style="color: var(--blue);">@<?= htmlspecialchars($rsete["pseudo"]); ?></b></h3>
                <div class="input">
                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rsete["id"], $key)); ?>" required>
                    <input type="email" name="email" value="<?= htmlspecialchars($rsete["email"]); ?>" required />
                    <input type="submit" value="Modifier" name="app_modify">
                </div>
<?php
        }else { echo ""; }

    }else {
?>
                <h3>Ajouter un utilisateur</h3>
                <div class="input">
                    <input type="email" name="email" placeholder="Email" required />
                    <input type="submit" value="Ajouter" name="ajouter">
                </div>
<?php
    }
?>
            </form>
            <div class="table_users">
                <form method="post" class="form_search">
                    <h3>Rechercher</h3>
                    <div class="input">
                        <input type="text" name="search" placeholder="Rechercher..." required />
                        <input type="submit" value="Rechercher">
                    </div>
                </form>
                <div class="users">
                    <table>
                        <tr>
                            <th>#</th>
                            <th>Pseudo</th>
                            <th>Email</th>
                            <th>Date de création</th>
                            <th colspan="2">Actions</th>
                        </tr>
<?php 
    $i = 0;
    $search = isset($_POST["search"]) ? trim(htmlspecialchars($_POST["search"])) : '';
    $select_users = $bdd->prepare("SELECT id, pseudo, email, date_format(dateCreate, 'Le %d/%m/%Y à %Hh:%im:%ss') as dateC FROM users WHERE pseudo LIKE ? AND `type` = ? ORDER BY pseudo");
    $select_users->execute(["%".$search."%", "users"]);

    while($result_su = $select_users->fetch()) {
?>
                        <tr>
                            <td><b><?= $i + 1; ?></b></td>
                            <td><?= htmlspecialchars($result_su["pseudo"]) ?></td>
                            <td><a target="_blank" href="mailto:<?= htmlspecialchars($result_su["email"]) ?>"><?= htmlspecialchars($result_su["email"]) ?></a></td>
                            <td><?= htmlspecialchars($result_su["dateC"]) ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($result_su["id"], $key)); ?>">
                                    <input type="submit" value="Modifier" name="modify" style="background-color: var(--orange); color: var(--white); font-weight: 600; padding: 10px; border-radius: 5px; cursor: pointer;">
                                </form>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($result_su["id"], $key)); ?>">
                                    <input type="submit" value="Supprimer" name="delete" style="background-color: var(--red); color: var(--white); font-weight: 600; padding: 10px; border-radius: 5px; cursor: pointer;">
                                </form>
                            </td>
                        </tr>
<?php
        $i++;
    }
    if($i == 0) {
?>
                        <tr><td colspan="6">Aucun résultats</td></tr>
<?php
    }
?>
                    </table>
                </div>
            </div>
        </section>
    </div>
    <script>
        function confirmer(text) {
            return confirm(text);
        }
    </script>
</body>
</html>