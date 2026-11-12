<?php

// Relances des opportunités (extension opportunity-reminders): its section of
// the Administration page and its settings page.

/** @var array<string, string> $mod_strings Set by SuiteCRM */
$mod_strings['LBL_OR_ADMIN_HEADER'] = 'Relances des opportunités';
$mod_strings['LBL_OR_ADMIN_HEADER_DESC'] = 'Tâches de relance pour les opportunités qui n\'avancent plus.';
$mod_strings['LBL_OR_ADMIN_TITLE'] = 'Relances des opportunités';
$mod_strings['LBL_OR_ADMIN_DESC'] = 'Régler le délai avant relance, lancer les relances.';
$mod_strings['LBL_OR_PAGE_INTRO'] = 'Chaque matin à 7 h (Administration → Planificateur), une tâche « Relancer » est créée pour le commercial de chaque opportunité ouverte dont la fiche n\'a pas été modifiée depuis ce nombre de jours. Jamais deux : pas de nouvelle relance tant qu\'une tâche liée à l\'opportunité n\'est pas terminée, ni avant le même délai une fois qu\'elle l\'est.';
$mod_strings['LBL_OR_DAYS'] = 'Relancer après (jours sans modification)';
$mod_strings['LBL_OR_DAYS_HELP'] = 'de 1 à 365, 30 par défaut';
$mod_strings['LBL_OR_SAVE'] = 'Enregistrer';
$mod_strings['LBL_OR_RUN'] = 'Enregistrer et lancer maintenant';
$mod_strings['LBL_OR_SAVED'] = 'Réglage enregistré : relance après %d jours sans modification.';
$mod_strings['LBL_OR_RAN'] = 'Relances lancées : %d tâche(s) créée(s).';
$mod_strings['LBL_OR_DAYS_INVALID'] = 'Le nombre de jours doit être un nombre entier de 1 à 365.';
