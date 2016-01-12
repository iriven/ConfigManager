IrivenConfigManager
=======

>classe php de gestion des fichiers de configuration

## type d'extension prises en charge
* .php
* .json
* .ini

## Les Caractéristiques
* **Leger** - 12KB environ et un seul fichier
* **Intuitif** - Detection automatique du driver à utiliser, sur la base de l'extension du fichier passé en parametre
* **Facile** - Utilisation et Prise en main extrêmement simple et rapide.
* **Puissant** - Prise en charge de tout type de tableau php (simple, multidimentionnel).
* 
## Les Methodes Publics

## Utilisation
* cas 1
**le $filename est connu et se trouve dans le dossier de stockage des fichiers de configuration($configDir)
$config= new IrivenConfigManager($filename,$configDir);
