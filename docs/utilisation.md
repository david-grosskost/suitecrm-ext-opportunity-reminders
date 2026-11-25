# Utilisation

## Ce qui change

Chaque matin, le commercial assigné à une opportunité ouverte qui n'a pas bougé depuis le
délai réglé reçoit une tâche :

- nom : « Relancer : <nom de l'opportunité> » ;
- statut « Non démarrée », échéance le jour même ;
- liée à l'opportunité : elle apparaît dans ses **Activités** et dans les tâches du
  commercial.

Une opportunité n'est relancée qu'une fois à la fois : pas de nouvelle tâche tant qu'une
tâche liée n'est pas terminée, ni avant le même délai une fois qu'elle l'est. Créer la tâche
ne modifie pas l'opportunité.

## Réglages

- **Administration → Relances des opportunités** : le nombre de jours sans modification
  avant une relance, de 1 à 365 (30 par défaut). **Enregistrer et lancer maintenant** crée
  tout de suite les relances dues, sans attendre le lendemain matin (pratique en démo).
- **Administration → Planificateur → Relances des opportunités** : l'horaire (chaque jour à
  7 h, à l'heure de l'administrateur), ou rendre la tâche inactive pour suspendre les
  relances.

## Données de démo

`make db@seed` (ou le workflow Seed d'une démo) laisse quatre opportunités ouvertes de
`core/opportunities` sans modification depuis 35 à 90 jours
(`opportunity-reminders/opportunities`). **Enregistrer et lancer maintenant** crée alors
leurs relances.

## Limites

- La relance ne regarde que la date de modification de la fiche de l'opportunité : un appel
  ou une réunion enregistrés sans toucher à la fiche ne repoussent pas la relance.
- Aucune notification par e-mail : la relance est une tâche, visible dans SuiteCRM.
