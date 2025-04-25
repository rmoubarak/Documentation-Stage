<?php

namespace App\Services;

class Date
{
    /**
     * Retourne un tableau contenant la différence entre deux dates
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public static function getRange(\DateTime $from = null, \DateTime $to = null): bool|array
    {
        if (!$from || !$to) {
            return false;
        }

        $interval = $from->diff($to);

        return ['years' => $interval->y, 'months' => $interval->m, 'days' => $interval->d];
    }

    /**
     * Vérifie si une date en string est valide du point de vue formel et fonctionnel (30/02/2018 renvoie false)
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate(string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        if (!$date) {
            return false;
        }

        $d = \DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    public static function getMois(): array
    {
        return [
            '01' => ['long' => 'Janvier', 'court' => 'Jan'],
            '02' => ['long' => 'Février', 'court' => 'Fév'],
            '03' => ['long' => 'Mars', 'court' => 'Mar'],
            '04' => ['long' => 'Avril', 'court' => 'Avr'],
            '05' => ['long' => 'Mai', 'court' => 'Mai'],
            '06' => ['long' => 'Juin', 'court' => 'Jui'],
            '07' => ['long' => 'Juillet', 'court' => 'Jui'],
            '08' => ['long' => 'Août', 'court' => 'Aoû'],
            '09' => ['long' => 'Septembre', 'court' => 'Sep'],
            '10' => ['long' => 'Octobre', 'court' => 'Oct'],
            '11' => ['long' => 'Novembre', 'court' => 'Nov'],
            '12' => ['long' => 'Décembre', 'court' => 'Déc'],
        ];
    }
}