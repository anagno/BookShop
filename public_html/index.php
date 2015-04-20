<?php

//http://php.about.com/od/phpwithmysql/ss/php_search_3.htm
echo "<h2>Search Results:</h2><p>";

//If they did not enter a search term we give them an error
if ($find == "")
{
echo "<p>You forgot to enter the name of the book!!!";
exit;
}

// Otherwise we connect to our Database
mysql_connect("localhost", "tme119", "tme119_password") or die(mysql_error());
mysql_select_db("tme119") or die(mysql_error());

// We perform a bit of filtering
$find = strtoupper($find);
$find = strip_tags($find);
$find = trim ($find);

//Now we search for our search term, in the field the user specified
$iname = mysql_query("SELECT * FROM table_book WHERE books LIKE '%$find%'");

//And we display the results
while($result = mysql_fetch_array( $iname ))
{
echo $result['books'];
echo " ";
echo $result['authors'];
echo "<br>";
echo $result['editions'];
echo "<br>";
echo "<br>";
}

//This counts the number or results - and if there wasn't any it gives them a little message explaining that
$anymatches=mysql_num_rows($iname);
if ($anymatches == 0)
{
echo "Sorry, but we can not find an entry to match your query...<br><br>";
}

//And we remind them what they searched for
echo "<b>Searched For:</b> " .$find;
//}
?> 

<!Doctype html>

<html>
<head>

<title>Δημοτική Βιβλιοθήκη - Δήμος Διονύσου</title>
<meta charset=UTF-8>
<link href="css/style.css" rel="stylesheet" type="text/css">


<script type="text/javascript">
function bigImg(x) {
    x.style.height = "160px";
    x.style.width = "150px";
}

function normalImg(x) {
    x.style.height = "150px";
    x.style.width = "140px";
}
</script>


</head>


<body onload="startTime()">

	<div id="container">

	<header>
	<div id="header-top1">

<p id="dateandtime"></p>  <!-- Εδώ έχουμε το σενάριο javascript για την ώρα. χρησιμοποιούμε κάποιες συναρτήσεις που ήδη υπάρχουν για την ώρα-->
		<script type="text/javascript">
function startTime() {
    var today=new Date();
    var h=today.getHours(); /*Με αυτή τη συνάρτηση μπορούμε να πάρουμε την ώρα, αντίστοιχα τα λεπτά και τα δευτερόλεπτα*/
    var m=today.getMinutes();
    var s=today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt').innerHTML = h+":"+m+":"+s; /*φτιάχνουμε το πως θέλουμε να φαίνεται η ώρα*/
    var t = setTimeout(function(){startTime()},500);

	var d=new Date(); /*μεταβλητές για να πάρουμε σε νούμερα το χρόνο, το μήνα και την ημέρα*/
	var d1=new Date();
	var d2=new Date();
	m=d1.getMonth();
	n=m+1; /*Επειδή ο μήνας μετριέται από το 0 μέχρι το 11, εμείς για να φαίνεται σωστά στους ανθρώπους πρέπει να προσθέσουμε 1*/
	document.getElementById("dateandtime").innerHTML=d2.getDate()+"/"+n+"/"+d.getFullYear();
}

function checkTime(i) {
    if (i<10) {i = "0" + i};  // βάζει το 0 μπροστά από νούμερα < 10
    return i;
}


	</script>
		<div id="txt"></div>


	</div>
	<div id="header-top">

		<h1>Δημοτική Βιβλιοθήκη</h1>
	</div>

<hr>
	<div id="header-bottom">
<div align="right">
<a href="/public_html/login.php">log in</a></div>
		<h2>Δήμος Διονύσου</h2>

	</div>

	<nav class="newcolor"> <!--Ορίζουμε το καινούργιο section και σαν back-ground color έχει τη μορφοποίηση του newcolor-->

		<ul> <!--Τα βάζουμε σε λίστα την οποία μορφοποιήσαμε στο css να τα βγάζει σε μία γραμμή. -->
		        <li><strong>Αρχική Σελίδα</strong></li>
		        <li>| <strong>Αναζήτηση</strong></li>
		        <li> : <form  method="post" action="<?=$PHP_SELF?>">       
      <input type="text" class="searchkey" name="search" size="18"/> 
	<input type="image" name="submit" class="ok" alt="Ok"
      src="img/search.png" />   
     
      </form></li>
                </ul>

	</nav>  
<hr>
	</header>

	<section>

<form>
<table width='100%'> <!--Βάζουμε πίνακα ο οποίος πιάνει το 100% του κειμένου που έχουμε ορίσει παραπάνω.-->
				<tr>
			    	<td>

				<p><div id="description">Καλώς ορίσατε στη νέα δημοτική βιβλιοθήκη του δήμου Διονύσου<br/>
<br>
Με τη διείσδυση των νέων τεχνολογιών στην καθημερινότητα, οι άνθρωποι χρησιμοποιούν όλο και περισσότερο το διαδίκτυο για την πραγματοποίηση απλών καθημαρινών διαδικασιών. Γιατί λοιπόν να μην χρησιμοποιούμε το διαδίκτυο και για την ενοικίαση βιβλίων;
</br>
<br>
Τώρα πλέον, μπορείτε να επισκέπτεστε τη βιβλιοθήκη του δήμου μας από το σπίτι σας, με τις πιτζάμες σας και μόνο με λίγα κλικ στον υπολογιστή σας.</br>
<br>
Για τη δική σας εξυπηρέτηση, μπορείτε να βλέπετε τα βιβλία που είναι διαθέσιμα στη βιβλιοθήκη μας και ως εγγεγραμμένοι χρήστες του ιστότοπού μας, μπορείτε να νοικιάσετε τα βιβλία που επιθυμείτε και θα σας αποσταλλούν στο σπίτι σας μέσα άμεσα.
</br>
<hr>

</div>
</form>

	</section>

</body>

<hr>
<footer>		Copyright &copy; tme119.anagno.me - 2015</footer>

</html>
