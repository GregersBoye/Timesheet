<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<!-- Minus AutoDato -->
	<title>[Ingen titel]</title>
<meta name="Generator" content="Stone's WebWriter 4" />
</head>
<body>
<form action="calculate.php" method="post" name="seddel">
<p>
Inds�t sti til ICL-fil:<br />
<input type="text" name="path" /></p>
<p>Navn:<br />
<input type="text" name="namos" />
</p>
<p>M�ned/�r:<br />
<select name="maaned">
	<option value="1">Januar </option>
	<option value="2">Februar </option>
	<option value="3">Marts</option>
	<option value="4">April </option>
	<option value="5">Maj</option>
	<option value="6">Juni</option>
	<option value="7">Juli</option>
	<option value="8">August</option>
	<option value="9">September</option>
	<option value="10">Oktober</option>
	<option value="11">November</option>
	<option value="12">December</option>
</select>
</p>
<p>�r:<br />
<select size="2" name="aar">
	<option value="2008">2008</option>
	<option value="2009">2009</option>
    <option value="2010">2010</option>
    <option value="2010">2011</option>
</select>
</p>


<input type="submit" value="Beregn!" />



</form>
<h3>TODO-liste:</h3>
<ol type="I">
	<li>G�re skemaet f�rdigt
	
<ol type="I">
	<li>Bruge beskrivelse</li>
	<li>Kun tage aktuelle m�ned/v�lge m�ned</li>
	<li>F� styr p� det sidste fra originalen</li>
</ol>


</li>

	<li>Underst�tte gentagne h�ndelser</li>
	<li>Bruge outlook-format</li>

</ol>


</body>
</html>