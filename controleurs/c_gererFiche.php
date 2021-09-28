<?php

/**
 * Gestion des frais
 *
 * PHP Version 7
 * 
 * @category  PPE
 * @package   Wiki Fiche
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

$idCompte = $_SESSION['idCompte'];
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);


switch ($action) {
    case 'selectionnerFiche':
        $fiches = $pdo->getFiches();
        include('vues/v_listFiche.php');
        break;

    case 'insererFiche':
        include('vues/v_creerFiche.php');
        break;
    case 'insert':
        $idCategorie = filter_input(INPUT_POST, 'categorie', FILTER_SANITIZE_NUMBER_INT);
        $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_STRING);
        checkFiche($libelle, $description, $contenu);
        if (nbErreurs() !== 0) {
            include 'vues/v_erreurs.php';
            include 'vues/v_creerFiche.php';
        } else {
            $fiches = $pdo->insertFiches($idCategorie, $idCompte, $libelle, $description, $contenu);
            include 'vues/v_successful.php';
        }
        break;

    case 'visiterFiche':
        $idFiche = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $theFiche = $pdo->getTheFiche($idFiche);
        include 'vues/v_fiche.php';
        break;

    case 'likerFiche':
        $idFiche = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $lignes = $pdo->checkLike($idCompte, $idFiche);
        if ($lignes['nb'] == 0) {
            $pdo->likerFiche($idFiche);
            $pdo->insertLike($idCompte, $idFiche);
            include 'vues/v_successful.php';
        } else {
            $pdo->unLike($idFiche);
            $pdo->deleteLike($idCompte, $idFiche);
            include 'vues/v_message.php';
        }
        $fiches = $pdo->getFiches();
        include('vues/v_listFiche.php');
    case 'getFicheByCategorie':
        if (isset($_POST['idcateg'])) {
            foreach ($_POST['idcateg'] as $idCategorie) {
                $fiches[] = $pdo->getFicheByCategorie($idCategorie);
            }
        } else {
            header("Location: index.php?uc=gererFiche&action=selectionnerFiche");
        }
        include 'vues/v_listFiche.php';
        break;
}
