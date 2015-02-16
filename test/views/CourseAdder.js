var courseCount = 0
$("#results").parent().hide()

$("#courseSelector").on("change","select", function(){
	if( $(this).attr("class") == "subjectListSelect" ) {
		var subject = $(this).val()
		var id = $(this).attr("id")
		var idNumber = id.substr(id.length-1,id.length-1) //start,end. Returns last character
		//console.log(idNumber)

		//Send subject selected, return list of courses associated with that subject
		$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getCourses.php',
			'data' : { 'subject' : subject } }
			).done( function( data ) {
				var courseList = "#courseList" + idNumber
				$(courseList).empty();
				var classes = $.parseJSON(data);

				//Instatiate a select,
				var classList = $("<select>") 
				classList.attr("id","classListSelect" + idNumber)

				//Fill the options with all classes associated with the subject selected
				for (var i = 0; i < classes.length; i++)
				{
					var newOption = $("<option>")
					newOption.attr("value", classes[i].CourseNumber).text(classes[i].CourseNumber + " " + classes[i].Title)
					classList.append(newOption)
				}

				$(courseList).append(classList);
				$(courseList).append("<br>");


				//Create a checkbox so that a user may designate if a course is required 
				var requiredCheckBox = $("<input>")
				requiredCheckBox.attr("type","checkbox")
				requiredCheckBox.attr("id","requiredCheckBox"+courseCount)
				$(courseList).append("Required?  ");
				$(courseList).append(requiredCheckBox);

				$(courseList).append("<br>");

				//Create a checkbox so that a user may designate if they want an online course
				var onlineCheckBox = $("<input>")
				onlineCheckBox.attr("type","checkbox")
				onlineCheckBox.attr("id","onlineCheckBox"+courseCount)
				$(courseList).append("Online?  ");
				$(courseList).append(onlineCheckBox);
			});
	}
});

$("#addClass").click(function() {
	courseCount += 1

	if (courseCount <= 6) {

		var newCourse = $("#course0").clone()

		//update attributes and child attributes
		newCourse.attr("id","course"+courseCount)
		newCourse.children("#courseList0").attr("id","courseList"+courseCount).empty()

		newCourse.children("#subjectListSelect0").attr("id","subjectListSelect"+courseCount)
		newCourse.children("#classListSelect0").attr("id","classListSelect"+courseCount)

		$("#courseSelector").append(newCourse)
	
	} else {
		//Error message saying too many coursess
	}
});

$("#submitClasses").click(function() {
	$("#results").parent().show()
	$("#results").empty()

	var startTime = $("#startTime").val()
	var endTime = $("#endTime").val()

	startTime = parseTime(startTime)
	endTime = parseTime(endTime)

	var daysOff = { "Monday" : $("#mondayCheckBox").prop("checked"), "Tuesday" : $("#tuesdayCheckBox").prop("checked"), 
				"Wednesday" : $("#wednesdayCheckBox").prop("checked"), "Thursday" : $("#thursdayCheckBox").prop("checked"),
				"Friday" : $("#fridayCheckBox").prop("checked") }


	//Create array that will hold the courses that a user selected
	var courseArray = []
	for (var i = 0; i <= courseCount; i++) {
		var subjectListSelect = "#subjectListSelect" + i
		var classListSelect = "#classListSelect" + i
		var requiredCheckBox = "#requiredCheckBox" + i
		var onlineCheckBox = "#onlineCheckBox" + i
		//$("#results").append("<p>" + $(subjectListSelect).val() + " " + $(classListSelect).val() + "</p>")
		courseArray[i] = [ $(subjectListSelect).val(), $(classListSelect).val(), 
				$(requiredCheckBox).prop('checked'), $(onlineCheckBox).prop('checked')]
	}

	console.log(courseArray)
	
	//Send courses to handler
	$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getSchedule.php',
			'data' : { 'courses' : courseArray, 'startTime' : startTime, 'endTime' : endTime, 'daysOff' : daysOff} }
			).done( function(result) {
				//return result
				var serverMessage = $("<h3>")
				var center = $("<center>")
				center.html("Your perfect schedule:")
				serverMessage.html(center)

				//serverMessage.html("The server responded with this:")
				$("#results").append(serverMessage)

				//uncomment the following once the php script is working
				var schedule = $.parseJSON(result);


				var resultsTable = $("<table>").attr("class","table table-striped").attr("border",0)

				var keyRow = $("<tr>").attr("class","text-left")
				for (var key in schedule[0]) { 
  					var tableData = $("<td>")
  					tableData.html(key)
  					keyRow.append(tableData)
				}
				resultsTable.append(keyRow)

				for (var i = 0; i < schedule.length; i++) {
					var tableRow = $("<tr>").attr("class","text-left")
					for (var key in schedule[i]) { 
						var value = schedule[i][key];
      					var tableData = $("<td>")
      					tableData.html(value)
      					tableRow.append(tableData)
					}
					resultsTable.append(tableRow)
				}

				createCalendar(schedule);

				$("#results").append(resultsTable)


				/*
				$("#results").append("unparsedJSON:")
				//delete this once the php script is working
				$("#results").append(result)
				*/

				/*
				for(var i = 0; i < schedule.length; i++) {
					$("#results").append("<p>" + schedule[i][0] + " " + schedule[i][1] + "</p>")
				}*/
			});
});

function parseTime (time) {
	var locationOfColon = 0
	for (var i = 0; i < time.length; i++) {
		if (time[i] == ":")
			locationOfColon = i
	}

	var hour, minute, meridiem;

	if (locationOfColon == 1) {
		//then they typed in something like 9:54 AM
		hour = time[0]
		minute = time[2] + time[3]
		meridiem = time [5] + time[6]
	} else {
		//then they typed in something like 10:55 AM
		hour = time [0] + time[1]
		minute = time[3] + time[4]
		meridiem = time [6] + time[7]
	}

	hour = parseInt(hour)

	if (meridiem == "PM") {
		if(hour != 12 ) {
			hour += 12
		}
	}
	if (meridiem == "AM") {
		if(hour == 12) {
			hour = 0
		}
	}

	var returnThis = hour + minute
	return parseInt(returnThis)
}

function createCalendar (schedule) {
	for (var i = 0; i < schedule.length; i++) {
		var days = schedule[i]["Days"].split(" ")
	}
}