<?php
	$servername= "sci-mysql";
	$username = "coa123wuser";
	$password = "grt64dkh!@2FD";
	$dbname = "coa123wdb"; // Server connection
	
	$monthFrom = trim($_GET["monthFrom"]);
	$monthTo = trim($_GET["monthTo"]);
	$size = trim($_GET["size"]);
	$grade = trim($_GET["grade"]); // Getting values from wedding.php
	$addDate = false;
	
	// Function used to get all the dates in range of the inputted dates
	function getDateArray($first, $last, $step = '+1 day', $ouput = 'Y-m-d') {
		$arrDates = array();
		$dateFrom = strtotime($first);
		$dateTo = strtotime($last);
		
		while($dateFrom <= $dateTo) {
			$arrDates[] = date($ouput, $dateFrom);
			$dateFrom = strtotime($step, $dateFrom);
		}
		return $arrDates;
	} 
	
	// Creating a connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}

	// First sql statement 
	$sqlCode="SELECT venue.venue_id, name, capacity, licensed, cost, weekday_price, weekend_price, COUNT(booking_date) AS count
			FROM venue
			INNER JOIN venue_booking ON venue.venue_id = venue_booking.venue_id
			INNER JOIN catering ON venue.venue_id = catering.venue_id
			WHERE capacity >= " .$size. " AND grade = " .$grade. "
			GROUP BY venue.venue_id, name, capacity, licensed, cost, weekday_price, weekend_price;";
	
	//echo $sqlCode; // Testing SQL code
	
	// Creating the first SQL response array
	$firstResult = mysqli_query($conn, $sqlCode); 

	// Storing the range of dates into an array
	$rangeOfDates = array();
	$rangeOfDates = getDateArray($monthFrom, $monthTo);
	$outputData = array();
	
	// Comparing dates
	while ($row = mysqli_fetch_array($firstResult)) {
		
		// Making sure there is a response from the first SQL statement
	    if ($row > 0) {

			// Looping through all the dates between the selected dates
			for ($x = 0; $x < sizeOf($rangeOfDates); $x++) {
				
				// Second sql statement 
				$sqlCode2="SELECT venue.venue_id, booking_date FROM venue INNER JOIN venue_booking ON venue.venue_id = venue_booking.venue_id 
					WHERE booking_date BETWEEN '" .$monthFrom. "' AND '" .$monthTo. "';";
				$secResult = mysqli_query($conn, $sqlCode2);
				$secOutput = array();
				
				//echo $sqlCode2; // Testing SQL code
				
				// Looping through the booked dates
				while ($secRow = mysqli_fetch_array($secResult)) {		
					
					// Making sure both venue names are equal AND the booked date and range date are different
					if ($secRow[0] == $row[0]) { 
						if ($rangeOfDates[$x] != $secRow[1]) {
							$addDate = true;
						} else {
							$addDate = false;
							break;
						}
					}
				}
				if ($addDate == true) {
					// Adding the response we want to the output array
					$newRowArr = array('venue_id' => $row[0], 'name' => $row[1], 'capacity' => $row[2], 'licensed' => $row[3], 'cost' => $row[4], 'weekday_price' => $row[5],
						'weekend_price' => $row[6], 'count' => $row[7], 'booking_date' => $rangeOfDates[$x]);
					$outputData[] = $newRowArr;
				}
			}
		}
		
		
	}
	echo json_encode($outputData);
?>
