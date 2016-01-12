IrivenConfigManager
=======

>classe php de gestion des fichiers de configuration

## Les Caractéristiques
* **Leger** - 12KB environ et un seul fichier
* **Multi-driver** - Prise en charge des fichiers de configuration de type Php-Array (.php), Json(.json) et INI(.ini)
* **Intuitif** - Detection automatique du driver à utiliser, sur la base de l'extension du fichier passé en parametre
* **Facile** - Utilisation et Prise en main extrêmement simple et rapide.
* **Puissant** - Prise en charge de tout type de tableau php (simple, multidimentionnel).

## Les Methodes Publics
* **set** - $config->set($key,$value);
* **get** - $config->get($key);
* **del** - $config->del($key);

## Utilisation
* **cas 1** - Le $filename est connu et se trouve dans le dossier de stockage des fichiers de configuration($configDir)
$config= new IrivenConfigManager($filename,$configDir);
