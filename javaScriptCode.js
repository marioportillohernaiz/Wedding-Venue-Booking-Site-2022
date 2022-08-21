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
