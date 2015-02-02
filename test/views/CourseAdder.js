
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
} );

$('#subjectListSelect').change(function(){
	subject = $(this).val();
	$.ajax( { 
		'type' : 'POST',
		'url' : 'handlers/getCourses.php',
		'data' : { 'subject' : subject} }
		).done( function( data ) {
			//$('#courseList').append(data);
			$('#courseList').empty();

			var classList = '<select>'
			var obj = $.parseJSON(data);
			for(var i = 0; i < obj.length; i++)
			{
				classList += '<option value = "blah">' + obj[i].CourseNumber + ' ' + obj[i].Title + '</option>'
			}
			classList += '</select>'
			$('#courseList').append(classList);
		} );
});