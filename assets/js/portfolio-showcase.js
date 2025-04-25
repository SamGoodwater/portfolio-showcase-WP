/**
 * Portfolio Showcase - JavaScript principal
 * 
 * Ce fichier contient les classes et fonctions nécessaires pour gérer
 * les fonctionnalités du carousel et de la palette de couleurs du plugin Portfolio Showcase.
 */

jQuery(document).ready(function($) {
    // =====================================================
    // CONFIGURATION GLOBALE
    // =====================================================
    
    // Récupérer les paramètres globaux depuis l'objet window
    const globalSettings = window.portfolioShowcaseSettings?.defaultSettings || {};
    
    /**
     * Récupère une valeur de paramètre en priorisant les paramètres locaux,
     * puis les paramètres globaux, et enfin une valeur par défaut
     * @param {Object} localSettings - Les paramètres locaux
     * @param {string} key - La clé du paramètre à récupérer
     * @param {string} defaultValue - La valeur par défaut si aucune autre n'est trouvée
     * @returns {*} La valeur du paramètre
     */
    function getSettingsValue(localSettings, key, defaultValue) {
        // Vérifier d'abord les paramètres locaux
        if (localSettings && key in localSettings) {
            return localSettings[key];
        }
        
        // Puis vérifier les paramètres globaux
        if (globalSettings && key in globalSettings) {
            return globalSettings[key];
        }
        
        // Enfin, retourner la valeur par défaut
        return defaultValue;
    }

    /**
     * Applique un style à un ou plusieurs éléments
     * @param {jQuery} $container - Le conteneur jQuery dans lequel chercher les éléments
     * @param {string} selector - Le sélecteur CSS pour cibler les éléments
     * @param {string} property - La propriété CSS à modifier
     * @param {string|number} value - La valeur à appliquer
     * @param {boolean} [useVariable=false] - Si true, applique la valeur comme variable CSS
     */
    function applyStyle($container, selector, property, value, useVariable = false) {
        if (!selector) {
            // Appliquer directement au conteneur
            if (useVariable) {
                $container.get(0).style.setProperty(`--${property}`, value);
            } else {
                $container.css(property, value);
            }
            return;
        }

        const $elements = $container.find(selector);
        
        if (useVariable) {
            $container.get(0).style.setProperty(`--${property}`, value);
        } else {
            $elements.css(property, value);
        }
    }
    
    // =====================================================
    // CLASSE PORTFOLIO CAROUSEL
    // =====================================================
    
    /**
     * Classe principale pour gérer le carousel d'images
     * Gère la navigation, le plein écran, les prévisualisations et les miniatures
     */
    class PortfolioCarousel {
        /**
         * Constructeur - Initialise le carousel
         * @param {HTMLElement} element - L'élément DOM du carousel
         */
        constructor(element) {
            // Éléments DOM principaux
            this.element = $(element);
            this.container = this.element.find('.carousel-container');
            this.slides = this.element.find('.carousel-slide');
            
            // État du carousel
            this.currentIndex = 0;
            this.isFullscreen = false;
            
            // Éléments de navigation
            this.thumbnailsContainer = null;
            this.previewElements = null;
            this.descriptions = Array.from(this.element.find('.carousel-slide-description'));
            
            // Cache des éléments fréquemment utilisés
            this.$titles = this.element.find('.carousel-title');
            this.$descriptions = this.element.find('.carousel-slide-description');
            this.$arrows = this.element.find('.carousel-nav-arrow');
            
            // Namespace unique pour les événements
            this.namespace = '.portfolio-carousel-' + Math.random().toString(36).substr(2, 9);
            
            // Récupérer et fusionner les paramètres
            const localSettings = JSON.parse(this.element.attr('data-settings') || '{}');
            this.settings = $.extend(true, {}, globalSettings.carousel || {}, localSettings);
            
            // Initialiser le carousel
            this.init();
        }

        /**
         * Initialise le carousel et configure tous les composants
         */
        init() {
            if (this.slides.length === 0) {
                console.warn('No slides found in carousel');
                return;
            }

            this.cleanup();
            this.setupSlides();
            
            if (this.slides.length > 1) {
                this.setupPreviews();
                this.setupThumbnails();
                this.setupNavigationArrows();
            }
            
            this.showSlide(0);
            this.setupFullscreen();
            this.setupKeyboardNavigation();
            this.setupClickHandlers();
            
            this.update();
            this.updateSize();        
        }

        /**
         * Met à jour tous les styles du carousel
         */
        update() {
            this.updateColors();
            this.updateBackgroundColor();
            this.updateTitleStyles();
            this.updateSize();
        }

        /**
         * Met à jour les couleurs du carousel
         */
        updateColors() {
            const settings = this.settings;
            const isFullscreen = this.isFullscreen;
            
            const titleColor = getSettingsValue(
                settings,
                isFullscreen ? 'local-carousel-color-title-fullscreen' : 'local-carousel-color-title',
                isFullscreen ? '#f5f5f5' : '#f5f5f5'
            );
            
            const descriptionColor = getSettingsValue(
                settings,
                isFullscreen ? 'local-carousel-color-description-fullscreen' : 'local-carousel-color-description',
                isFullscreen ? '#f2f7f5' : '#2e3d38'
            );
            
            // Appliquer les couleurs aux titres et descriptions
            applyStyle(this.element, '.carousel-title', 'color', titleColor);
            applyStyle(this.element, '.carousel-slide-description', 'color', descriptionColor);
            

        }

        /**
         * Met à jour la couleur de fond du carousel
         */
        updateBackgroundColor() {
            const settings = this.settings;
            const isFullscreen = this.isFullscreen;
            
            if (isFullscreen) {
                const backgroundColor = getSettingsValue(
                    settings,
                    'local-carousel-color-background-fullscreen',
                    '#121212'
                );
                
                const opacity = getSettingsValue(
                    settings,
                    'local-carousel-opacity-background-fullscreen',
                    90
                );
                
                const r = parseInt(backgroundColor.substring(1, 3) || '00', 16);
                const g = parseInt(backgroundColor.substring(3, 5) || '00', 16);
                const b = parseInt(backgroundColor.substring(5, 7) || '00', 16);
                
                applyStyle(
                    this.element,
                    null,
                    'background-color',
                    `rgba(${r}, ${g}, ${b}, ${opacity / 100})`
                );
            } else {
                const backgroundColor = getSettingsValue(
                    settings,
                    'local-carousel-color-background',
                    '#f5f5f5'
                );
                
                applyStyle(this.element, null, 'background-color', backgroundColor);
            }
        }

        /**
         * Met à jour les styles des titres
         */
        updateTitleStyles() {
            const $titleContainers = this.element.find('.carousel-title-container');
            const isFullscreen = this.element.hasClass('fullscreen');
            
            $titleContainers.each((_, container) => {
                const $container = $(container);
                const position = this.settings['local-carousel-position-title'] || 'top-left';
                
                // Supprimer toutes les classes de position existantes
                $container.removeClass('top-left top-right top-center bottom-left bottom-right bottom-center center');
                
                // Ajouter la classe fullscreen si nécessaire
                if (isFullscreen) {
                    $container.addClass('fullscreen');
                } else {
                    $container.removeClass('fullscreen');
                }
                
                // Ajouter la nouvelle classe de position
                $container.addClass(position);
            });
        }

        /**
         * Nettoie les événements et éléments précédents
         * Évite les fuites de mémoire et les comportements inattendus
         */
        cleanup() {
            // Nettoyer les événements précédents
            this.element.off(this.namespace);
            this.slides.off(this.namespace);
            $(document).off(this.namespace);

            // Supprimer les éléments de navigation précédents
            if (this.previewElements) {
                this.previewElements.remove();
            }
            if (this.thumbnailsContainer) {
                this.thumbnailsContainer.remove();
            }
        }

        /**
         * Configure les slides et leurs attributs
         */
        setupSlides() {
            // Ajouter l'index à chaque slide
            this.slides.each((index, slide) => {
                $(slide).attr('data-index', index);
            });

            // Ajouter le gestionnaire de clic sur les images pour le plein écran
            this.slides.on('click' + this.namespace, 'img', (e) => {
                e.stopPropagation();
                this.toggleFullscreen();
            });
        }

        /**
         * Configure les prévisualisations des images précédentes/suivantes
         */
        setupPreviews() {
            if (this.slides.length > 1) {
                this.updatePrevNextPreviews();
            }
        }

        /**
         * Met à jour les prévisualisations des images précédentes/suivantes
         * Crée les éléments DOM nécessaires et configure les événements
         */
        updatePrevNextPreviews() {
            // Supprimer les prévisualisations existantes
            this.element.find('.carousel-preview-container, .carousel-preview-image, .carousel-preview-button').remove();
            
            if (this.slides.length <= 1) return;
            
            // Calculer les index pour le défilement infini
            const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            const nextIndex = (this.currentIndex + 1) % this.slides.length;
            
            // ===== PRÉVISUALISATION PRÉCÉDENTE =====
            
            // Créer le conteneur
            const prevContainer = $('<div class="carousel-preview-container prev"></div>');
            this.container.append(prevContainer);
            
            // Créer l'image de prévisualisation
            const prevSlide = $(this.slides[prevIndex]);
            const prevImg = prevSlide.find('img').attr('src');
            if (prevImg) {
                const prevPreviewImage = $('<div class="carousel-preview-image prev"></div>');
                prevPreviewImage.css('background-image', `url('${prevImg}')`);
                prevContainer.append(prevPreviewImage);
            }
            
            // Créer le bouton de prévisualisation
            const prevPreviewButton = $('<div class="carousel-preview-button prev"></div>');
            prevContainer.append(prevPreviewButton);
            
            // Ajouter le gestionnaire d'événements
            prevPreviewButton.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.prevSlide();
            });
            
            // ===== PRÉVISUALISATION SUIVANTE =====
            
            // Créer le conteneur
            const nextContainer = $('<div class="carousel-preview-container next"></div>');
            this.container.append(nextContainer);
            
            // Créer l'image de prévisualisation
            const nextSlide = $(this.slides[nextIndex]);
            const nextImg = nextSlide.find('img').attr('src');
            if (nextImg) {
                const nextPreviewImage = $('<div class="carousel-preview-image next"></div>');
                nextPreviewImage.css('background-image', `url('${nextImg}')`);
                nextContainer.append(nextPreviewImage);
            }
            
            // Créer le bouton de prévisualisation
            const nextPreviewButton = $('<div class="carousel-preview-button next"></div>');
            nextContainer.append(nextPreviewButton);
            
            // Ajouter le gestionnaire d'événements
            nextPreviewButton.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.nextSlide();
            });
            
            // Stocker les références pour le nettoyage
            this.previewElements = this.element.find('.carousel-preview-container');
        }

        /**
         * Configure les miniatures pour la navigation rapide
         */
        setupThumbnails() {
            // Créer le conteneur des miniatures
            this.thumbnailsContainer = $('<div class="carousel-thumbnails"></div>');
            
            // Créer une miniature pour chaque slide
            this.slides.each((index, slide) => {
                const img = $(slide).find('img');
                if (img.length) {
                    // Créer l'élément miniature
                    const thumbnail = $('<div class="carousel-thumbnail"></div>')
                        .attr('data-index', index)
                        .attr('aria-label', `Go to slide ${index + 1}`);
                        
                    // Créer l'image miniature
                    const thumbImg = $('<img>')
                        .attr('src', img.attr('src'))
                        .attr('alt', img.attr('alt') || `Thumbnail ${index + 1}`);
                    
                    thumbnail.append(thumbImg);
                    this.thumbnailsContainer.append(thumbnail);
                }
            });
            
            // Ajouter le conteneur au carousel
            this.element.append(this.thumbnailsContainer);
            
            // Ajouter le gestionnaire d'événements pour la navigation
            this.thumbnailsContainer.on('click' + this.namespace, '.carousel-thumbnail', (e) => {
                e.stopPropagation();
                const index = $(e.currentTarget).data('index');
                this.showSlide(index);
            });
        }

        /**
         * Configure la gestion du mode plein écran
         */
        setupFullscreen() {
            // Gérer les changements d'état du plein écran pour différents navigateurs
            $(document).on('fullscreenchange' + this.namespace + 
                         ' webkitfullscreenchange' + this.namespace + 
                         ' mozfullscreenchange' + this.namespace + 
                         ' MSFullscreenChange' + this.namespace, () => {
                // Détecter l'état du plein écran pour différents navigateurs
                const isFullscreen = document.fullscreenElement || 
                                   document.webkitFullscreenElement || 
                                   document.mozFullScreenElement || 
                                   document.msFullscreenElement;
                
                // Mettre à jour l'état du carousel
                this.isFullscreen = !!isFullscreen && isFullscreen === this.element[0];
                this.element.toggleClass('fullscreen', this.isFullscreen);
                
                // Mettre à jour l'interface
                this.update();
                
                if (this.slides.length > 1) {
                    this.updatePrevNextPreviews();
                }
            });
        }
        
        /**
         * Configure la navigation au clavier
         */
        setupKeyboardNavigation() {
            $(document).on('keydown' + this.namespace, (e) => {
                // Ne réagir aux touches que si le carrousel est en plein écran
                if (!this.isFullscreen) return;
                
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        this.prevSlide();
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        this.nextSlide();
                        break;
                    case 'Escape':
                        e.preventDefault();
                        if (this.isFullscreen) this.toggleFullscreen();
                        break;
                }
            });
        }
        
        /**
         * Configure les gestionnaires de clic
         */
        setupClickHandlers() {
            // Gérer le clic en dehors du contenu principal pour quitter le plein écran
            this.element.on('click' + this.namespace, (e) => {
                // Vérifier si le clic est sur une zone interactive
                const isInteractiveElement = $(e.target).closest('.carousel-preview-button, .carousel-thumbnails, .fullscreen-btn').length > 0;
                
                // Quitter le plein écran si le clic n'est pas sur une zone interactive
                if (!isInteractiveElement && this.isFullscreen) {
                    this.toggleFullscreen();
                }
            });
        }

        /**
         * Affiche un slide spécifique
         * @param {number} index - L'index du slide à afficher
         */
        showSlide(index) {
            // Vérifier que l'index est valide
            if (index < 0 || index >= this.slides.length) return;

            // Masquer toutes les diapositives
            this.slides.removeClass('active').css('opacity', 0);
            this.element.find('.carousel-thumbnail').removeClass('active');
            
            // Mettre à jour l'index courant
            this.currentIndex = index;
            const currentSlide = $(this.slides[index]);
            
            // Afficher la diapositive actuelle
            currentSlide.addClass('active').css('opacity', 1);
            
            // Mettre à jour la miniature active
            this.element
                .find(`.carousel-thumbnail[data-index="${index}"]`)
                .addClass('active');
                
            // Mettre à jour les prévisualisations
            if (this.slides.length > 1) {
                this.updatePrevNextPreviews();
            }
            
            // Faire défiler les miniatures pour centrer la miniature active
            if (this.thumbnailsContainer && this.thumbnailsContainer.length) {
                const thumbnail = this.thumbnailsContainer.find(`.carousel-thumbnail[data-index="${index}"]`);
                if (thumbnail.length) {
                    const containerWidth = this.thumbnailsContainer.width();
                    const thumbnailLeft = thumbnail.position().left;
                    const thumbnailWidth = thumbnail.outerWidth();
                    
                    // Calculer le défilement nécessaire pour centrer la miniature
                    const scrollLeft = thumbnailLeft - (containerWidth / 2) + (thumbnailWidth / 2);
                    
                    // Animer le défilement
                    this.thumbnailsContainer.animate({
                        scrollLeft: scrollLeft
                    }, 300);
                }
            }
        }

        /**
         * Affiche le slide précédent
         */
        prevSlide() {
            const newIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            this.showSlide(newIndex);
        }

        /**
         * Affiche le slide suivant
         */
        nextSlide() {
            const newIndex = (this.currentIndex + 1) % this.slides.length;
            this.showSlide(newIndex);
        }

        /**
         * Bascule le mode plein écran
         */
        async toggleFullscreen() {
            try {
                if (!this.isFullscreen) {
                    // Entrer en mode plein écran
                    const elem = this.element[0];
                    if (elem.requestFullscreen) {
                        await elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) {
                        await elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) {
                        await elem.msRequestFullscreen();
                    }
                } else {
                    // Quitter le mode plein écran
                    if (document.exitFullscreen) {
                        await document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        await document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        await document.msExitFullscreen();
                    }
                }
            } catch (error) {
                // Gérer les erreurs de l'API plein écran
                console.warn('Fullscreen operation failed:', error);
                
                // Forcer la mise à jour de l'état si l'API échoue
                this.isFullscreen = !this.isFullscreen;
                this.element.toggleClass('fullscreen', this.isFullscreen);
                
                // Mettre à jour l'interface
                if (this.slides.length > 1) {
                    this.updatePrevNextPreviews();
                }
            }
            this.update();
        }

        /**
         * Configure les flèches de navigation
         */
        setupNavigationArrows() {
            // Créer les flèches de navigation
            const prevArrow = $('<div class="carousel-nav-arrow prev"></div>');
            const nextArrow = $('<div class="carousel-nav-arrow next"></div>');
            
            // Ajouter les flèches au conteneur
            this.container.append(prevArrow, nextArrow);
            
            // Ajouter les gestionnaires d'événements
            prevArrow.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.prevSlide();
            });
            
            nextArrow.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.nextSlide();
            });
        }

        /**
         * Met à jour la taille du carousel
         */
        updateSize() {
            const width = getSettingsValue(
                this.settings,
                'local-carousel-width',
                '100%'
            );
            
            const height = getSettingsValue(
                this.settings,
                'local-carousel-height',
                '100%'
            );
            
            applyStyle(this.element, null, 'width', width);
            applyStyle(this.element, null, 'height', height);
        }
    }

    // =====================================================
    // CLASSE PORTFOLIO COLOR PALETTE
    // =====================================================
    
    /**
     * Classe pour gérer la palette de couleurs
     */
    class PortfolioColorPalette {
        /**
         * Constructeur - Initialise la palette de couleurs
         * @param {HTMLElement} element - L'élément DOM de la palette
         */
        constructor(element) {
            this.element = $(element);
            
            // Cache des éléments fréquemment utilisés
            this.$rectangles = this.element.find('.color-rectangle');
            this.$comments = this.element.find('.color-comment');
            
            // Récupérer et fusionner les paramètres
            const localSettings = JSON.parse(this.element.attr('data-settings') || '{}');
            this.settings = $.extend(true, {}, globalSettings.palette || {}, localSettings);
            
            this.init();
        }
        
        /**
         * Initialise la palette de couleurs
         */
        init() {
            this.update();
        }
        
        /**
         * Met à jour tous les styles de la palette
         */
        update() {
            const rectangleHeight = getSettingsValue(
                this.settings,
                'local-palette-height-rectangle',
                '15px'
            );
            
            const commentPosition = getSettingsValue(
                this.settings,
                'local-palette-position-comment',
                'bottom'
            );
            
            const commentColor = getSettingsValue(
                this.settings,
                'local-palette-color-comment',
                '#2e3d38'
            );
            
            applyStyle(this.element, '.color-rectangle', 'height', rectangleHeight);
            
            this.$comments
                .removeClass('top bottom')
                .addClass(commentPosition);
            
            applyStyle(this.element, '.color-comment', 'color', commentColor);
        }
    }

    // =====================================================
    // INITIALISATION
    // =====================================================
    
    // Initialiser tous les carousels sur la page
    $('.portfolio-carousel').each(function() {
        new PortfolioCarousel(this);
    });
    
    // Initialiser toutes les palettes de couleurs sur la page
    $('.portfolio-color-palette').each(function() {
        new PortfolioColorPalette(this);
    });
});