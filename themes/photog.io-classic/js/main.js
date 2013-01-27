// http://www.nutt.net/2012/04/21/adding-a-label-when-placeholder-isnt-supported-by-browser/
$.support.placeholder = (function() {
    return 'placeholder' in document.createElement('input');
})();

  function getUrlParts() {
    return document.URL.split('/').filter(function(element){
        return (element != '');
      }).slice(2);
  }

 function page2path( page ) {
    var url_parts = getUrlParts();

      if ( url_parts.length ) {
        if ( url_parts.slice(-2)[0] == 'page' ) {
          var new_url = '/' + url_parts.slice( 0, -1 ).join( '/' ) + '/' + page + '/';
        } else {
          var new_url = '/' + url_parts.join( '/' ) + '/page/' + page + '/';
        }
      } else {
          var new_url = '/page/' + page + '/';
      }
    return new_url;
  }

  function getCurrentPage(){
    var url_parts = getUrlParts();
    if ( url_parts.slice(-2)[0] == 'page' ) {
      return url_parts.slice(-1)[0];
    } else {
      return 1;
    }
  }

function upload( img_data, onSuccess ) {
      // var base_url = $('link[rel="home"]')[0].href;
      var post_url = base_url + 'upload?ajax=true';
      var send_data = {'image': img_data };
      $.post( post_url, send_data, function(data, textStatus, jqXHR) {
              $('#holder img').removeClass('loading');
              // progress.value = progress.innerHTML = 100;
              var new_photo_id = data;
              // $('[name="photo-id"]').val( new_photo_id );
              // window.location = data.photo.permalink;
              if ( onSuccess ) {
                onSuccess( data );
              }
      }, 'json');
}

function edit_photo( photo_info, onSuccess ) {
      var post_url = window.location.pathname + '/edit';
      var send_data = photo_info;
      $.post( post_url, send_data, function(data, textStatus, jqXHR) {
              onSuccess( data );
      }, 'json');
}

$( document ).ajaxError(function(event, jqxhr, settings, exception) {
  console.log(event);
  console.log(jqxhr);
  console.log(settings);
  console.log(exception);
});

navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
window.URL = window.URL || window.webkitURL || window.mozURL || window.msURL;

$(document).ready(function() {
  base_url = $('link[rel="home"]')[0].href;

  if ($.support.placeholder) {

      $('label[for]').each(function(i, val) {
          var input_selector = '#' + $(this).prop('for');
          input_selector = input_selector.replace("[", "\\\[");
          input_selector = input_selector.replace("]", "\\\]");

          var input = $( input_selector );

      	if ( ! $( input ).prop('placeholder' ) ) {
        	$( input ).prop('placeholder', $(this).html() );
			$(this).remove();
      	}
      });
  }

  $('.photos').infinitescroll({
    // debug: true,

    // path: ["page/", "/"],

    state: {
      currPage: getCurrentPage()
    },
    path: page2path,
    navSelector  : "nav.pagination",
                   // selector for the paged navigation (it will be hidden)
    nextSelector : "nav.pagination .next a",
                   // selector for the NEXT link (to page 2)
    itemSelector : ".photos > li",          
                   // selector for all items you'll retrieve
    bufferPx     : 300,
    loading      :  { img : '', msgText: 'Loading more photos...', finishedMsg: '', selector: '.entry-content' },
    animate      :  false
   }, function( json, opts ) {
      // Get current page
      var page = opts.state.currPage;

      var new_url = page2path( page );

      history.pushState( null, "", new_url );
      _gaq.push([ '_trackPageview', new_url ]);

    }
  );

    $('.site-title').fitText(0.5);

    $('.photos .caption').dotdotdot( {watch: true} );

    $('html').addClass($.fn.details.support ? 'details' : 'no-details');    
    $('details').details();

    $('[href$="/login/"]').click(function(e){
        e.preventDefault();
        $('.login').css( {display: 'block'} );
        return false;
    });
    $('[href$="/upload/"]').click(function(e){
        e.preventDefault();
        // $('.upload').css( {display: 'block'} );
        $('#file').trigger('click');
        return false;
    });

    if ( navigator.getUserMedia ) {
      $('html').removeClass('no-getusermedia');
    }

    // BEGIN file upload handling
    // Courtesy of @rem: http://html5demos.com/dnd-upload
    var holder = document.getElementById('holder'),
        tests = {
          filereader: typeof FileReader != 'undefined',
          dnd: 'draggable' in document.createElement('span'),
          formdata: !!window.FormData,
          progress: "upload" in new XMLHttpRequest
        }, 
        support = {
          filereader: document.getElementById('filereader'),
          formdata: document.getElementById('formdata'),
          progress: document.getElementById('progress')
        },
        acceptedTypes = {
          'image/png': true,
          'image/jpeg': true,
          'image/gif': true
        },
        progress = document.getElementById('uploadprogress'),
        fileupload = document.getElementById('file');

    "filereader formdata progress".split(' ').forEach(function (api) {
      if (tests[api] === false) {
        support[api].className = 'fail';
      } else {
        // FFS. I could have done el.hidden = true, but IE doesn't support
        // hidden, so I tried to create a polyfill that would extend the
        // Element.prototype, but then IE10 doesn't even give me access
        // to the Element object. Brilliant.
        // support[api].className = 'hidden';
      }
    });

    function previewfile(file) {
      if (tests.filereader === true && acceptedTypes[file.type] === true) {
        var reader = new FileReader();
        reader.onload = function (event) {
          var image = new Image();
          image.src = event.target.result;
          image.className = 'loading';
          image.width = 250; // a fake resize
          holder.appendChild(image);
          $('p', holder).remove();
        };
        reader.readAsDataURL(file);
      }  else {
      }
    }

    function readfiles(files) {
        var img_data = {};
        
        var reader = new FileReader();
        
        reader.onload = function(event){
            img_data = event.target.result;            
            data = upload( img_data, function(data){
              window.location = data.photo.permalink;
            });
        }

        reader.readAsDataURL( files[0] );
        previewfile( files[0] );
           
    }

    if ( tests.dnd ) { 
      holder.ondragover = function () { this.className = 'drag'; return false; };
      holder.ondragleave = function () { this.className = ''; return false; };
      holder.ondrop = function (e) {
        this.className = '';
        e.preventDefault();
        readfiles(e.dataTransfer.files);
      }
        // fileupload.className = 'hidden';
    }
    fileupload.onchange = function () {
        readfiles(this.files);
    };    
    // END file upload handling

}); // $(document).ready