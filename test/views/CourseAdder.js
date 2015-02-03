var courseCount = 0

$("#courseSelector").on("change","select", function(){
	if( $(this).attr("class") == "subjectListSelect" ) {
		var subject = $(this).val()
		var id = $(this).attr("id")
		var idNumber = id.substr(id.length-1,id.length-1) //start,end. Returns last character
		console.log(idNumber)

		//Send subject selected, return list of courses associated with that subject
		$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getCourses.php',
			'data' : { 'subject' : subject} }
			).done( function( data ) {
				var courseList = "#courseList" + idNumber
				$(courseList).empty();
				var classes = $.parseJSON(data);

				var classList = $("<select>") //Instatiate a select
				classList.attr("id","classListSelect"+idNumber)
				for(var i = 0; i < classes.length; i++)
				{
					var newOption = $("<option>")
					newOption.attr("value", classes[i].CourseNumber).text(classes[i].CourseNumber + " " + classes[i].Title)
					classList.append(newOption)
				}

				$(courseList).append(classList);
			});
	}
});

$("#addClass").click(function() {
	courseCount += 1
	var newCourse = $("#course0").clone()

	//update attributes and child attributes
	newCourse.attr("id","course"+courseCount)
	newCourse.children("#courseList0").attr("id","courseList"+courseCount)
	newCourse.children("#classListSelect0").attr("id","classListSelect"+courseCount)
	newCourse.children("#subjectListSelect0").attr("id","subjectListSelect"+courseCount)

	$("#courseSelector").append(newCourse)
});

$("#submitClasses").click(function() {

	$("#results").empty()
	var courseArray = []
	for(var i = 0; i <= courseCount; i++)
	{
		var subjectListSelect = "#subjectListSelect" + i
		var classListSelect = "#classListSelect" + i
		//$("#results").append("<p>" + $(subjectListSelect).val() + " " + $(classListSelect).val() + "</p>")
		courseArray[i] = [$(subjectListSelect).val(),$(classListSelect).val()]
	}

	console.log(courseArray)
	
	//Send courses to handler
	$.ajax( { 
			'type' : 'POST',
			'url' : 'handlers/getSchedule.php',
			'data' : { 'courses' : courseArray} }
			).done( function(result) {
				//return result
				var serverMessage = $("<p>")
				serverMessage.html("After the AJAX call the server returned this:")
				$("#results").append(serverMessage)

				var schedule = $.parseJSON(result);
				$("#results").append(schedule)
				/*
				for(var i = 0; i < schedule.length; i++)
				{
					$("#results").append("<p>" + schedule[i][0] + " " + schedule[i][1] + "</p>")
				}*/
			});
});