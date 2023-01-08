<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Mario Portillo Hernaiz">
    <title>Wedding Venue</title>
	
	<!-- BOOSTRAP CSS LINK-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	
	<!-- BOOSTRAP JS LINK-->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	
	<!-- MY CSS CODE -->
	<link href="main.css" rel="stylesheet">
	
	<!-- JQUERY LIBRARY -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
		// Following function gets all the data we need to output
		$(document).ready(function () {
			$("#booking").submit(function () {
			
				let monthFrom = $("#monthFrom").val(); //Gets an input
				let monthTo = $("#monthTo").val();
				let partySize = $("#partySize").val();
				let gradeIn = $("#grade").val();
				var lastName = "";
                $.ajax({
                    url: "booking.php",
                    type: "GET", //GET shows data in the url, POST doesnt
					data: {monthFrom:monthFrom, monthTo:monthTo, size:partySize, grade:gradeIn},
					
                    success: function (response) {
						let length = response.length;
						let returnHtml;
						let returnDates;
						
						if (length > 0) {
							returnHtml = '<div class="container"><div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">';
							
							for (let i = 0; i < length; i++) {
								
								returnDates = "<table style='border: 1px solid black; width:100%;'><tr style='text-align:center'><th>Dates</th><th>Day of the Week</th><th>Cost</th></tr>";
								let venue_id = response[i].venue_id;
								let name = response[i].name;
								let capacity = response[i].capacity;
								let licensed = response[i].licensed;
								if (licensed == 1) {
									licensed = "Yes";
								} else {
									licensed = "No";
								}
								let cost = response[i].cost;
								let week = response[i].weekday_price;
								let weekend = response[i].weekend_price;
								let count = response[i].count;
								
								let booked = response[i].booking_date;
								
								if (lastName != name) {
									lastName = name;
									
									returnHtml += '<div class="col"><div class="card shadow-sm">';
									returnHtml += '<img class="venueImg" src="img'+venue_id+'.jpg">';
									returnHtml += '<div class="card-body">';
									returnHtml += '<h4 class="card-text" style="text-align:center">'+ name +'</h4>'+
										'<p class="card-text" style="text-align:center">Capacity: '+ capacity +'</p>'+
										'<p class="card-text" style="text-align:center">Licensed: '+ licensed +'</p>'+
										'<p class="card-text" style="text-align:center">Catering Cost: £'+ cost +' pp</p>'+
										'<p class="card-text" style="text-align:center">Week-day Price: £'+ week +'</p>'+
										'<p class="card-text" style="text-align:center">Week-end Price: £'+ weekend +'</p>'+
										'<p class="card-text" style="text-align:center">Number of times previously booked: '+ count +'</p>'+
										'<h5 class="card-text" style="text-align:center">Available days to book:</h5>';
										
									for (let j = 0; j < length; j++) {
										let venue_id2 = response[j].venue_id;
										let booked = response[j].booking_date;
										let totalPrice = 0;
										
										const d = new Date(booked);
										let day = d.getDay();
										let dayOfWeek;
										if (day == 0) {
											dayOfWeek = "Sunday";
											totalPrice = (partySize*cost) + parseInt(weekend);
										} else if (day == 1) {
											dayOfWeek = "Monday";
											totalPrice = (partySize*cost) + parseInt(week);
										} else if (day == 2) {
											dayOfWeek = "Tuesday";
											totalPrice = (partySize*cost) + parseInt(week);
										} else if (day == 3) {
											dayOfWeek = "Wednesday";
											totalPrice = (partySize*cost) + parseInt(week);
										} else if (day == 4) {
											dayOfWeek = "Thursday";
											totalPrice = (partySize*cost) + parseInt(week);
										} else if (day == 5) {
											dayOfWeek = "Friday";
											totalPrice = (partySize*cost) + parseInt(week);
										} else if (day == 6) {
											dayOfWeek = "Satutday";
											totalPrice = (partySize*cost) + parseInt(weekend);
										}
										if (venue_id2 == venue_id) {
											returnDates += "<tr style='text-align:center'>" +
												"<td>" + booked + "</td>" +
												"<td>" + dayOfWeek + "</td>" +
												"<td> £" + totalPrice + "</td>" +
												"</tr>";
										}
									}
									returnDates += "</table>";
									returnHtml += returnDates + '</div></div></div>';
								}
							}
							returnHtml += "</div></div>";
							
						} else {
							returnHtml = "<h3>Sorry, we weren't able to find any venue</h3>";
						}
                        $("#sqlResponse").html(returnHtml);
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.status + ': ' + xhr.statusText);
                    },
                    dataType: "json"});
			});
		});
		
		// Following functions prevents the user to select a past date
		$(function(){
			var dtToday = new Date();
			
			var month = dtToday.getMonth() + 1;
			var day = dtToday.getDate();
			var year = dtToday.getFullYear();
			if(month < 10) {
				month = '0' + month.toString();
			}
			if(day < 10) {
				day = '0' + day.toString();
			}
			
			var todaysDate = year + '-' + month + '-' + day;
			$('#monthFrom').attr('min', todaysDate);
			$('#monthTo').attr('min', todaysDate);
		});
		function update() {
			let selectedDate = document.getElementById("monthFrom").value;
			const nextDate = new Date(new Date(selectedDate).setDate(new Date(selectedDate).getDate() + 1)).toISOString().split("T")[0];
			$('#monthTo').attr('min', nextDate);
		}
	</script>	
</head>
<body class="bg-light">
	<!-- HEADER -->
	<div id="headerW" class="fixed-top ">
		<header class="d-flex justify-content-center py-3">
		<a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
			<img class="iconImg" src="icon.png">
			<span class="fs-3">Wedding Venues</span>
		</a>
		<ul class="nav nav-pills">
			<li class="nav-item"><a href="#" class="nav-link active bg-light text-dark"" aria-current="page">Home</a></li>
			<li class="nav-item"><a href="#booking" class="nav-link text-white">Venue Booking</a></li>
		</ul>
		</header>
	</div>
	<!-- CAROUSEL -->
	<div id="MyCarousel" class="carousel slide" data-bs-ride="carousel">
		<div class="carousel-indicators">
		  <button type="button" data-bs-target="#MyCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
		  <button type="button" data-bs-target="#MyCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
		  <button type="button" data-bs-target="#MyCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
		</div>
		
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="bd-placeholder-img" src="mainOne.jpg">
				<div class="container">
					<div class="carousel-caption text-dark d-none d-md-block" style="font-family: 'Century Gothic'">
						<h1>For your special day...</h1>
						<p>Search, Plan and Book the most beautiful locations for your special day.</p>
					</div>
				</div>
			</div>
			<div class="carousel-item">
				<img class="bd-placeholder-img" src="mainTwo.jpg">				
			</div>
			<div class="carousel-item">
				<img class="bd-placeholder-img" src="mainThree.jpg">			
			</div>
		</div>
		<button class="carousel-control-prev" type="button" data-bs-target="#MyCarousel" data-bs-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Previous</span>
		</button>
		<button class="carousel-control-next" type="button" data-bs-target="#MyCarousel" data-bs-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="visually-hidden">Next</span>
		</button>		
	</div>
<main>
	<!-- DESCRIPTION -->
	<div class="container marketing">
		<hr class="featurette-divider">
		<div class="row featurette">
			<div class="col-md-7">
				<h2 class="featurette-heading">"Impressed at the world wide locations this website gives option to."</h2>
				<p class="lead">- Christopher Columbus</p>
			</div>
			<div class="col-md-5">
				<img class="sideImg" src="sideOne.jpg">
			</div>
		</div>
	
		<hr class="featurette-divider">
		<div class="row featurette">
			<div class="col-md-5">
				<img class="sideImg" src="sideTwo.jpg">
			</div>
			<div class="col-md-7">
				<h2 class="featurette-heading">"The best website to find not only the best venues, but the best catering."</h2>
				<p class="lead">- Gordon Ramsay</p>
			</div>
		</div>
		<hr class="featurette-divider">
	
	<!-- FUNCTIONALITY -->
		<form id="booking" onSubmit="return false"> <!-- on return: return false -->
		<div class="row text-center m-4">
			<h2 id="bookVenue">Book a Venue</h2>
		</div>
		
		<div class="row">
			<!-- MONTH INPUT -->
			<div class="col-lg-4">
				<img class="bookingImg mx-auto" src="calendar.png">			
				<label for="monthFrom">Date From: </label>
				<input name="monthFrom" type="date" class="larger" id="monthFrom" size="11"  onchange="update()" required />
				<br>
				<label for="monthTo">To: </label>
				<input name="monthTo" type="date" class="larger" id="monthTo" size="11" required />
			</div>
			<!-- PARTY SIZE INPUT -->
			<div class="col-lg-4">
				<img class="bookingImg mx-auto" src="person.png">			
				<label for="partySize">Venue Capacity: </label></td>
				<input name="partySize" type="range" class="larger" id="partySize" value="50" step="10" min="50" max="1000" size="11" oninput="outputRange.value=partySize.value" required /> <!--<center>-->
				<br>
				<output name="outputRange" id="outputRange" for="partySize"></output>
			</div>
			<!-- GRADE INPUT -->
			<div class="col-lg-4">
				<img class="bookingImg mx-auto" src="grade.png">			
				<label for="grade">Choose grade:</label>
				<select name="grade" class="larger" id="grade" value="1" max="5" min="1" required >
					<option value="1">1 star</option>
					<option value="2">2 stars</option>
					<option value="3">3 stars</option>
					<option value="4">4 stars</option>
					<option value="5">5 stars</option>
				</select>
			</div>
		</div>
		
		<!-- SUBMIT BUTTON -->
		<div class="row m-5">
			<input type="submit" name="submit" id="submit" value="Submit" class="larger text-white btn btn-secondary"/>
		</div>
		</form>
		
		<!-- OUTPUT -->
		<div id="sqlResponse"></div>
	</div>
	<br>
	<div class="b-divider"></div>
	<footer class="d-flex justify-content-center align-items-center py-3">
		<div class="d-flex align-items-center">
		  <a href="/" class="mb-3 me-2 mb-md-0 text-muted text-decoration-none lh-1">
			<img class="iconImg" src="icon.png">
		  </a>
		  <span class="text-muted text-center">Contact us to book:<br>Phone: (+44)783828292 Email: wedding@askus.co.uk</br></span>
		</div>
	</footer>
</main>
</body>
</html>
