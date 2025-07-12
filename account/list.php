<?php 
    // Inclure les fonctions nécessaires une seule fois
    require_once('../assets/php/connexion.php'); // Contient les fonctions encrypt() et decrypt()
    require_once('../assets/php/fonctions.php');

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $myid = null;
    $pseudo = "";
    $idd = 0;
    if (isset($_SESSION['id'])) {
        $smc = $bdd->prepare("SELECT id, pseudo, email, `type`, discute FROM users WHERE id = ?");
        $smc->execute([$_SESSION['id']]);
        if ($rsmc = $smc->fetch()) {
            $myid = $rsmc["id"];
            $pseudo = $rsmc["pseudo"];

            $sdc = $bdd->prepare("SELECT id FROM users WHERE pseudo = ?");
            $sdc->execute([$rsmc["discute"]]);
            if ($rsdc = $sdc->fetch()) {
                $idd = $rsdc["id"];
            }
        }
    }
    
    $i = 0;
    $search = isset($_POST["search"]) ? trim(htmlspecialchars($_POST["search"])) : '';
    $select_users = $bdd->prepare("SELECT id, pseudo FROM users WHERE id != ? AND pseudo LIKE ? AND type = ? ORDER BY pseudo");
    $select_users->execute([$myid, "%" . $search . "%", "users"]);

    $categories = [
        "Mes amis" => [],
        "Demande lancée" => [],
        "Demande reçue" => [],
        "Autres" => []
    ];

    while ($result_su = $select_users->fetch()) {
        $select_fy = $bdd->prepare("SELECT * FROM amis WHERE (idu_one = ? OR idu_one = ?) AND (idu_two = ? OR idu_two = ?) AND valide = ?");
        $select_fy->execute([$myid, $result_su["id"], $result_su["id"], $myid, "oui"]);
        // $select_sms_non_lu = $bdd->prepare("SELECT * FROM chat WHERE idu = ? AND id_receiver = ? AND vue = ?");
        // $select_sms_non_lu->execute([$result_su["id"], $myid, "non"]);

        $select_fn = $bdd->prepare("SELECT * FROM amis WHERE idu_one = ? AND idu_two = ? AND valide = ?");
        $select_fn->execute([$myid, $result_su["id"], "non"]);

        $select_fr = $bdd->prepare("SELECT * FROM amis WHERE idu_one = ? AND idu_two = ? AND valide = ?");
        $select_fr->execute([$result_su["id"], $myid, "non"]);

        if ($rsfy = $select_fy->fetch()) {
            // Mes amis
            $categories["Mes amis"][] = $result_su;
        } elseif ($rsfn = $select_fn->fetch()) {
            // Demande lancée
            $categories["Demande lancée"][] = $result_su;
        } elseif ($rsfr = $select_fr->fetch()) {
            // Demande reçue
            $categories["Demande reçue"][] = $result_su;
        } else {
            // Autres
            $categories["Autres"][] = $result_su;
        }
        $i++;
    }

    if ($i == 0) {
        echo '<div class="person">Aucun résultat</div>';
    } else {
        foreach ($categories as $title => $users) {
            if (!empty($users)) {
?>
                <h3 style="color: var(--white); margin: 0 0 10px 0;"><?= htmlspecialchars($title); ?> :</h3>
<?php
                foreach ($users as $user) {
?>
                    <div class="person">
                        <div class="name_person">@<?= htmlspecialchars($user["pseudo"]); ?></div>
<?php
                    switch ($title) {
                        case "Mes amis":
?>
                        <div style="display: flex; flex-direction: row; gap: 10px;">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                                <button type="submit" name="dis_amis" style="background-color: var(--blue);"><i class="fas fa-sms"></i></button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                                <button type="submit" name="ret_amis" style="background-color: var(--red);"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
<?php
                            break;
                        case "Demande lancée":
?>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                            <button type="submit" name="ann_amis" style="background-color: var(--orange);"><i class="fas fa-xmark"></i></button>
                        </form>
<?php
                            break;
                        case "Demande reçue":
?>
                        <div style="display: flex; flex-direction: row; gap: 10px;">
                            <form method="post">
                                <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                                <button type="submit" name="acc_reçu" style="background-color: var(--green);"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                                <button type="submit" name="ann_reçu" style="background-color: var(--orange);"><i class="fas fa-xmark"></i></button>
                            </form>
                        </div>
<?php
                            break;
                        case "Autres":
?>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($user["id"], $key)); ?>">
                            <button type="submit" name="dev_amis" style="background-color: var(--green);"><i class="fas fa-plus"></i></button>
                        </form>
<?php
                            break;
                    }
?>
                    </div>
<?php
                }
            }
        }
    }
?>
