jQuery(document).ready(function($) {
    // Carousel Image Management
    var carouselFrame;
    
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
                    '<img src="' + image.url + '" alt="">' +
                    '<div class="image-details">' +
                    '<input type="text" name="carousel_images[' + currentIndex + '][title]" value="" placeholder="Image title">' +
                    '<textarea name="carousel_images[' + currentIndex + '][description]" placeholder="Image description"></textarea>' +
                    '</div>' +
                    '<input type="hidden" name="carousel_images[' + currentIndex + '][id]" value="' + image.id + '">' +
                    '<input type="hidden" name="carousel_images[' + currentIndex + '][url]" value="' + image.url + '">' +
                    '<button type="button" class="remove-image">×</button>' +
                    '</div>');
                
                imageList.append(imageItem);
                currentIndex++;
            });
        });
        
        carouselFrame.open();
    });
    
    $(document).on('click', '.remove-image', function() {
        $(this).closest('.carousel-image-item').remove();
        // Réindexer les éléments restants
        reindexImages();
    });
    
    // Fonction pour réindexer les images
    function reindexImages() {
        $('#carousel-image-list .carousel-image-item').each(function(index) {
            $(this).find('input[name^="carousel_images"][name$="[title]"]').attr('name', 'carousel_images[' + index + '][title]');
            $(this).find('textarea[name^="carousel_images"][name$="[description]"]').attr('name', 'carousel_images[' + index + '][description]');
            $(this).find('input[name^="carousel_images"][name$="[id]"]').attr('name', 'carousel_images[' + index + '][id]');
            $(this).find('input[name^="carousel_images"][name$="[url]"]').attr('name', 'carousel_images[' + index + '][url]');
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
}); 