<?php
class Utilitaire
{
    /**
     * Retourne le taux de votes positifs, soit les positifs divisés
     * par les positifs plus les négatifs.
     * 
     * @param int $votesPositifs - Total de votes positifs
     * @param int $votesNegatifs - Total de votes négatifs
     * @return int Quotient entre les votes positifs et la somme des positifs et des négatifs
     */
    public static function tauxVotesPositifs(int $votesPositifs, int $votesNegatifs): float
    {
        $numerateur = $votesPositifs;
        $denominateur = $votesPositifs + $votesNegatifs;

        if ($denominateur == 0) {
            return 0;
        } else {
            return $numerateur / $denominateur;
        }
    }

    /**
     * Retourne la date fournie dans le format Lundi, 20 juin 2022
     * 
     * @param string $date - Date dans le format 2022-06-20
     * @return string Date fournie dans le format Lundi, 20 juin 2022
     */
    public static function dateFormatee(string $date): string
    {
        $mydate = date_create_from_format("Y-m-d", $date);
        return date_format($mydate, "l, j F Y");
    }
}
