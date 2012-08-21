<?php 

class Event 
{
	var $name;
	var $start;
	var $ending;
	
	function __construct($name, $start, $ending)
	{
		$this->name = $name;
		$this->start = mktime(substr($start, 11,2),substr($start, 14,2),0,substr($start, 5,2),substr($start,8,2), substr($start,0,4));
		$this->ending = mktime(substr($ending, 11,2),substr($ending, 14,2),0,substr($ending, 5,2),substr($ending,8,2), substr($ending,0,4));

	}
	
	function sortElementsByName($first, $second)
	{
		return strcmp(strtolower($first->name), strtolower($second->name));
	}
	
	function sortElementsByTime($first, $second)
	{
		$a = $first->start;
		$b = $second->start;
        if ($a == $bl) {
            return 0;
        }
        return ($a > $b) ? +1 : -1;

	}
	
	function getWorkDate(){
		return date("d/n 'y", $this->ending);
	}
	
	function getStartTime(){
		return date("H:i", $this->start);
	}
	
	function getEndTime(){
		return date("H:i", $this->ending);	
	}
	
	function getWorkTime($round)
	{
		$returnVal = ($this->ending-$this->start)/3600;
		if($round) $returnVal = round($returnVal, 2);
		return $returnVal;
	
	}

	function printEvent()
	{
		echo "<tr class=\"timeLine\">";
			echo "<td>".$this->getWorkDate()."</td>";
		
			echo "<td>".$this->getStartTime()."</td>";
			echo "<td>".$this->getEndTime()."</td>";
			echo "<td style=\"width:50px;\">".countDecimals($this->getWorkTime(true))."</td>";
		echo "</tr>\n";
	}
}

	
?>