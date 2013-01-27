$(document).ready(function() {

// Requires js/raf.js if using requestAnimationFrame()
// theme_url('js/raf.js')

/*
audio = document.createElement('audio');
var source_mp3 = document.createElement('source');
var source_ogg = document.createElement('source');

audio.appendChild( source_mp3 );
audio.appendChild( source_ogg );
source_mp3.src = base_url + 'static/audio/alert.mp3';
source_ogg.src = base_url + 'static/audio/alert.ogg';
*/

navigator.getUserMedia( {'audio':false, 'video':true}, onSuccess, onError);

function onSuccess( stream ) {
	var video = document.querySelector('video');
	var canvas = document.querySelector('canvas');


	if ( navigator.mozGetUserMedia ) {
		video.mozSrcObject = stream;
	} else if ( window.URL && window.URL.createObjectURL ) {
		video.src = window.URL.createObjectURL( stream );
	} else {
		video.src = stream;
	}
	video.play();
	$('.pending-permission').remove();
	$('<div class="status-text loading-video"><p>Loading video...</p></div>').appendTo('.video-container');
	video.addEventListener('canplay', function(){
		// Uncomment this line to copy the <video> to the <canvas> (To be used for filters)
		// requestAnimationFrame( paintOnCanvas );
	});

	video.addEventListener('playing', function(){
		console.log('playing');
		$('.status-text.loading-video').remove();
	});
}

function onError() {
    console.log('GetUserMedia has FAILED! D:');
}

var back = document.createElement('canvas');
var backcontext = back.getContext('2d');

function paintOnCanvas() {
	// place the rAF *before* the render() to assure as close to 
    // 60fps with the setTimeout fallback.
    // requestAnimationFrame( paintOnCanvas );

	var video = document.querySelector('video');
	var canvas = document.querySelector('canvas');

	var w = video.videoWidth;
	var h = video.videoHeight;

	canvas.width = w;
	canvas.height = h;

	var ctx = canvas.getContext('2d');
	ctx.drawImage( video, 0, 0, w, h );


	var idata = ctx.getImageData( 0, 0, w, h );

	// filter = 'noise';
	// idata = Filters[filter]( idata );

	// Draw the pixels onto the visible canvas
	ctx.putImageData( idata, 0, 0 );
}

Filters = {};

Filters.grayscale = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them grayscale
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		var brightness = (3*r+4*g+b)>>>3;
		data[i] = brightness;
		data[i+1] = brightness;
		data[i+2] = brightness;
	}

	idata.data = data;
	return idata;
}

Filters.blue = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them blue
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		var brightness = (3*r+4*g+b)>>>3;
		data[i] = brightness;
		data[i+1] = brightness;
		data[i+2] = brightness + 100;
	}

	idata.data = data;
	return idata;
}

Filters.sepia = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them blue
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		var brightness = (3*r+4*g+b)>>>3;
		data[i] = brightness + 20;
		data[i+1] = brightness - 20;
		data[i+2] = brightness - 20;
	}

	idata.data = data;
	return idata;
}


Filters.translucent = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them blue
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		var brightness = (3*r+4*g+b)>>>3;
		// data[i] = brightness;
		// data[i+1] = brightness;
		// data[i+2] = brightness;
		data[i+3] = data[i+1];
	}

	idata.data = data;
	return idata;
}

Filters.invert = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them blue
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		var brightness = (3*r+4*g+b)>>>3;
		data[i] = 255 - data[i];
		data[i+1] = 255 - data[i+1];
		data[i+2] = 255 - data[i+2];
	}

	idata.data = data;
	return idata;
}

Filters.noise = function( idata ) {

	var data = idata.data;
	// Loop through the pixels, turning them blue
	for( var i = 0; i < data.length; i+=4 ) {
		var r = data[i];
		var g = data[i+1];
		var b = data[i+2];
		// var brightness = (3*r+4*g+b)>>>3;
		var rand =  (0.5 - Math.random()) * 50;
		data[i] = data[i] + rand;
		data[i+1] = data[i+1] + rand;
		data[i+2] = data[i+2] + rand;
	}

	idata.data = data;
	return idata;
}

$('video').click(function(){
	document.querySelector('video').pause();

	$('<div class="status-text uploading"><p>Uploading...</p></div>').appendTo('.video-container');
	paintOnCanvas();
	var img = document.createElement('img');
	var canvas = document.querySelector('canvas');
	img.className = 'snapped-photo';

	img.src = canvas.toDataURL('image/jpeg');
	
	upload( img.src, function( data ){
		window.location = data.photo.permalink;
		// window.location = data.permalink;
	} );
	
	// Maybe in addition to/instead of the audio,
	// we should display an ajax throbber until the page reloads
	// First show an "Uploading..." message
	// and then maybe a "Redirecting..." message
	// audio.play();

	// document.querySelector('.entry-content').appendChild(img);
});

});