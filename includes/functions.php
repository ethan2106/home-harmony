<?php

/**
 * Fonctions utilitaires pour l'application Harmony
 */

/**
 * Retourne la date du jour en français
 */
/**
 * @return string
 */
function getFrenchDate()
{
    $jours = [
        'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi',
        'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi',
        'Sunday' => 'Dimanche',
    ];
    $mois = [
        'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars',
        'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin',
        'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre',
        'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre',
    ];

    return $jours[date('l')] . ' ' . date('d') . ' ' . $mois[date('F')];
}
