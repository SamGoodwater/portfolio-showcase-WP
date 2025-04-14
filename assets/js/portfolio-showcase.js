jQuery(document).ready(function($) {
    class PortfolioCarousel {
        constructor(element) {
            this.element = $(element);
            this.container = this.element.find('.carousel-container');
            this.slides = this.element.find('.carousel-slide');
            this.currentIndex = 0;
            this.isFullscreen = false;
            this.thumbnailsContainer = null;
            this.previewElements = null;
            
            // Namespace unique pour les événements
            this.namespace = '.carousel' + Math.random().toString(36).substr(2, 9);
            
            this.init();
        }

        getSettings() {
            const title = this.element.find('.carousel-title');
            const description = this.element.find('.carousel-description');
            
            return {
                fullscreen: this.element.hasClass('fullscreen'),
                titlePosition: title.length ? title.attr('class').split(' ')[1] || 'top-left' : 'top-left',
                descriptionPosition: description.length ? description.attr('class').split(' ')[1] || 'bottom-left' : 'bottom-left',
                backgroundColor: this.element.css('background-color') || '#f5f5f5'
            };
        }

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
            }
            this.showSlide(0);
            this.setupFullscreen();
            this.setupKeyboardNavigation();
            this.setupClickHandlers();
        }

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

        setupSlides() {
            this.slides.each((index, slide) => {
                $(slide).attr('data-index', index);
            });

            // Ajouter le gestionnaire de clic sur les images pour le plein écran
            this.slides.on('click' + this.namespace, 'img', (e) => {
                e.stopPropagation();
                this.toggleFullscreen();
            });
        }

        setupPreviews() {
            // Ajouter les prévisualisations de l'image précédente et suivante
            if (this.slides.length > 1) {
                this.updatePrevNextPreviews();
            }
        }

        updatePrevNextPreviews() {
            // Supprimer les prévisualisations existantes
            this.element.find('.carousel-preview-container, .carousel-preview-image, .carousel-preview-button').remove();
            
            if (this.slides.length <= 1) return;
            
            // Index de l'image précédente et suivante pour le défilement infini
            const prevIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            const nextIndex = (this.currentIndex + 1) % this.slides.length;
            
            // Créer le conteneur pour la prévisualisation précédente
            const prevContainer = $('<div class="carousel-preview-container prev"></div>');
            this.container.append(prevContainer);
            
            // Créer l'image de prévisualisation précédente
            const prevSlide = $(this.slides[prevIndex]);
            const prevImg = prevSlide.find('img').attr('src');
            if (prevImg) {
                const prevPreviewImage = $('<div class="carousel-preview-image prev"></div>');
                prevPreviewImage.css('background-image', `url('${prevImg}')`);
                prevContainer.append(prevPreviewImage);
            }
            
            // Créer le bouton de prévisualisation précédente (zone de clic)
            const prevPreviewButton = $('<div class="carousel-preview-button prev"></div>');
            prevContainer.append(prevPreviewButton);
            
            // Ajouter le clic pour naviguer vers l'image précédente
            prevPreviewButton.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.prevSlide();
            });
            
            // Créer le conteneur pour la prévisualisation suivante
            const nextContainer = $('<div class="carousel-preview-container next"></div>');
            this.container.append(nextContainer);
            
            // Créer l'image de prévisualisation suivante
            const nextSlide = $(this.slides[nextIndex]);
            const nextImg = nextSlide.find('img').attr('src');
            if (nextImg) {
                const nextPreviewImage = $('<div class="carousel-preview-image next"></div>');
                nextPreviewImage.css('background-image', `url('${nextImg}')`);
                nextContainer.append(nextPreviewImage);
            }
            
            // Créer le bouton de prévisualisation suivante (zone de clic)
            const nextPreviewButton = $('<div class="carousel-preview-button next"></div>');
            nextContainer.append(nextPreviewButton);
            
            // Ajouter le clic pour naviguer vers l'image suivante
            nextPreviewButton.on('click' + this.namespace, (e) => {
                e.stopPropagation();
                this.nextSlide();
            });
            
            this.previewElements = this.element.find('.carousel-preview-container');
        }

        setupThumbnails() {
            this.thumbnailsContainer = $('<div class="carousel-thumbnails"></div>');
            
            this.slides.each((index, slide) => {
                const img = $(slide).find('img');
                if (img.length) {
                    const thumbnail = $('<div class="carousel-thumbnail"></div>')
                        .attr('data-index', index)
                        .attr('aria-label', `Go to slide ${index + 1}`);
                        
                    const thumbImg = $('<img>')
                        .attr('src', img.attr('src'))
                        .attr('alt', img.attr('alt') || `Thumbnail ${index + 1}`);
                    
                    thumbnail.append(thumbImg);
                    this.thumbnailsContainer.append(thumbnail);
                }
            });
            
            this.element.append(this.thumbnailsContainer);
            
            this.thumbnailsContainer.on('click' + this.namespace, '.carousel-thumbnail', (e) => {
                e.stopPropagation();
                const index = $(e.currentTarget).data('index');
                this.showSlide(index);
            });
        }

        setupFullscreen() {

            // Gérer les changements d'état du plein écran
            $(document).on('fullscreenchange' + this.namespace + 
                         ' webkitfullscreenchange' + this.namespace + 
                         ' mozfullscreenchange' + this.namespace + 
                         ' MSFullscreenChange' + this.namespace, () => {
                const isFullscreen = document.fullscreenElement || 
                                   document.webkitFullscreenElement || 
                                   document.mozFullScreenElement || 
                                   document.msFullscreenElement;
                
                this.isFullscreen = !!isFullscreen && isFullscreen === this.element[0];
                this.element.toggleClass('fullscreen', this.isFullscreen);
                
                // Mettre à jour les prévisualisations et les miniatures en mode plein écran
                if (this.slides.length > 1) {
                    this.updatePrevNextPreviews();
                }
            });
        }
        
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
        
        setupClickHandlers() {
            // Ajouter un gestionnaire pour quitter le mode plein écran
            // quand on clique en dehors du contenu principal
            this.element.on('click' + this.namespace, (e) => {
                // Vérifier si le clic est sur une zone interactive
                const isInteractiveElement = $(e.target).closest('.carousel-preview-button, .carousel-thumbnails, .fullscreen-btn').length > 0;
                
                // Si le clic n'est pas sur une zone interactive et que nous sommes en plein écran
                if (!isInteractiveElement && this.isFullscreen) {
                    this.toggleFullscreen();
                }
            });
        }

        showSlide(index) {
            if (index < 0 || index >= this.slides.length) return;

            // Masquer toutes les diapositives avec une transition
            this.slides.removeClass('active').css('opacity', 0);
            this.element.find('.carousel-thumbnail').removeClass('active');
            
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
                    
                    this.thumbnailsContainer.animate({
                        scrollLeft: scrollLeft
                    }, 300);
                }
            }
        }

        prevSlide() {
            const newIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
            this.showSlide(newIndex);
        }

        nextSlide() {
            const newIndex = (this.currentIndex + 1) % this.slides.length;
            this.showSlide(newIndex);
        }

        async toggleFullscreen() {
            try {
                if (!this.isFullscreen) {
                    const elem = this.element[0];
                    if (elem.requestFullscreen) {
                        await elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) {
                        await elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) {
                        await elem.msRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        await document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        await document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        await document.msExitFullscreen();
                    }
                }
            } catch (error) {
                console.warn('Fullscreen operation failed:', error);
                // Forcer la mise à jour de l'état si l'API échoue
                this.isFullscreen = !this.isFullscreen;
                this.element.toggleClass('fullscreen', this.isFullscreen);
                
                // Mettre à jour l'interface en fonction du nouvel état
                if (this.slides.length > 1) {
                    this.updatePrevNextPreviews();
                }
            }
        }
    }

    // Initialiser tous les carousels sur la page
    $('.portfolio-carousel').each(function() {
        new PortfolioCarousel(this);
    });
});