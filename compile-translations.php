<?php
/**
 * Script pour compiler les fichiers de traduction PO en MO
 * 
 * Ce script doit être exécuté depuis la ligne de commande avec PHP.
 * Il utilise la fonction msgfmt de PHP pour compiler les fichiers PO en MO.
 */

// Vérifier si le script est exécuté depuis la ligne de commande
if (php_sapi_name() !== 'cli') {
    die('Ce script doit être exécuté depuis la ligne de commande.');
}

// Chemin vers le dossier des langues
$languages_dir = __DIR__ . '/languages';

// Vérifier si le dossier des langues existe
if (!is_dir($languages_dir)) {
    die("Le dossier des langues n'existe pas: $languages_dir\n");
}

// Compiler le fichier PO en MO
$po_file = $languages_dir . '/portfolio-showcase-fr_FR.po';
$mo_file = $languages_dir . '/portfolio-showcase-fr_FR.mo';

if (!file_exists($po_file)) {
    die("Le fichier PO n'existe pas: $po_file\n");
}

// Compiler le fichier PO en MO
$command = "msgfmt -o $mo_file $po_file";
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "Fichier MO créé avec succès: $mo_file\n";
} else {
    echo "Erreur lors de la compilation du fichier PO en MO.\n";
    echo "Assurez-vous que la commande 'msgfmt' est disponible sur votre système.\n";
    echo "Vous pouvez installer gettext pour obtenir cette commande.\n";
} 