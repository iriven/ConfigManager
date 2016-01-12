<?php
/**
 * Created by PhpStorm.
 * User: IRIVEN FRANCE
 * Date: 21/11/2015
 * Time: 10:44
 */
class IrivenConfigManager
{
    /**
    * Chemin complet du fichier de configuration
    */
    private $fileFullPath;
    /**
    * tableau multidimensionnel contenant les donnée de configuration, initialement
    * chargées depuis le fichier
    */
    private $Config = array();
    /**
    * processeurs de fichier pris en charge par l'application
    */
    private $availableDrivers = array('JSON', 'PHP','INI');
    /**
    * processeur de fichier actif
    */
    private $driver = null;

    /**
    * Dossier de stockage des fichiers de configuration
    */
    private $repository = '';// modifier ici selon la config de votre site

    /**
     * @param $filename
     * @param null $location
     */
    public function __construct( $filename, $location=null )
    {
        $numargs = func_num_args();
        try
        {
            if($numargs > 2)
                throw new Exception('SETUP ERROR: configuration manager can accept only up to 2 parameters,'.$numargs.' given!');
            $this->loadDriver($filename,$location);
            return $this;
        }
        catch(Exception $a)
        {
            die($a->getMessage());
        }
    }

    /**
     * @param $file
     * @param null $location
     * @return bool|null|string
     * @throws Exception
     */
    private function loadDriver($file,$location=null)
    {
        if(!($numargs = func_num_args()))
            return false;
        if(!is_string($file) or ($location and !is_string($location)))
            return false;
        $this->driver or $this->driver = 'PHP';
        try
        {
			 try
			{
				if($location)
                $this->repository=rtrim($this->normalize($location),DIRECTORY_SEPARATOR);
				$filename = basename($file).'.'.strtolower($this->driver);
				if(strpos(basename($file),'.')!==false)
				{
					// PHP >5.2
					$this->driver = strtoupper(pathinfo($file, PATHINFO_EXTENSION))?: strtoupper(end(explode('.', $file)));
					$filename = basename(substr($file,0,strrpos($file,'.'))).'.'.strtolower($this->driver);
				}
				if(!in_array($this->driver,$this->availableDrivers, true))
					throw new Exception('SETUP ERROR: the configuration driver "'.$this->driver.'" is not supported!');
				$this->fileFullPath = $this->normalize($this->repository.DIRECTORY_SEPARATOR.$filename);
				if(!file_exists($this->fileFullPath))
					file_put_contents($this->fileFullPath,'',LOCK_EX);
				switch($this->driver)
				{
					case 'JSON':
						$this->Config = unserialize(json_decode(file_get_contents($this->fileFullPath), true));
						break;
					case 'INI':
						$this->Config = parse_ini_file($this->fileFullPath, true);
						break;
					default:
						if(!$this->Config = include $this->fileFullPath) $this->Config = array();
						break;
				}
			}
			catch(Exception $a)
			{
				die( $a->getMessage());
			}
        }
        catch(InvalidArgumentException $b)
        {
            die( $b->getMessage());
        }
        return $this->driver;
    }

    /**
     * @param null $section
     * @param null $item
     * @return array|bool
     */
    public function get($section=null, $item=null)
    {
        if($item) $item = trim(strtolower($item));
        if($section) $section = trim(strtolower($section));
        if(!count($this->Config)) return false;
        if(func_num_args() == 0) return $this->Config;
        if((!$item and $section) or ($item and !$section))
        {
            if(!$item) $item = $section;
            if(!isset($this->Config[$item]))
            {
                if(isset($this->Config['root'][$item])) return $this->Config['root'][$item];
                return false;
            }
            return $this->Config[$item];
        }
        if(!isset($this->Config[$section][$item])) return false;
        return $this->Config[$section][$item];
    }

    /**
     * @param null $section
     * @param $item
     * @param null $value
     * @return bool
     */
    public function set($section=null,$item,$value=null)
    { ob_start();
        $numarg = func_num_args();
        $arguments=func_get_args();
        switch($numarg)
        {
            case 1:
                if(!is_array($arguments[0])) return false;
                $item=array_change_key_case($arguments[0], CASE_LOWER); $section=null; $value=null;	break;
            case 2:
                if(is_array($arguments[0])) return false;
                $_arg = strtolower(trim($arguments[0]));
                if(is_array($arguments[1])){ $section=$_arg; $item =array_change_key_case($arguments[1], CASE_LOWER);$value=null;}
                else {$item = $_arg;$value=$arguments[1];$section=null;}
                break;
            default:
                break;
        }
        $section = $section? trim(strtolower($section)) : 'root';
        if(!is_array($item))
        {
            if(!$value) return false;
            $item=trim(strtolower($item));
            if(!isset($this->Config[$section][$item]) or !is_array($this->Config[$section][$item])):
                $this->Config[$section][$item]=$value;
            else:
                if(!is_array($value)) $value = array($value);
                $this->Config[$section][$item] = array_merge($this->Config[$section][$item],$value);
            endif;
        }
        else
        {
            if($value) return false;
            $item = array_change_key_case($item, CASE_LOWER);
            $sectionsize = count($this->Config[$section]);
            $itemsize = count($item);
            if($sectionsize)
            {
                if($itemsize=='1')
                {
                    if(isset($this->Config[$section][key($item)]))
                        $this->Config[$section][key($item)] = array_merge($this->Config[$section][key($item)],$item[key($item)]);
                    else if(!is_numeric(key($item))) $this->Config[$section][key($item)]=$item[key($item)];
                }
                else $this->Config[$section] = array_merge($this->Config[$section],$item);
            }
            else $this->Config[$section] = $item;
        }
        $re = $this->Save();
        ob_end_clean();
        return $re;
    }

    /**
     * @param $section
     * @param null $item
     * @return bool
     */
    public function del($section, $item=null)
    {
        $section = trim(strtolower($section));
        if($item and strlen($item))
        {
            $item = trim(strtolower($item));
            if(!isset($this->Config[$section])) return false;
            $sectionsize=count($this->Config[$section]);
            if($sectionsize > 1){if(isset($this->Config[$section][$item])) unset($this->Config[$section][$item]);}
            else{if(isset($this->Config[$section][$item])) unset($this->Config[$section]);}
        }
        else
        {
            $item = $section;
            if(!isset($this->Config[$item]))
            {
                $rootsize = count($this->Config['root']);
                if($rootsize>1){if(isset($this->Config['root'][$item])) unset($this->Config['root'][$item]);}
                else{if(isset($this->Config['root'][$item])) unset($this->Config['root']);}
            }
            else unset($this->Config[$item]);
        }
        return $this->Save();
    }

    /**
     * @return bool
     */
    private function Save()
    {
        if( !is_writeable( $this->fileFullPath ) ) @chmod($this->fileFullPath,0775);
        $content = null;
        switch($this->driver)
        {
            case 'JSON':
                $content .= json_encode(serialize($this->Config));
                break;
            case 'INI':
                $buildSectionDatas = function ($section,$datas) use (&$buildSectionDatas)
                {
                    $Output= '';
                    is_array($datas) or $datas = array($datas);
                    foreach($datas as $groupKey => $groupValue)
                    {
                        if(is_array($groupValue))
                            $Output.= $buildSectionDatas($section[$groupKey],$groupValue);
                        else{ $Output .= $section[$groupKey].'='.(is_numeric($groupValue) ? $groupValue : '"'.$groupValue.'"').PHP_EOL;}
                    }
                    return $Output;
                };
                $content .= '; @file generated by: "'.get_class($this).'" Class'.PHP_EOL;
                $content .= '; @Last Update: '.date('Y-m-d H:i:s').PHP_EOL;
                $content .= PHP_EOL;
                foreach($this->Config as $section => $value)
                {
                    if(is_array($value)) {
                        $content .= '[' . $section . ']' . PHP_EOL;
                        $content .= $buildSectionDatas($section,$value). PHP_EOL;
                        $content .= PHP_EOL;
                    }
                    else{$content .= $section.'='.(is_numeric($value) ? $value : '"'.$value.'"').PHP_EOL;}
                }
                break;
            default:
                $content .= '<?php'.PHP_EOL;
                $content .= '/**'.PHP_EOL;
                $content .= '@file generated by: "'.get_class($this).'" Class'.PHP_EOL;
                $content .= ' @Last Update: '.date('Y-m-d H:i:s').PHP_EOL;
                $content .= '*/'.PHP_EOL;
                $content .= 'return ';
                $content .= var_export($this->Config, true) . ';';
                break;
        }
        file_put_contents($this->fileFullPath, $content, LOCK_EX);
        @chmod($this->fileFullPath,0644);
        return true;
    }

    /**
     * @param $path
     * @param null $relativeTo
     * @return string
     */
    private function normalize($path, $relativeTo = null) {
        $path = rtrim(preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
        $isAbsolute = stripos(PHP_OS, 'win')===0 ? preg_match('/^[A-Za-z]+:/', $path): !strncmp($path, DIRECTORY_SEPARATOR, 1);
        if (!$isAbsolute)
        {
            if (!$relativeTo) $relativeTo = getcwd();
            $path = $relativeTo.DIRECTORY_SEPARATOR.$path;
        }
        if (is_link($path) and ($parentPath = realpath(dirname($path))))
            return $parentPath.DIRECTORY_SEPARATOR.$path;
        if ($realpath = realpath($path))  return $realpath;
        $parts = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
        while (end($parts) !== false)
        {
            array_pop($parts);
            $attempt = stripos(PHP_OS, 'win')===0 ? implode(DIRECTORY_SEPARATOR, $parts): DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $parts);
            if ($realpaths = realpath($attempt))
            {
                $path = $realpaths.substr($path, strlen($attempt));
                break;
            }
        }
        return $path;
    }
    /**
     * fin de la classe
     */

}
