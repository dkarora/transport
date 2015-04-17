$(function() {
	// add the preview link
	var previewlinkcode = '<a style="float: right; font-size: 125%" href="javascript:;" onclick="DoPreview()">Preview Post</a>';
	var backtoauthoringlinkcode = '<p style="width: 100%; text-align: right; margin: 0;"><a style="font-size: 125%;" href="javascript:;" onclick="HidePreview()">Back to Authoring</a></p>';
	
	$("#content .submit").append(previewlinkcode);
	$("#write-new-post").prepend(previewlinkcode);
	
	$("#preview-pane").css('padding', 0).css('margin', 0).append(backtoauthoringlinkcode);
	$("#preview-pane").prepend('<a style="font-size: 125%; float: right;" href="javascript:;" onclick="HidePreview()">Back to Authoring</a>');
});

function HidePreview()
{
	$('#preview-pane').hide();
	$('#write-new-post').show();
}

function DoPreview()
{
	var text = $('#NewsPostContent').val();
	$('#preview-pane').show();
	$('#write-new-post').hide();
	$('#preview-content').children().remove().end().append('<h2>' + $('#NewsPostTitle').val() + '</h2>').append('<p></p>');
	
	var lines = Explode(text);
	for (var i = 0; i < lines.length; i++)
		$('#preview-content p').css('margin', 0).append(ReplaceUrls(lines[i]) + '<br />');
}

function Explode(s)
{
	return s.split("\n");
}

function ReplaceUrls(s)
{
	// do images first
	var imgregexp = /\b[a-zA-Z0-9-:\/\._]+?[a-zA-Z0-9_-]+\.(jpe?g|png|tiff?|gif|bmp)\b/;
	var regexp = /\b(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?\b/
	var rt = '';
	
	var index = s.search(imgregexp);
	var matches = imgregexp.exec(s);
	
	while (index >= 0)
	{
		rt += s.substring(0, index);
		rt += '<img src="' + matches[0] + '" />';
		
		s = s.substring(index + matches[0].length);
		matches = imgregexp.exec(s);
		index = s.search(imgregexp);
	}
	
	var index = s.search(regexp);
	var matches = regexp.exec(s);
	
	while (index >= 0)
	{
		rt += s.substring(0, index);
		rt += '<a href="' + matches[0] + '">[link]</a>';
		
		s = s.substring(index + matches[0].length);
		matches = regexp.exec(s);
		index = s.search(regexp);
	}
	
	rt += s;
	return rt;
}
