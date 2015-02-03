var courseCount = 0
/*
$( '#submitbutton' ).click(function() { 
	var newLine;
	newLine = '<div>' + '<p>' + 'Select course major: ' + '<select>'

	//for( var i=0, len = 5; i<len; i++ ){ 
	
	newLine = newLine + '<option value="CSCI">' + 'CSCI' + '</option>'
	
	newLine = newLine + '<option value="Math">' + 'Math' + '</option>'
	
	newLine = newLine + '<option value="PHYS">' + 'PHYS' + '</option>'
	//} 
 
	newLine = newLine + '</select>' + '</p>' + '</div>'
	$( '#courseList' ).prepend( newLine );
	//$( '#courseList' ).prepend( '<div>' + '<p>' + 'Select course major: ' + '<select>' + '<option value="CSCI">' + 'CSCI' + '</option>' + '<option value="Math">' + 'Math' + '</option>' +'</p>' + '</div>');  
} );*/

//$('.subjectListSelect').on( "change", function() {
//$('.subjectListSelect').change(function(){
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
				$('#courseList').empty();
				var classes = $.parseJSON(data);

				var classList = $("<select>") //Instatiate a select
				classList.attr("id","classListSelect"+courseCount)
				for(var i = 0; i < classes.length; i++)
				{
					var newOption = $("<option>")
					newOption.attr("value", classes[i].CourseNumber).text(classes[i].CourseNumber + " " + classes[i].Title)
					classList.append(newOption)
				}
				
				$('#courseList').append(classList);
			});
	}
});

//$(staticAncestors).on(eventName, dynamicChild, function() {});