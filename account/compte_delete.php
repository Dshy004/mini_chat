<?php
    // Réactivation d'un utilisateur
    if (isset($_POST["activer"])) {
        $encrypted = $_POST["id"];
        $decrypted = decrypt($encrypted, $key);
        $id_users = $decrypted;

        $select_exist_users = $bdd->prepare("SELECT * FROM users WHERE id = ?");
        $select_exist_users->execute([$id_users]);

        if ($rsese = $select_exist_users->fetch()) {
            $password = password_hash($ms_code, PASSWORD_BCRYPT);

            $update_users = $bdd->prepare("UPDATE users SET `password` = ?, `type` = ? WHERE id = ?");
            $update_users->execute([$password, "users", $rsese["id"]]);

            $to = $rsese["email"];
            $subject = "Eslash";
            $message = '<html>
                            <head>
                                <title>Code de validation duc ompte</title>
                            </head>
                            <body>
                                <div class="container" style="width: 100%; max-width: 700px; margin: 0 auto; padding: 20px;">
                                    <h3>Votre compte a été réactivé avec un nouveau mot de passe: <b>'. $ms_code .'</b></h3>      
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
                $add_history->execute([$rsmc["id"], "a réactivé @" . $rsese["pseudo"]]);

                echo '<script>alert("Utilisateur réactivé avec succès !");</script>';
            }else {
                echo '<script>alert("Envoie du mail de réactivation échouée !");</script>';
            }
        } else {
            echo '<script>alert("Réactivation échouée !");</script>';
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
        nav .menu form:nth-child(3) button {
            background-color: var(--white);
            color: var(--blue);
            border-radius: 10px;
        }
        section .table_users .users {
            height: 70vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include('./nav.php'); ?>
        <section>
            <h2 style="color: var(--red);">
                Compte supprimé:
<?php
    $count_compte_delete = $bdd->prepare("SELECT COUNT(*) as n_compte_delete FROM users WHERE `type` = ?");
    $count_compte_delete->execute(["inactif"]);
    if($result_ccd = $count_compte_delete->fetch()) {
?>
                <span style="color: var(--red);"><?= htmlspecialchars($result_ccd["n_compte_delete"]); ?></span>
<?php
    }
?>
            </h2>
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
                            <th colspan="2">Email</th>
                        </tr>
<?php 
    $i = 0;
    $search = isset($_POST["search"]) ? trim(htmlspecialchars($_POST["search"])) : '';
    $select_users_delete = $bdd->prepare("SELECT * FROM users WHERE pseudo LIKE ? AND `type` = ? ORDER BY pseudo");
    $select_users_delete->execute(["%".$search."%", "inactif"]);

    while($result_sud = $select_users_delete->fetch()) {
?>
                        <tr>
                            <td><b><?= $i + 1; ?></b></td>
                            <td><?= htmlspecialchars($result_sud["pseudo"]) ?></td>
                            <td><a target="_blank" href="mailto:<?= htmlspecialchars($result_sud["email"]) ?>"><?= htmlspecialchars($result_sud["email"]) ?></a></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment réactiver cet utilisateur ?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($result_sud["id"], $key)); ?>">
                                    <input type="submit" value="Reactiver" name="activer" style="background-color: var(--green); color: var(--white); font-weight: 600; padding: 10px; border-radius: 5px; cursor: pointer;">
                                </form>
                            </td>
                        </tr>
<?php
        $i++;
    }
    if($i == 0) {
?>
                        <tr><td colspan="4">Aucun résultats</td></tr>
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