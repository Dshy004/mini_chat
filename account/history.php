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
        nav .menu form:nth-child(2) button {
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
            <h2 style="color: var(--blue);">
                Historique:
<?php
    $count_history = $bdd->prepare("SELECT COUNT(*) as n_history FROM historique");
    $count_history->execute();
    if($result_ch = $count_history->fetch()) {
?>
                <span style="color: var(--red);"><?= htmlspecialchars($result_ch["n_history"]); ?></span>
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
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
<?php 
    $i = 0;
    $search = isset($_POST["search"]) ? trim(htmlspecialchars($_POST["search"])) : '';
    $select_history = $bdd->prepare("SELECT historique.id, `action`, users.pseudo, users.type, date_format(dateAction, 'Le %d/%m/%Y à %Hh:%im:%ss') as dateA FROM
        historique INNER JOIN users ON users.id = historique.id_users WHERE users.pseudo LIKE ? ORDER BY historique.id DESC");
    $select_history->execute(["%".$search."%"]);

    while($result_sh = $select_history->fetch()) {
?>
                        <tr>
                            <td><b><?= $i + 1; ?></b></td>
                            <td><?= htmlspecialchars($result_sh["pseudo"]) ?></td>
                            <td><?= htmlspecialchars($result_sh["type"]) ?></td>
                            <td><?= htmlspecialchars($result_sh["action"]) ?> <?= htmlspecialchars($result_sh["dateA"]) ?></td>
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