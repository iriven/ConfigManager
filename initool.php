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
 
public function set($section=null,$item,$value=null)
{
	$numarg = func_num_args();
	$arg=func_get_args();
	switch($numarg)
	{
		case 1:
		if(!is_array($arg[0])) return false;
		$item=array_change_key_case($arg[0], CASE_LOWER);
		$section=null;
		$value=null;
		break;
		case 2:
		if(is_array($arg[0])) return false;
		 $_arg = strtolower(trim($arg[0]));
		if(is_array($arg[1]))
		{
			$section=$_arg; $item =array_change_key_case($arg[1], CASE_LOWER);$value=null;
		}
		else
		{$item = $_arg;$value=$arg[1];$section=null;}
		
		break;		
		default:
		break;
	}
	$section = $section? trim(strtolower($section)) : 'root';	
	if(!is_array($item))
	{
		$item=trim(strtolower($item));
		if(!isset($this->datas[$section][$item]) or !is_array($this->datas[$section][$item])):
		 		$this->datas[$section][$item]=$value;
		 else:
		 		if(!is_array($value)) $value = array($value);
			 	$this->datas[$section][$item] = array_merge($this->datas[$section][$item],$value);
		 endif;	
	}
	else
	{
		
			$sectionsize=count($this->datas[$section]);		
			if($sectionsize){
				if(sizeof($item)=='1' and array_key_exists(key($item),$this->datas[$section]))
				$this->datas[$section][key($item)] = array_merge($this->datas[$section][key($item)],$item[key($item)]);
				elseif(sizeof($item)=='1' and !is_numeric(key($item))) $this->datas[$section][key($item)]=$item[key($item)];
				else $this->datas[$section] = array_merge($this->datas[$section],$item);
				}
			else $this->datas[$section] = $item;	
	}
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
