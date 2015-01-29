
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

$('select').change(function(){
	hello = "<p>You selected something..</p>"
	$('#courseList').append(hello);

	subject = $(this).val();
	//$( '#courseList' ).prepend( value );
	$.ajax( { 
		'type' : 'POST',
		'url' : 'handlers/getCourses.php',
		'data' : { 'subject' : subject} }
		).done( function( data ) {
			hello = "<p>AJAX is done... V</p>"
			$('#courseList').append(hello);
			$('#courseList').append(data);
			bye = "<p>^</p>";
			$('#courseList').append(bye);
		} );
});