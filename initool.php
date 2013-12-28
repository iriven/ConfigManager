<?php
class initool{
private $inifile;
private $datas = array();
const sep = ':';
 
public function __construct($file)
{
    $this->inifile = $file;
    if(strtolower(pathinfo($this->inifile, PATHINFO_EXTENSION)) !=='ini')
    {
    $this->inifile = pathinfo($this->inifile, PATHINFO_DIRNAME).
                    DIRECTORY_SEPARATOR.
                     pathinfo($this->inifile, PATHINFO_FILENAME).
                     '.ini';
    }
    if(!file_exists($this->inifile)) touch($this->inifile);
     $this->datas=$this->parse($this->inifile);
}   
 
 
     
public function get($section=null, $item=null)
{
    if($item) $item = trim(strtolower($item));
    if($section) $section = trim(strtolower($section));
    if(!count($this->datas)) return false;
    if(func_num_args() == 0) return $this->datas; 
    if((!$item and $section) or ($item and !$section))
    {
        if(!$item) $item = $section;
        if(!isset($this->datas[$item]))
        {
            if(isset($this->datas['root'][$item])) return $this->datas['root'][$item];
            return false;
        }
        return $this->datas[$item];
    }
    if(!isset($this->datas[$section][$item])) return false;
    return $this->datas[$section][$item];
}
 
public function set($item=array(),$section=null)
{
    if($item) $item=array_change_key_case($item, CASE_LOWER);
    if($section) $section = trim(strtolower($section));
    if(!is_array($item)):
    if(!$section and !array_key_exists($item,$this->datas))$this->datas[$item] = array();
    else
    if(!array_key_exists($item,$this->datas[$section])) $this->datas[$section][$item] = array();
    endif;
foreach($item as $k=>$v):
    if(!$section)$section = 'root';
     if (is_array($v)):
      $this->datas[$section][$k] = $this>set($v,$this->datas[$section][$k]);
    else:
      $this->datas[$section][$k] = $v;
    endif;
  endforeach;
    $this->save();
}
 
 
    public function del($section, $item=null){
        $section = trim(strtolower($section));
        if($item) $item = trim(strtolower($item));
        if(!$item or !strlen($item))
        {
            $item = $section;
            if(!isset($this->datas[$item]))
            {
                if(isset($this->datas['root'][$item])) unset($this->datas['root'][$item]);
            }
            else unset($this->datas[$item]);
        }
    else
        {
            $itemcount=count($this->datas[$section]);        
            if($itemcount >'1')
            {
                if(array_key_exists($item,$this->datas[$section])) unset($this->datas[$section][$item]);
            }
            else
            {
                if(array_key_exists($item,$this->datas[$section])) unset($this->datas[$section]);
            }
        }
        $this->save();
        }
/**
 *
 * @param string $inifile
 * @return array
 */
private function parse($inifile)
{
    $inidatas = parse_ini_file($inifile, true);
    $output = array();
    foreach($inidatas as $namespace => $properties)
    {
        list($name, $extends) = explode(sep, $namespace);
        $name = trim($name);
        $extends = trim($extends);
        // create namespace if necessary
        if(!isset($output[$name])) $output[$name] = array();
        // inherit base namespace
        if(isset($inidatas[$extends]))
        {
            foreach($inidatas[$extends] as $prop => $val)
                $output[$name][$prop] = $val;
        }
        // overwrite / set current namespace values
        foreach($properties as $prop => $val)
        $output[$name][$prop] = $val;
    }
    return $output;
}
 
 
/**
 * @transform an array of datas into the ini file datas format.
 * @param array $datas
 * @return string
 */
private function prepare($datas=array())
{
    $data='';
    foreach($datas as $section => $groupe)
    {
        if(!is_array($groupe)) $data .= $section.'='.(is_numeric($groupe) ? $groupe : '"'.$groupe.'"').PHP_EOL;
        else
        { 
            $data.='['.$section.']'.PHP_EOL; // \n est une entrée à la ligne
            $data.= $this->prepare($groupe);
        }
    }
    return $data;
}
 
private function save()
{   
    $content = $this->prepare($this->datas);
    if(false===file_put_contents($this->inifile, $content,LOCK_EX))
        throw new Exception('Impossible d\'écrire dans ce fichier');    
}
/* fin de la classe*/      
} 
