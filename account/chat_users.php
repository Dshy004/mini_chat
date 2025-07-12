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

    // Envoyer les messages
    if (isset($_POST["send_sms"])) {
        if (!empty($_POST["id"])) {
            $idu = $myid; // Utilisateur qui envoie le message
    
            // Déchiffrement de l'identifiant du destinataire
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_receiver = $decrypted;
    
            // Validation des champs
            $message = !empty($_POST["message"]) ? htmlspecialchars(trim($_POST["message"])) : null;
            $docs = !empty($_FILES['docs']) ? $_FILES['docs'] : null;
    
            if (empty($message) && empty($docs)) {
                $error[] = "Les champs sont vides !";
            } else {
                // Insertion du message texte dans la base de données
                $insert_sms = $bdd->prepare("INSERT INTO chat (idu, id_receiver, pseudo, `message`) VALUES (?, ?, ?, ?)");
                $insert_sms->execute([$idu, $id_receiver, $pseudo, $message]);
    
                if ($docs) {
                    $filename = $docs['name'];
                    $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowedExtensions = [ 'png', 'jpg', 'jpeg', 'webm', 'webp', 'mp4', 'mp3', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip' ];
    
                    if (in_array($fileExtension, $allowedExtensions)) {
                        $uniqueFilename = uniqid() . '_' . basename($filename);
                        $uploadPath = "../assets/docs/" .  $uniqueFilename;
    
                        // Tentative de téléchargement ddu fichier
                        if (move_uploaded_file($docs['tmp_name'], $uploadPath)) {
                            // Mise à jour ddu fichier dans la base de données
                            $update_docs = $bdd->prepare("UPDATE chat SET `docs` = ? WHERE idu = ? AND id_receiver = ? ORDER BY id DESC LIMIT 1");
                            $update_docs->execute([$uniqueFilename, $idu, $id_receiver]);
                        } else {
                            $error[] = "Erreur lors du téléchargement ddu fichier.";
                        }
                    } else {
                        $error[] = "Ce format de fichier n'est pas accepté";
                    }
                }
    
                // Si aucune erreur, redirection vers l'index
                if (empty($error)) {
                    header('Location: ./'); exit();
                }
            }
        } else {
            header('Location: ./'); exit();
        }
    }   
    // Appliquer la modification du message
    if(isset($_POST["app_modify_sms"])) {
        if (!empty($_POST["id"]) && !empty($_POST["new_text"])) {
            $new_message = htmlspecialchars(trim($_POST["new_text"]));

            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_chats = $decrypted;

            $select_exist_message = $bdd->prepare("SELECT * FROM chat WHERE id = ?");
            $select_exist_message->execute([$id_chats]);

            if ($rsem = $select_exist_message->fetch()) {
                $up_sms = $bdd->prepare("UPDATE chat SET `message` = ? WHERE id = ?");
                $up_sms->execute([$new_message, $id_chats]);

                header('Location: ./'); exit();
            } else {
                $error[] = "Modification échouée !";
            }
        }
    }
    // Supprmer le message
    if(isset($_POST["trash"])) {
        if (!empty($_POST["id"])) {
            $encrypted = $_POST["id"];
            $decrypted = decrypt($encrypted, $key);
            $id_chat = $decrypted;

            $select_exist_chat = $bdd->prepare("SELECT * FROM chat WHERE id = ?");
            $select_exist_chat->execute([$id_chat]);

            if ($rsec = $select_exist_chat->fetch()) {
                $delete_sms = $bdd->prepare("UPDATE chat SET `delete` = ? WHERE id = ?");
                $delete_sms->execute(["oui", $rsec["id"]]);

                header('Location: ./'); exit();
            } else {
                $error[] = "Suppression échouée !";
            }
        }
    }

    $select_sms = $bdd->prepare("SELECT id, idu, id_receiver, `message`, `docs`, vue, `delete`, DATE_FORMAT(dateSent, '%d/%m/%Y %H:%i') as dateS FROM chat ORDER BY id");
    $select_sms->execute();

    while ($rss = $select_sms->fetch()) {
        if ($rss['idu'] == $myid && $rss['id_receiver'] == $idd) {
            if($rss["docs"] == null && $rss["message"] !== null) {
                if($rss["delete"] == "oui") {
?>
                    <div class="me">
                        <div class="text_forms">
                            <div class="text">
                                <span><?= nl2br(makeLinksClickable(htmlspecialchars_decode(trim($rss["message"])))) ?></span>
                                <br><span style="color: var(--red); font-size: 0.6rem;">Ce message a été supprimé</span>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }else {
?>
                    <div class="me">
                        <div class="text_forms">
                            <div class="text"><?= nl2br(makeLinksClickable(htmlspecialchars_decode(trim($rss["message"])))) ?></div>
                            <div class="forms">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rss["id"], $key)); ?>">
                                    <button type="submit" name="modify_sms"><i class="fas fa-square-pen" style="color: var(--orange);"></i></button>
                                </form>
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer le message ?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rss["id"], $key)); ?>">
                                    <button type="submit" name="trash"><i class="fas fa-trash" style="color: var(--red);"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }
            }else if($rss["docs"] !== null && $rss["message"] == null) {
?>
                    <div class="me">
                        <div class="text_forms">
                            <div class="text">
<?php
                $fichierextension = strtolower(pathinfo($rss["docs"], PATHINFO_EXTENSION));

                $fichierimg = array('png','jpg','jpeg','webp');
                $fichiervideo_audio = array('mp4','mp3','webm');
                $fichierdoc = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip');

                if(in_array($fichierextension, $fichierimg)) {
?>
                                <img style="width: 100%; height: 350px; border-radius: 10px;" src="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>" alt="<?= htmlspecialchars($rss["docs"]); ?>">
<?php
                }else if(in_array($fichierextension, $fichiervideo_audio)) {
?>
                                <div style="padding: 15px; background-color: var(--black); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a style="color: var(--white);" target="_blank" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-play"></i></a></div>
<?php
                }else if(in_array($fichierextension, $fichierdoc)) {
?>
                                <div style="padding: 15px; background-color: var(--black); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a download style="color: var(--white);" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-download"></i></a></div>
<?php
                }

                if($rss["delete"] == "oui") {
?>
                                <br><span style="color: var(--red); font-size: 0.6rem;">Ce message a été supprimé</span>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }else {
?>
                            </div>
                            <div class="forms">
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer le message ?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rss["id"], $key)); ?>">
                                    <button type="submit" name="trash"><i class="fas fa-trash" style="color: var(--red);"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }
            }else if($rss["docs"] !== null && $rss["message"] !== null) {
?>
                    <div class="me">
                        <div class="text_forms">
                            <div class="text">
<?php
                $fichierextension = strtolower(pathinfo($rss["docs"], PATHINFO_EXTENSION));

                $fichierimg = array('png','jpg','jpeg','webp');
                $fichiervideo_audio = array('mp4','mp3','webm');
                $fichierdoc = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip');

                if(in_array($fichierextension, $fichierimg)) {
?>
                                <img style="width: 100%; height: 350px; border-radius: 10px;" src="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>" alt="<?= htmlspecialchars($rss["docs"]); ?>">
<?php
                }else if(in_array($fichierextension, $fichiervideo_audio)) {
?>
                                <div style="padding: 15px; margin: 0 0 10px 0; background-color: var(--black); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a style="color: var(--white);" target="_blank" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-play"></i></a></div>
<?php
                }else if(in_array($fichierextension, $fichierdoc)) {
?>
                                <div style="padding: 15px; margin: 0 0 10px 0; background-color: var(--black); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a download style="color: var(--white);" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-download"></i></a></div>
<?php
                }

                if($rss["delete"] == "oui") {
?>
                                <span><?= nl2br(htmlspecialchars_decode(trim($rss["message"]))); ?></span>
                                <br><span style="color: var(--red); font-size: 0.6rem;">Ce message a été supprimé</span>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }else {
?>
                                <span><?= nl2br(makeLinksClickable(htmlspecialchars_decode(trim($rss["message"])))) ?></span>
                            </div>
                            <div class="forms">
                                <form method="post">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rss["id"], $key)); ?>">
                                    <button type="submit" name="modify_sms"><i class="fas fa-square-pen" style="color: var(--orange);"></i></button>
                                </form>
                                <form method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer le message ?')">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars(encrypt($rss["id"], $key)); ?>">
                                    <button type="submit" name="trash"><i class="fas fa-trash" style="color: var(--red);"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="date">
                            <span><?= htmlspecialchars($rss["dateS"]) ?></span>
                            <span><?= ($rss["vue"] == "oui") ? "<i class=\"fas fa-check-double\" style=\"color: var(--blue);\"></i>" : "<i class=\"fas fa-check\" style=\"color: var(--blue);\"></i>"; ?></span>
                        </div>
                    </div>
<?php
                }
            }else {
                echo "";
            }
        } else if ($rss['idu'] == $idd && $rss['id_receiver'] == $myid && $rss["delete"] == "non") {
            if($rss["docs"] == null && $rss["message"] !== null) {
?>
                    <div class="you">
                        <div class="text"><?= nl2br(makeLinksClickable(htmlspecialchars_decode(trim($rss["message"])))) ?></div>
                        <div class="date"><?= htmlspecialchars($rss["dateS"]) ?></div>
                    </div>
<?php
            }else if($rss["docs"] !== null && $rss["message"] == null) {
?>
                    <div class="you">
                        <div class="text">
<?php
                $fichierextension = strtolower(pathinfo($rss["docs"], PATHINFO_EXTENSION));

                $fichierimg = array('png','jpg','jpeg','webp');
                $fichiervideo_audio = array('mp4','mp3','webm');
                $fichierdoc = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip');

                if(in_array($fichierextension, $fichierimg)) {
?>
                            <img style="width: 100%; height: 350px; border-radius: 10px;" src="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>" alt="<?= htmlspecialchars($rss["docs"]); ?>">
<?php
                }else if(in_array($fichierextension, $fichiervideo_audio)) {
?>
                            <div style="padding: 15px; background-color: var(--green); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a style="color: var(--white);" target="_blank" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-play"></i></a></div>
<?php
                }else if(in_array($fichierextension, $fichierdoc)) {
?>
                            <div style="padding: 15px; background-color: var(--green); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a download style="color: var(--white);" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-download"></i></a></div>
<?php
                }
?>
                        </div>
                        <div class="date"><?= htmlspecialchars($rss["dateS"]) ?></div>
                    </div>
<?php
            }else if($rss["docs"] !== null && $rss["message"] !== null) {
?>
                    <div class="you">
                        <div class="text">
<?php
                $fichierextension = strtolower(pathinfo($rss["docs"], PATHINFO_EXTENSION));

                $fichierimg = array('png','jpg','jpeg','webp');
                $fichiervideo_audio = array('mp4','mp3','webm');
                $fichierdoc = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip');

                if(in_array($fichierextension, $fichierimg)) {
?>
                            <img style="width: 100%; height: 350px; border-radius: 10px;" src="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>" alt="<?= htmlspecialchars($rss["docs"]); ?>">
<?php
                }else if(in_array($fichierextension, $fichiervideo_audio)) {
?>
                            <div style="padding: 15px; margin: 0 0 10px 0; background-color: var(--green); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a style="color: var(--white);" target="_blank" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-play"></i></a></div>
<?php
                }else if(in_array($fichierextension, $fichierdoc)) {
?>
                            <div style="padding: 15px; margin: 0 0 10px 0; background-color: var(--green); border-radius: 10px; color: var(--white);"><span><?= htmlspecialchars($rss["docs"]); ?></span> &nbsp;&nbsp;<a download style="color: var(--white);" href="../assets/docs/<?= htmlspecialchars($rss["docs"]); ?>"><i class="fas fa-download"></i></a></div>
<?php
                }
?>
                            <span><?= nl2br(makeLinksClickable(htmlspecialchars_decode(trim($rss["message"])))) ?></span>
                        </div>
                        <div class="date"><?= htmlspecialchars($rss["dateS"]) ?></div>
                    </div>
<?php 
            }else {
                echo "";
            }
        }
    }
?>