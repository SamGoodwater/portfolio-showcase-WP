# Portfolio Showcase

Une extension WordPress moderne pour créer et afficher des portfolios professionnels avec carrousel et palette de couleurs.

## Fonctionnalités

- **Type de publication personnalisé** pour les projets de portfolio
- **Carrousel d'images avancé** avec navigation infinie, prévisualisations et mode plein écran
- **Palette de couleurs interactive** avec commentaires et positions personnalisables
- **Paramètres globaux** pour une configuration centralisée
- **Options de style personnalisables** par projet
- **Design responsive** adapté à tous les appareils
- **Intégration facile** via shortcode
- **Support multilingue** avec fichiers de traduction

## Installation

1. Téléchargez l'extension
2. Uploadez le dossier `portfolio-showcase` dans le répertoire `/wp-content/plugins/`
3. Activez l'extension via le menu 'Extensions' dans WordPress
4. Commencez à créer vos projets de portfolio !

## Utilisation

### Création d'un projet

1. Allez dans le menu "Portfolio" dans l'administration WordPress
2. Cliquez sur "Ajouter un Nouveau Projet"
3. Remplissez les informations du projet :
   - Titre et description
   - Images pour le carrousel
   - Couleurs pour la palette
   - Options de style personnalisées
4. Publiez le projet

### Affichage d'un projet

Utilisez le shortcode suivant pour afficher un projet sur n'importe quelle page ou article :

```
[portfolio_showcase id="ID_DU_PROJET"]
```

Remplacez `ID_DU_PROJET` par l'ID du projet que vous souhaitez afficher.

### Configuration globale

1. Allez dans le menu "Portfolio" > "Settings" dans l'administration WordPress
2. Configurez les paramètres globaux pour le carrousel et la palette de couleurs
3. Ces paramètres serviront de valeurs par défaut pour tous les projets

## Personnalisation

### Options de style globales

Vous pouvez personnaliser l'apparence globale de vos projets de portfolio :

- **Couleurs** : Titres, descriptions, arrière-plans
- **Positions** : Titres, descriptions, commentaires
- **Comportements** : Mode plein écran, opacité

### Options de style par projet

Chaque projet peut avoir ses propres paramètres qui surchargent les paramètres globaux :

- **Carrousel** : Couleurs, positions, comportements
- **Palette de couleurs** : Couleurs, commentaires, hauteur des rectangles

### Carrousel

Le carrousel offre plusieurs options de personnalisation :

- **Positions du titre** : Haut-gauche, haut-droite, haut-centre, bas-gauche, bas-droite, bas-centre, centre
- **Positions de la description** : Haut, bas
- **Mode plein écran** : Activé/désactivé
- **Navigation** : Flèches, miniatures, clavier

### Palette de couleurs

La palette de couleurs peut être personnalisée avec :

- **Couleurs** : Ajoutez autant de couleurs que nécessaire
- **Commentaires** : Ajoutez des descriptions pour chaque couleur
- **Position des commentaires** : Haut ou bas
- **Hauteur des rectangles** : Ajustable selon vos préférences

## Traductions

L'extension est entièrement traduite en français et prête à être utilisée. Les fichiers de traduction se trouvent dans le dossier `languages/`.

### Comment changer la langue

1. Assurez-vous que la langue de votre site WordPress est définie sur français
2. Les traductions seront automatiquement chargées

### Comment ajouter une nouvelle traduction

1. Copiez le fichier `languages/portfolio-showcase.pot` et renommez-le selon le code de langue souhaité (par exemple, `portfolio-showcase-es_ES.po` pour l'espagnol)
2. Utilisez un éditeur de fichiers PO comme Poedit pour traduire les chaînes de caractères
3. Sauvegardez le fichier PO, ce qui générera automatiquement le fichier MO correspondant
4. Placez les deux fichiers (PO et MO) dans le dossier `languages/`

Pour plus d'informations sur les traductions, consultez le fichier `languages/README.md`.

## Compatibilité

- WordPress 6.0 ou supérieur
- PHP 7.4 ou supérieur
- Compatible avec les thèmes modernes

## Support

Pour toute question ou problème, veuillez créer une issue sur le dépôt GitHub de l'extension.

## Licence

Cette extension est distribuée sous licence GPL v2 ou ultérieure. 