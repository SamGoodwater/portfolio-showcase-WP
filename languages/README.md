# Traductions pour Portfolio Showcase

Ce dossier contient les fichiers de traduction pour l'extension Portfolio Showcase.

## Fichiers disponibles

- `portfolio-showcase.pot` : Fichier template pour créer de nouvelles traductions
- `portfolio-showcase-fr_FR.po` : Fichier de traduction en français
- `portfolio-showcase-fr_FR.mo` : Fichier compilé de traduction en français (généré à partir du fichier PO)

## Comment utiliser les traductions

1. Les traductions sont automatiquement chargées par WordPress lorsque la langue du site est définie sur français.

2. Pour activer les traductions, assurez-vous que :
   - La langue de votre site WordPress est définie sur français
   - Les fichiers PO et MO sont présents dans ce dossier

## Comment créer une nouvelle traduction

1. Copiez le fichier `portfolio-showcase.pot` et renommez-le selon le code de langue souhaité (par exemple, `portfolio-showcase-es_ES.po` pour l'espagnol)

2. Utilisez un éditeur de fichiers PO comme Poedit pour traduire les chaînes de caractères

3. Sauvegardez le fichier PO, ce qui générera automatiquement le fichier MO correspondant

4. Placez les deux fichiers (PO et MO) dans ce dossier

## Comment mettre à jour les traductions

Si vous mettez à jour l'extension et ajoutez de nouvelles chaînes de caractères à traduire :

1. Utilisez un outil comme `msgmerge` pour mettre à jour votre fichier PO avec les nouvelles chaînes :
   ```
   msgmerge -U portfolio-showcase-fr_FR.po portfolio-showcase.pot
   ```

2. Traduisez les nouvelles chaînes dans le fichier PO mis à jour

3. Recompilez le fichier MO à partir du fichier PO mis à jour

## Comment compiler manuellement les fichiers MO

Si vous avez besoin de compiler manuellement un fichier PO en MO, vous pouvez utiliser la commande `msgfmt` :

```
msgfmt -o portfolio-showcase-fr_FR.mo portfolio-showcase-fr_FR.po
```

Ou utiliser le script `compile-translations.php` fourni à la racine de l'extension :

```
php compile-translations.php
``` 