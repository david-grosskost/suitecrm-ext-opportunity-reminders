# Fonctionnement

## Fichiers

| Fichier | Rôle |
|---|---|
| `legacy/include/opportunity-reminders/Reminders.php` | la règle : quelles opportunités relancer, la tâche créée |
| `legacy/include/opportunity-reminders/Settings.php` | le nombre de jours, dans les réglages de SuiteCRM |
| `legacy/include/opportunity-reminders/Page.php`, `PageActions.php`, `PageView.php`, `SettingsPage.php`, `page.tpl` | la page d'administration |
| `legacy/include/opportunity-reminders/SchedulerRecord.php` | la ligne du planificateur, créée et supprimée par les scripts d'installation |
| `legacy/Extension/modules/Schedulers/Ext/ScheduledTasks/opportunity-reminders.php` | la tâche planifiée `opportunityReminders` |
| `legacy/Extension/modules/Administration/Ext/` | l'entrée dans Administration, la page, leurs libellés |
| `legacy/Extension/application/Ext/Language/` | le nom et la description des tâches créées (français, anglais) |
| `database/install.php`, `uninstall.php` | création et suppression du planificateur |
| `database/seeders/opportunity-reminders/` | les données de démo |
| `tests/` | tests de la règle, du réglage, de la page et du planificateur |

## La règle de relance

Une opportunité est relancée quand elle est :

- ouverte : son étape de vente n'est ni « Gagnée » ni « Perdue » ;
- non modifiée depuis au moins le nombre de jours réglé ;
- sans tâche liée en cours, et sans tâche liée terminée depuis moins de ce même délai.

La tâche est assignée au commercial de l'opportunité. Son nom est tronqué à 50 caractères
(longueur du champ dans SuiteCRM). Les textes viennent des libellés de l'application ; à
défaut, en anglais.

## Données en base

- Le nombre de jours : table `config`, catégorie `opportunity_reminders`.
- La ligne « Relances des opportunités » de la table `schedulers`.
- Les tâches créées : des tâches ordinaires (`tasks`), liées à l'opportunité.

## Installation et désinstallation

- **Première installation** (`database/install.php`) : crée la ligne du planificateur,
  chaque jour à 7 h. Elle appartient ensuite à l'administrateur : une mise à jour de
  l'extension ne repasse pas ce script et garde son horaire.
- **Désinstallation** (`database/uninstall.php`) : supprime la ligne du planificateur, avant
  le retrait des fichiers (sinon il appellerait une fonction disparue). Les tâches créées et
  le nombre de jours restent ; réinstallée, l'extension recrée le planificateur.

## Points d'attention

- **Le réglage dépend d'upstream-fixes** : SuiteCRM 8.10 lit les réglages d'administration
  dans `$_GET` alors que les formulaires les envoient en `POST` ; sans le correctif, le
  nombre de jours enregistré ne serait jamais pris en compte.
- Les scripts d'installation renvoient leur message au lieu de l'afficher : le scanner du
  Chargeur de module refuse `echo`.
- SuiteCRM ne charge que le fichier de la tâche planifiée pour l'exécuter : ce fichier
  charge lui-même `Settings` et `Reminders`.
