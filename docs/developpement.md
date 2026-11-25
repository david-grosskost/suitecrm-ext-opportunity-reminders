# Développement

Les commandes `make`, la CI et le format des extensions viennent de
[suitecrm-devkit](https://github.com/david-grosskost/suitecrm-devkit#documentation), à la
version de `composer.json`. Cette page ne décrit que ce qui est propre à l'extension.

## Vérifier

```bash
make extension@validate && make quality@lint && make quality@stan && make test@unit
make package@build && make package@scan
```

## Tester sur une instance locale

1. `make package@build`, puis sur l'instance locale de suitecrm-core, **Administration →
   Chargeur de module** : **Choisir un fichier**
   (`build/opportunity-reminders-<version>.zip`), **Télécharger**, **Installer**.
2. Vérifier que **Administration → Planificateur** contient « Relances des opportunités ».
3. Dans suitecrm-core, `make db@seed` (quatre opportunités sans modification depuis 35 à
   90 jours), puis **Administration → Relances des opportunités → Enregistrer et lancer
   maintenant** : quatre tâches « Relancer : … » doivent apparaître.
4. Relancer : aucune tâche de plus (pas de doublon).
5. Dans suitecrm-core : `make test@regression`.

## Publier une version

Pull request relue, puis merge (squash) : release-please ouvre la pull request
« chore(main): release x.y.z ». La merger crée le tag, la release et son paquet.
suitecrm-core la récupère avec
`make composer@update PACKAGES="david-grosskost/suitecrm-ext-opportunity-reminders"`.

Mettre à jour `README.md` et `docs/` dans la même pull request que le changement qu'ils
décrivent.
