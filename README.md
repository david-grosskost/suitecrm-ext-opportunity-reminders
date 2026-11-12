# Relances des opportunités

Extension SuiteCRM 8 : **une opportunité qui n'avance plus n'est jamais oubliée.** Chaque
matin à 7 h, le commercial reçoit une tâche « Relancer : <opportunité> » pour chacune de ses
opportunités ouvertes (ni gagnées ni perdues) dont la fiche n'a pas été modifiée depuis un
nombre de jours réglable, 30 par défaut.

Jamais deux relances pour la même opportunité : pas de nouvelle tâche tant qu'une tâche liée à
l'opportunité n'est pas terminée, ni avant le même délai une fois qu'elle l'est. Créer une
tâche ne modifie pas l'opportunité : c'est au commercial de la faire avancer.

## Utiliser

- **Administration → Relances des opportunités** : le nombre de jours (de 1 à 365), et
  **Enregistrer et lancer maintenant** pour créer tout de suite les relances dues (en démo,
  pour ne pas attendre 7 h).
- **Administration → Planificateur → Relances des opportunités** : l'horaire (chaque jour à
  7 h, à l'heure de l'administrateur), ou rendre la tâche inactive.
- Les relances sont des tâches ordinaires : dans **Activités** de l'opportunité et dans les
  tâches du commercial (statut « Non démarrée », échéance le jour même).

## Activer, désactiver

Dans le `.env.local` de l'instance, ajouter `opportunity-reminders` à `EXTENSIONS` (avec
`upstream-fixes`), puis redémarrer :

- **à la première activation**, le planificateur « Relances des opportunités » est créé
  (`database/install.php`) ; il appartient ensuite à l'administrateur, qui peut changer son
  horaire ;
- **à la désactivation**, il est supprimé (`database/uninstall.php`). Les tâches déjà créées et
  le nombre de jours restent ; réactivée, l'extension recrée le planificateur.

## Données de démo

Avec l'extension active, `make db@seed` (ou le workflow Seed d'une démo) laisse quatre
opportunités ouvertes de `core/opportunities` sans modification depuis 35 à 90 jours
(`opportunity-reminders/opportunities`) : **Enregistrer et lancer maintenant** crée alors
leurs relances.

## Où est quoi

| Fichier | Rôle |
|---|---|
| `legacy/include/opportunity-reminders/Reminders.php` | quelles opportunités relancer, la tâche créée |
| `legacy/include/opportunity-reminders/Settings.php` | le nombre de jours (réglages de SuiteCRM, catégorie `opportunity_reminders`) |
| `legacy/include/opportunity-reminders/Page.php`, `PageView.php`, `SettingsPage.php` | la page d'administration |
| `legacy/include/opportunity-reminders/SchedulerRecord.php` | la ligne du planificateur |
| `legacy/Extension/modules/Schedulers/Ext/ScheduledTasks/` | la tâche planifiée `opportunityReminders` |
| `legacy/Extension/modules/Administration/Ext/` | l'entrée dans Administration, la page, leurs libellés |
| `legacy/Extension/application/Ext/Language/` | le nom et la description des tâches créées |
| `database/` | création et suppression du planificateur, données de démo |

## Vérifier

```bash
make extension@validate && make quality@lint && make quality@stan && make test@unit
```
