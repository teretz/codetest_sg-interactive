<?php
		
	//init
	$coins_bet = 0;
	$coins_won = 0;
	$player_ID = 1;
	$salt ='';
	$hash_passed='';

	//set values
	if( $_POST["coins_bet"] || $_POST["coins_won"]){
	//$hash_passed = 		$_POST['hash'];
	$coins_won = 	$_POST['coins_won'];
	$coins_bet = 	$_POST['coins_bet'];
	$player_ID = 	$_POST['player_id'];
	$salt = 	$_POST['salt'];
	$hash_passed = md5($salt);
	}
		

	
	//get salt from user by querying mysql
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "codetest";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "SELECT name, lifetime_spins, salt, credits FROM customers WHERE id=".$player_ID;
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$_t1 = $row["salt"];
			$name 	= $row["name"];
			$spins 	= $row['lifetime_spins'];
			$credits = $row['credits'];
			
			//hash salt from db
			$hash_generated = md5($_t1);				
			
			//compared both hashes
			if ($hash_generated == $hash_passed ){
				//if equal, authenicate
				$authenicate_spin = 'TRUE';
			} else {
				$authenicate_spin = 'FALSE';
			}
		}
	} else {
		echo "0 results";
	}
	
	//add one spin to user data
	if ($authenicate_spin == 'TRUE'){
		$spins = $spins + 1;
		$sql = "UPDATE customers SET lifetime_spins=".$spins." WHERE id=". $player_ID;
	}
	
	if ($conn->query($sql) === TRUE) {
		//echo "Spins updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
	
	//adjust credits to user data
	if ($authenicate_spin == 'TRUE'){
		$credits = $credits + $coins_won;
		$credits = $credits - $coins_bet;
		$sql = "UPDATE customers SET credits=".$credits." WHERE id=". $player_ID;
	}
	
	if ($conn->query($sql) === TRUE) {
		//echo "Credits updated successfully";
	} else {
		echo "Error updating record: " . $conn->error;
	}
	
	$conn->close();

	//does average return
	$average_return = $credits / $spins;
	$data = [ 'Player ID' => $player_ID, 'Name' => -1 , 'Credits' => $credits, 'Lifetime Spins ' => $spins, 'Lifetime Average Return' => $average_return];
	echo json_encode( $data );
	
	

?>


<html>
   <body>
	  <br/>&nbsp;<br/>
      <form action = "<?php $_PHP_SELF ?>" method = "POST">
         Coins Won: <input type = "text" name = "coins_won" /><br/>
         Coins Bet: <input type = "text" name = "coins_bet" /><br/>
		 Player ID: <input type = "text" name = "player_id" /><br/>
		 Salt: <input type = "text" name = "salt" /><br/>
         <input type = "submit" />
      </form>
		
	<br/>&nbsp;<<br/>
	<p> If all fields are complete, and salt matches what is in column for account in the user database, then no error msgs will appear and only a accurate updated JSON object is returned.</p>
   
   </body>
</html>
