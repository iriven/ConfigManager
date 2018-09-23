Iriven PHP ConfigManager
=======
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)
[![Build Status](https://travis-ci.org/iriven/ConfigManager.svg?branch=master)](https://travis-ci.org/iriven/ConfigManager)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/iriven/ConfigManager/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

>Iriven Php ConfigManager est un composant / package stand alone, developpé pour manipuler facilement 
les fichiers de configurations de  toute application PHP.

## Les Caractéristiques
* **Leger** - 12KB environ et un seul fichier
* **Multi-driver** - Prise en charge des fichiers de configuration de type **Php-Array** (.php), **Json** (.json), **INI** (.ini) et **YAML** (.yml)
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

## Donation

If this project help you reduce time to develop, you can give me a cup of coffee :)

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XDCFPNTKUC4TU)
