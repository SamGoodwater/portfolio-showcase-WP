jQuery(document).ready(function($) {
    // Carousel Image Management
    var carouselFrame;
    
    // Initialiser le tri par glisser-déposer
    $('#carousel-image-list').sortable({
        items: '.carousel-image-item',
        cursor: 'move',
        opacity: 0.8,
        placeholder: 'ui-sortable-placeholder',
        handle: '.move-handle',
        start: function(event, ui) {
            ui.placeholder.height(ui.item.height());
            ui.item.addClass('dragging');
        },
        stop: function(event, ui) {
            ui.item.removeClass('dragging');
            reindexImages();
            // Annoncer le changement pour les lecteurs d'écran
            const newPosition = ui.item.index() + 1;
            const totalItems = $('#carousel-image-list .carousel-image-item').length;
            announceToScreenReader(`Image déplacée à la position ${newPosition} sur ${totalItems}`);
        },
        update: function(event, ui) {
            reindexImages();
        }
    }).disableSelection();
    
    // Améliorer l'accessibilité des poignées de déplacement
    $('.move-handle').attr({
        'role': 'button',
        'aria-label': 'Déplacer l\'image',
        'tabindex': '0'
    }).on('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            $(this).closest('.carousel-image-item').trigger('mousedown');
        }
    });
    
    $('#add-carousel-images').on('click', function(e) {
        e.preventDefault();
        
        if (carouselFrame) {
            carouselFrame.open();
            return;
        }
        
        carouselFrame = wp.media({
            title: 'Select Carousel Images',
            button: {
                text: 'Add to Carousel'
            },
            multiple: true
        });
        
        carouselFrame.on('select', function() {
            var selection = carouselFrame.state().get('selection');
            var imageList = $('#carousel-image-list');
            var currentIndex = imageList.children().length;
            
            selection.map(function(attachment) {
                var image = {
                    id: attachment.id,
                    url: attachment.url,
                    title: '',
                    description: ''
                };
                
                // Créer l'élément HTML pour l'image
                var imageItem = $('<div class="carousel-image-item" data-id="' + image.id + '">' +
                    '<span title="Déplacer l\'image" class="move-handle">⋮⋮</span>' +
                    '<button title="Supprimer l\'image" type="button" class="remove-image">×</button>' +
                    '<img src="' + image.url + '" alt="">' +
                    '<div class="image-details">' +
                    '<input type="text" name="carousel_images[' + currentIndex + '][title]" value="" placeholder="Image title">' +
                    '<textarea name="carousel_images[' + currentIndex + '][description]" placeholder="Image description"></textarea>' +
                    '</div>' +
                    '<input type="hidden" name="carousel_images[' + currentIndex + '][id]" value="' + image.id + '">' +
                    '<input type="hidden" name="carousel_images[' + currentIndex + '][url]" value="' + image.url + '">' +
                    '</div>');
                
                imageList.append(imageItem);
                currentIndex++;
            });
        });
        
        carouselFrame.open();
    });
    
    // Améliorer la suppression avec confirmation
    $(document).on('click', '.remove-image', function() {
        const $item = $(this).closest('.carousel-image-item');
        const imageTitle = $item.find('input[name^="carousel_images"][name$="[title]"]').val() || 'Image sans titre';
        
        if (confirm(`Êtes-vous sûr de vouloir supprimer "${imageTitle}" ?`)) {
            $item.fadeOut(300, function() {
                $(this).remove();
                reindexImages();
                announceToScreenReader('Image supprimée');
            });
        }
    });
    
    // Fonction pour annoncer les changements aux lecteurs d'écran
    function announceToScreenReader(message) {
        const $announcement = $('<div class="screen-reader-text" aria-live="polite"></div>');
        $('body').append($announcement);
        $announcement.text(message);
        setTimeout(function() {
            $announcement.remove();
        }, 1000);
    }
    
    // Fonction optimisée pour réindexer les images
    function reindexImages() {
        const $items = $('#carousel-image-list .carousel-image-item');
        const fields = ['title', 'description', 'id', 'url'];
        
        $items.each(function(index) {
            const $item = $(this);
            fields.forEach(field => {
                $item.find(`input[name^="carousel_images"][name$="[${field}]"], 
                          textarea[name^="carousel_images"][name$="[${field}]"]`)
                    .attr('name', `carousel_images[${index}][${field}]`);
            });
        });
    }
    
    // Color Palette Management
    $('#add-color').on('click', function(e) {
        e.preventDefault();
        
        // Créer un nouvel élément de couleur
        var colorItem = $('<div class="color-item">' +
            '<input type="color" name="color_palette[colors][]" value="#000000" data-alpha="true">' +
            '<input type="text" name="color_palette[comments][]" placeholder="Color comment">' +
            '<button type="button" class="remove-color">×</button>' +
            '</div>');
        
        $('#color-palette-list').append(colorItem);
        
        // Initialiser le color picker avec support de la transparence
        initializeColorPicker(colorItem.find('input[type="color"]'));
        
    });
    
    $(document).on('click', '.remove-color', function() {
        $(this).closest('.color-item').remove();
    });
    
    // Initialiser les color pickers
    function initializeColorPicker(colorInput) {
        if (colorInput.length) {
            // Ajouter l'attribut data-alpha pour activer la transparence
            colorInput.attr('data-alpha', 'true');
            
            // Ajouter un gestionnaire d'événements pour formater la couleur correctement
            colorInput.on('change', function() {
                var color = $(this).val();
                // Si la couleur est au format rgba, la conserver telle quelle
                if (color.indexOf('rgba') === 0) {
                    $(this).val(color);
                }
            });
        }
    }
    
    // Initialiser tous les color pickers existants
    $('.color-item input[type="color"]').each(function() {
        initializeColorPicker($(this));
    });
    
    // Ajouter un gestionnaire d'événements pour le formulaire
    $('form#post').on('submit', function() {
        
        // Vérifier que les couleurs sont correctement définies
        var colorData = [];
        $('#color-palette-list .color-item').each(function(index) {
            var colorInput = $(this).find('input[type="color"]');
            var commentInput = $(this).find('input[type="text"]');
            
            colorData.push({
                color: colorInput.val(),
                comment: commentInput.val()
            });
            
        });
        
        // Créer un champ caché pour stocker les données de couleur
        // Toujours ajouter le champ, même si la palette est vide
        $('<input type="hidden" name="color_palette_data" value="' + JSON.stringify(colorData) + '">').appendTo('form#post');
    });
    
    // Mettre à jour l'affichage de la valeur d'opacité
    $('input[name="carousel_settings[background_opacity]"]').on('input', function() {
        $(this).siblings('.opacity-value').text($(this).val() + '%');
    });

    // Validate size fields in real-time
    $('.portfolio-showcase-size-field').on('input', function() {
        const value = $(this).val();
        const pattern = /^(\d+(?:\.\d+)?)(px|%|em|rem|vh|vw)$/;
        
        if (value && !pattern.test(value)) {
            $(this).addClass('error');
            $(this).next('.description').addClass('error');
        } else {
            $(this).removeClass('error');
            $(this).next('.description').removeClass('error');
        }
    });

    // Validate size fields on form submission
    $('form').on('submit', function(e) {
        let hasError = false;
        $('.portfolio-showcase-size-field').each(function() {
            const value = $(this).val();
            const pattern = /^(\d+(?:\.\d+)?)(px|%|em|rem|vh|vw)$/;
            
            if (value && !pattern.test(value)) {
                $(this).addClass('error');
                $(this).next('.description').addClass('error');
                hasError = true;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Please enter valid size values (e.g. 100%, 500px, 20rem)');
        }
    });
}); 