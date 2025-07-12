<?php 
    session_start();
    try {
        $bdd = new PDO('mysql:host=localhost; dbname=minichat', 'root', '');
    }catch (exception $e) {
        die('Erreur de connexion à la base de données');
    }
?>