<?php
require_once('./config/bd.cfg.php');

// Autochargement des fichiers de classes
spl_autoload_register(function ($nomClasse) {
    $nomFichier = "$nomClasse.cls.php";
    if (file_exists("classes/$nomFichier")) {
        include("classes/$nomFichier");
    }
});

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);



$dateAujourdhui = date("Y-m-d");

// On vérifie si l'utilisateur a navigué vers un jour spécifique.
if (isset($_GET["jour"])) {
    $date = $_GET["jour"];

    // Si l'utilisateur a entré manuellement une date
    // postérieure à aujourd'hui, on revient à aujourd'hui.
    if ($date > $dateAujourdhui) {
        $date = $dateAujourdhui;
    }
} else {
    // Sinon on prend la date d'aujourd'hui.
    $date = $dateAujourdhui;
}

// On instancie un objet ImageDuJour.
$idj = new ImageDuJour();
// Puis on l'utilise pour récuperer l'image de $date.
$image = $idj->unParDate($date);
// On récupère l'id de l'image. On va l'utiliser après pour la classe Commentaire.
$idImage = $image->img_id;
// On récupère la date de la prémière image qui existe dans la base des données.
$datePremiereImage = $idj->datePremiereImage();

// Demain de $date
$d = date_create_from_format("Y-m-d", $date);
date_add($d, date_interval_create_from_date_string("1 day"));
$dateDemain = date_format($d, "Y-m-d");
// Si la date de demain est postérieure à celle d'aujourd'hui
// on l'ajuste pour aujourd'hui.
if ($dateDemain > $dateAujourdhui) {
    $dateDemain = $dateAujourdhui;
}

// Hier de $date
$d = date_create_from_format("Y-m-d", $date);
date_sub($d, date_interval_create_from_date_string("1 day"));
$dateHier = date_format($d, "Y-m-d");
// Si la date d'hier est antérieure à celle de la prémière image
// on l'ajuste pour celle de la prémière image.
if ($dateHier < $datePremiereImage) {
    $dateHier = $datePremiereImage;
}


// On instancie la classe Commentaire avec l'id de l'image.
$c = new Commentaire($idImage);
// On récupère tous les commentaires de l'image.
$commentaires = $c->toutAvecVote();


/**
 * Cette fonction "traduit" le taux de votes positifs (le résultat de Utilitaire::tauxVotesPositifs)
 * dans l'une des classes v1-v5 (qui répresentent les différents degrés d'opacité des commentaires).
 */
function determineOpaciteCommentaires(float $taux): string
{
    // $taux est un numéro entre 0 et 1.
    // Il y a 5 classes distincts pour les commentaires (v1, v2, v3, v4, et v5).
    // Donc chaque classe réprésent une tranche de deux dixièmes (0.2).
    if (($taux >= 0.0) && ($taux <= 0.2)) {
        return "v5";
    } else if (($taux > 0.2) && ($taux <= 0.4)) {
        return "v4";
    } else if (($taux > 0.4) && ($taux <= 0.6)) {
        return "v3";
    } else if (($taux > 0.6) && ($taux <= 0.8)) {
        return "v2";
    } else if (($taux > 0.8) && ($taux <= 1.0)) {
        return "v1";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image du jour</title>
    <link rel="shortcut icon" href="ressources/images/favicon.png" type="image/png">
    <link rel="stylesheet" href="ressources/css/idj.css">
    <style>
        html {
            background-image: url(ressources/photos/<?php echo $image->img_fichier ?>);
        }
    </style>
</head>

<body>
    <div class="etiquette aime">
        <img src="ressources/images/aime-actif.png" alt=""><?php echo $c->obtenirNombreAime() ?>
    </div>
    <aside>
        <form action="">
            <textarea name="commentaire" id="commentaire"></textarea>
        </form>
        <ul class="commentaires">
            <?php
            foreach ($commentaires as $commentaire) {
                $taux = Utilitaire::tauxVotesPositifs($commentaire->vot_up, $commentaire->vot_down);

                echo "<li class='" . determineOpaciteCommentaires($taux) . "'>";
                echo $commentaire->com_texte;
                echo "<div class='vote'>";
                echo "<span class='up'>" . $commentaire->vot_up . "</span>";
                echo "<span class='down'>" . $commentaire->vot_down . "</span>";
                echo "</div>";
                echo "</li>";
            }
            ?>
        </ul>
    </aside>
    <div class="info">
        <div class="date">
            <span class="premier <?php if ($date == $datePremiereImage) echo "inactif"; ?>">
                <a title="Premier jour" href="index.php?jour=<?php echo $datePremiereImage ?>">&#x21E4;</a>
            </span>
            <span class="prec <?php if ($date == $datePremiereImage) echo "inactif"; ?>">
                <a title="Jour précédent" href="index.php?jour=<?php echo $dateHier ?>">&#x2B60;</a>
            </span>
            <span class="suiv <?php if ($date == $dateAujourdhui) echo "inactif"; ?>">
                <a title="Jour suivant" href="index.php?jour=<?php echo $dateDemain ?>">&#x2B62;</a>
            </span>
            <span class="dernier <?php if ($date == $dateAujourdhui) echo "inactif"; ?>">
                <a title="Aujourd'hui" href="index.php?jour=<?php echo $dateAujourdhui ?>">&#x21E5;</a>
            </span>
            <i><?php echo Utilitaire::dateFormatee($date) ?></i>
        </div>
        <div class="etiquette etiquette-etendue description"><?php echo $image->img_description ?></div>
    </div>
</body>

</html>