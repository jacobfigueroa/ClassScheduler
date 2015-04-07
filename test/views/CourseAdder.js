var courseCount = 0
var schedules
var scheduleIndex = 0;
var errors = [];

$("#results").parent().hide()
$("#errors").hide()

$("#courseSelector").on("change","select", function(){
	if( $(this).attr("class") == "subjectListSelect" ) {
		var subject = $(this).val()
		var id = $(this).attr("id")
		var idNumber = id.substr(id.length-1,id.length-1) //start,end. Returns last character

		//Send subject selected, return list of courses associated with that subject
		$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getCourses.php',
			'data' : { 'subject' : subject } }
			).done( function( data ) {
				//$("#courseSelector").append(data);
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

	if (courseCount < 6) {

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
	hideAndResetErrors();

	if (!areAllClassesFilledIn()) {
		showErrors();
		return;
	}

	var daysInfo = [];
	daysInfo = prepareDaysInfo();

	scheduleIndex = 0
	$("#results").parent().show()
	$("#results").empty()
	$("#results").html("Creating your perfect schedule...")
	$('#calendar').fullCalendar('removeEvents')


	//Create array that will hold the courses that a user selected
	var courseArray = []
	for (var i = 0; i <= courseCount; i++) {
		var subjectListSelectId = "#subjectListSelect" + i
		var classListSelectId = "#classListSelect" + i
		var onlineCheckBoxId = "#onlineCheckBox" + i
		courseArray[i] = [ $(subjectListSelectId).val(), $(classListSelectId).val(), $(onlineCheckBoxId).prop('checked')]
	}
	
	//Send courses to handler
	$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getSchedule.php',
			'data' : { 'courses' : courseArray, 'daysInfo' : daysInfo } }
			).done( function(result) {
				$("#results").append(result)
				schedules = $.parseJSON(result)
				showResult(schedules[scheduleIndex]) //Initially scheduleIndex = 0
			});
});

function hideAndResetErrors() {
	$("#errors").hide();
	errors = []
}

function showErrors() {
	$("#errors").empty();
	$("#errors").show();
	for (var i = 0; i < errors.length; i++) {
		console.log(errors[i])
		$("#errors").append(errors[i] + "<br>");
	}
}

function areAllClassesFilledIn() {
	var allClassesFilledIn = true;
	for (var i = 0; i <= courseCount; i++) {
		var subjectListSelect = "#subjectListSelect" + i
		if($(subjectListSelect).val() == "NULL") {
			allClassesFilledIn = false;
			errors.push("Course " + (i+1) + " was left blank. Please fill in all course info.");
		}
	}
	return allClassesFilledIn;
}

function prepareDaysInfo() {
		///////////////////////////////
	//THIS WILL BE THE NEW FORMAT//
	var days = ["M", "T", "W", "R", "F"];
	var fullDaysName = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
	var daysInfo = [];
	for(var i = 0; i < days.length; i++)
	{
		var day = days[i]
		var startTime = $("#"+day).children(".startTime").val()
		var endTime = $("#"+day).children(".endTime").val()
		var dayOff = $("#"+day).children(".dayOffCheckBox").prop("checked")


		if(startTime == "")
			startTime = "12:00 AM"
		if(endTime == "")
			endTime = "11:59 PM"

		startTime = convertTimeToMilitaryTime( startTime )
		endTime = convertTimeToMilitaryTime( endTime )

		if(startTime == "Not proper format" ) {
			console.log("Not proper format for start time for " + fullDaysName[i])
			errors.push("Not proper format for start time for " + fullDaysName[i])
		}
		if (endTime == "Not proper format") {
			errors.push("Not proper format for end time for " + fullDaysName[i])
		}

		var dayInfo = { day : { "startTime" : startTime, 
								"endTime" : endTime,
								"dayOff" : dayOff } }

		daysInfo.push(dayInfo)
	}
	console.log(daysInfo)
	return daysInfo;
	///////////////////////////////
	///////////////////////////////
}
// Example: 11:54 AM to 1154. 2:35 PM to 1435.
function convertTimeToMilitaryTime (time) {
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
	} else if (locationOfColon == 2) {
		//then they typed in something like 10:55 AM
		hour = time [0] + time[1]
		minute = time[3] + time[4]
		meridiem = time [6] + time[7]
	} else {
		// Not proper format
		return "Not proper format"
	}

	hour = parseInt(hour)

	if (meridiem.toUpperCase() == "PM") {
		if(hour != 12 ) {
			hour += 12
		}
	}
	if (meridiem.toUpperCase() == "AM") {
		if(hour == 12) {
			hour = 0
		}
	}

	var returnThis = hour + minute
	return parseInt(returnThis)
}

// Example: 1154 to 11:54 AM. 1435 to 2:35 PM.
function convertMilitaryTimeToTime (militaryTime) {
	//Convert to string first
	militaryTime += ""

	var hour, minute, meridiem;

	if ( militaryTime.length == 4 ) {
		//Example: 1145      hour = 11  minute = 45
		var hour = militaryTime[0] + militaryTime[1]
		var minute = militaryTime[2] + militaryTime[3]
	} else if (militaryTime.length == 3) {
		//Example: 935      hour = 9  minute = 35
		var hour = militaryTime[0]
		var minute = militaryTime[1] + militaryTime[2]
	} else {
		// Not proper format.
		//militaryTime comes from the database so most likely the class doesnt have a time
		return ""
	}

	if ( parseInt(hour) < 12 ) {
		meridiem = "AM"
	} else {
		meridiem = "PM"
		if ( parseInt(hour) > 12 ) {
			hour = (parseInt(hour) - 12) + ""
		}
	}

	return hour + ":" + minute + " " + meridiem
}

//Creates a calendary. at the moment the function is not used
function createCalendar (schedule) {

	$('#calendar').fullCalendar('removeEvents')

	$('#calendar').fullCalendar({
		header: false,
		defaultView: 'agendaWeek',
		weekends: false,
		defaultDate: '2015-02-09',
		editable: false,
		eventLimit: true, // allow "more" link when too many events
		minTime: "07:00:00",
		columnFormat: "ddd"
	});

				
	for (var i = 0; i < schedule.length; i++) {
		var days = splitDays(schedule[i]);
		var color = getColor(i)

		if(days == "") // The class has no meeting days (online)
		{
				var newEvent = new Object();
				newEvent.title = schedule[i]["CourseName"] + " " + schedule[i]["Title"] + " " + schedule[i]["Instructor"]
				newEvent.color = color
				newEvent.start = "2015-02-09"
				newEvent.end = "2015-02-14"
				newEvent.allDay = true;
				$('#calendar').fullCalendar( 'renderEvent', newEvent );
		}
		else
		{
			for (var j = 0; j < days.length; j++) {
				var newEvent = new Object();
				newEvent.title = schedule[i]["CourseName"] + " " + schedule[i]["Title"] + " " + schedule[i]["Instructor"]
				newEvent.color = color
				var day;
				//lazy way to do it
				if (days[j] == "M") {
					day = "2015-02-09"
				} else if (days[j] == "T") {
					day = "2015-02-10"
				} else if (days[j] == "W") {
					day = "2015-02-11"
				} else if (days[j] == "R") {
					day = "2015-02-12"
				} else if (days[j] == "F") {
					day = "2015-02-13"
				}
				newEvent.start = day + "T" + convertMilitaryTimeToFullCalendarFormat(schedule[i]["Start"])
				newEvent.end = day + "T" + convertMilitaryTimeToFullCalendarFormat(schedule[i]["End"])
				newEvent.allDay = false;

				$('#calendar').fullCalendar( 'renderEvent', newEvent );
			}
		}
	}
}

function getEarliestStartTimeOfClasses(schedule)
{
	var minTime = schedule[0]["Start"]
	for(var i = 0; i < schedule.length; i++)
	{
		if(schedule[i]["Start"] < minTime)
		{
			minTime = schedule[i]["Start"]
		}
	}
	return minTime
}
function getColor(i) {
	var colors = ["red", "blue", "green", "black", "orange", "purple"];
	return colors[i]
}

function splitDays(course)
{
	//Split the days that a course is offered.
	//Will have to make a seperate entry in calendar for each day of class.
	//For example a MWF class will need an entry on Monday, Wednesday and Friday.
	//So the below code will take care of that.
	var days = course["Days"].split(" ")
	return days
}

function convertMilitaryTimeToFullCalendarFormat(militaryTime){
	if ( militaryTime.length == 4 ) {
		//Example: 1145      hour = 11  minute = 45
		var hour = militaryTime[0] + militaryTime[1]
		var minute = militaryTime[2] + militaryTime[3]
	} else if (militaryTime.length == 3) {
		//Example: 935      hour = 9  minute = 35
		var hour = militaryTime[0]
		var minute = militaryTime[1] + militaryTime[2]

		hour = "0" + hour;
	}

	return hour + ":" + minute + ":" + "00"
}

function scrollTo(id) {
  Gentle_Anchors.Setup(id);
}

function showResult(result)
{
	$("#results").empty()

	//return result
	var serverMessage = $("<h3>")
	var center = $("<center>")
	center.html("Your perfect schedule:")
	serverMessage.html(center)

	//serverMessage.html("The server responded with this:")
	$("#results").append(serverMessage)

	//uncomment the following once the php script is working
	var schedule = result//$.parseJSON(result);


	// Create a table to display the classes that are returned
	var resultsTable = $("<table>").attr("class","table table-striped").attr("border",0)

	// Create a table row that displays the keys.
	var keyRow = $("<tr>").attr("class","text-left")
	for (var key in schedule[0]) { 
			var tableData = $("<td>")
			tableData.html(key)
			keyRow.append(tableData)
	}
	resultsTable.append(keyRow)

	// Create a table row that displays the values
	for (var i = 0; i < schedule.length; i++) {
		var tableRow = $("<tr>").attr("class","text-left")

		// iterate through all keys
		for (var key in schedule[i]) {
			// get associated value
			var value = schedule[i][key];
				var tableData = $("<td>")

				// Change military time from DB to normal time
				if (key == "Start" || key == "End") {
					tableData.html(convertMilitaryTimeToTime(value))
				} else {
					tableData.html(value)
				}
				
				tableRow.append(tableData)
		}
		resultsTable.append(tableRow)
	}


	$("#results").append(resultsTable)

	//create calendar
	var calendar = createCalendar(schedule)
	$("#calendar").append(calendar)
	//Automatically scroll to the results
	//scrollTo("#results")

	var prevScheduleButton = $("<button>") 
	prevScheduleButton.attr("type","button")
	prevScheduleButton.attr("class","btn btn-default")
	prevScheduleButton.attr("id","prevScheduleButton")
	prevScheduleButton.html("Previous Schedule")
	
	var nextScheduleButton = $("<button>") 
	nextScheduleButton.attr("type","button")
	nextScheduleButton.attr("class","btn btn-default")
	nextScheduleButton.attr("id","nextScheduleButton")
	nextScheduleButton.html("Next Schedule")


	$("#results").append(prevScheduleButton)
	$("#results").append(" ")
	$("#results").append(nextScheduleButton)
	$("#results").append(" ")

	//Since scheduleIndex is 0 based. It wouldn't make sense to the user to display Schedule 0
	//Instead add 1 to the index
	var text = "Schedule " + (scheduleIndex + 1) + " of " + schedules.length 
	$("#results").append(text)
}

$("#results").on("click","button", function() {
	if( $(this).attr("id") == "nextScheduleButton")
	{
		if(scheduleIndex + 1 < schedules.length)
		{
			scheduleIndex++
			showResult(schedules[scheduleIndex])
		}
	}
	if( $(this).attr("id") == "prevScheduleButton")
	{
		if(scheduleIndex - 1 >= 0)
		{
			scheduleIndex--
			showResult(schedules[scheduleIndex])
		}
	}
});
