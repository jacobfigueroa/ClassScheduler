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
	newCourse.attr("id","course"+courseCount)
	newCourse.children("#courseList0").attr("id","courseList"+courseCount)
	newCourse.children("#classListSelect0").attr("id","classListSelect"+courseCount)
	newCourse.children("#subjectListSelect0").attr("id","subjectListSelect"+courseCount)
	$("#courseSelector").append(newCourse)
});

$("#submitClasses").click(function() {
	$("#results").html("<p>Congrats you've submitted something. Here's your courses:</p>")
});