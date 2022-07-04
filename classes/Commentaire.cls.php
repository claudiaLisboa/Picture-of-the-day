<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Commentaire extends AccesBd
{
    private int $idIdj;

    public function __construct(int $idIdj)
    {
        parent::__construct();
        $this->idIdj = $idIdj;
    }

    /**
     * Retourne l'image de la date fournie.
     * @param string $date Date de l'image recherchée
     * @return object L'image recherchée
     */
    public function obtenirNombreAime(): int
    {
        $sql = "SELECT SUM(IFNULL(`com_aime`, 0)) AS com_aime FROM `commentaire` where `com_img_id_ce` = ?";
        $resultat = $this->lireUn($sql, array($this->idIdj));

        return $resultat->com_aime;
    }

    /**
     * Retourne la date de la première image.
     */
    public function datePremiereImage(): string
    {
        $sql = "SELECT MIN(`img_jour`) AS `img_jour` FROM `image`";
        $resultat = $this->lireUn($sql);

        return $resultat->img_jour;
    }
}
