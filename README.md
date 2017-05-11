IrivenConfigManager
=======

>IrivenConfigManager est un composant / package stand alone, developpé pour manipuler facilement 
les fichiers de configurations de  toute application PHP.

## Les Caractéristiques
* **Leger** - 12KB environ et un seul fichier
* **Multi-driver** - Prise en charge des fichiers de configuration de type **Php-Array** (.php), **Json** (.json) et **INI** (.ini)
* **Intuitif** - Detection automatique du driver à utiliser, sur la base de l'extension du fichier passé en parametre
* **Facile** - Utilisation et Prise en main extrêmement simple et rapide.
* **Puissant** - Prise en charge de tout type de tableau (simple, multidimentionnel).

## Initialisation de la classe
* **cas 1** - Le $filename est connu et se trouve dans le dossier de stockage des fichiers de configuration ($configDir)
 **$config= new Iriven\ConfigManager($filename,$configDir)**;
* **cas 2** - seul le chemin complet du fichier de configuration ($filepath) est fournit
 + **$config= new Iriven\ConfigManager($filepath)**;

## Les Methodes Publics
* **set** - $config->set($key,$value);
* **get** - $config->get($key);
* **del** - $config->del($key);

## Notes
Le type de fichier de comfiguration (php, json,ini) est automatiquement selectionné lors de l'initialisation de la classe en fonction de l'extension du fichier cible. si l'extension fu chier de configuration n'est pas renseigné, le driver par defaut sera positionné à **php**.
