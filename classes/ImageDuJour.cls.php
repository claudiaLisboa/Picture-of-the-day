<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class ImageDuJour extends AccesBd
{
    private string $jour;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retourne l'image de la date fournie.
     * @param string $date Date de l'image recherchée
     * @return object L'image recherchée
     */
    public function unParDate(string $date): object
    {
        $sql = "SELECT `img_id`, `img_fichier`, `img_description` FROM `image` WHERE `img_jour` = ?";
        $resultat = $this->lireUn($sql, array($date));

        return $resultat;
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
