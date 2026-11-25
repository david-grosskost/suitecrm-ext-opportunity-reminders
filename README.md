# Relances des opportunités (`opportunity-reminders`)

**Une opportunité qui n'avance plus n'est jamais oubliée.** Chaque matin à 7 h, le
commercial reçoit une tâche « Relancer : <opportunité> » pour chacune de ses opportunités
ouvertes (ni gagnées ni perdues) dont la fiche n'a pas été modifiée depuis un nombre de
jours réglable, 30 par défaut. Jamais deux relances pour la même opportunité, et créer une
tâche ne modifie pas l'opportunité : c'est au commercial de la faire avancer.

| Fiche | |
|---|---|
| Version de SuiteCRM | 8.10 (`>=8.10 <9`) |
| Types | `scheduler`, `ui`, `lang` |
| Dépend de | [upstream-fixes](https://github.com/david-grosskost/suitecrm-ext-upstream-fixes) (installée d'office), pour l'enregistrement du réglage |
| Installée d'office | non |

## Installer

Sur une instance, par **Administration → Chargeur de module** : l'extension y figure dans
la liste des paquets disponibles, cliquer sur **Installer**. Depuis le terminal, dans
suitecrm-core :

```bash
make extensions@install SLUGS="opportunity-reminders"
```

La première installation crée la ligne « Relances des opportunités » du planificateur. La
désinstallation se fait au même endroit et la supprime.

## Documentation

| Page | Contenu |
|---|---|
| [Utilisation](docs/utilisation.md) | les relances, le réglage du délai, l'horaire, les données de démo |
| [Fonctionnement](docs/fonctionnement.md) | la règle de relance, la tâche planifiée, la page d'administration, installation et désinstallation |
| [Développement](docs/developpement.md) | vérifier, tester en local, publier une version |

Les commandes, la CI et le format des extensions sont décrits dans la documentation de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation).
