var SlimStat = {
	// Private Properties
    _id: "undefined" != typeof SlimStatParams.id ? SlimStatParams.id : "-1.0",
    _base64_key_str: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    _plugins: {
        acrobat: {
            substrings: ["Adobe", "Acrobat"],
            active_x_strings: ["AcroPDF.PDF", "PDF.PDFCtrl.5"]
        },
        director: {
            substrings: ["Shockwave", "Director"],
            active_x_strings: ["SWCtl.SWCtl"]
        },
        flash: {
            substrings: ["Shockwave", "Flash"],
            active_x_strings: ["ShockwaveFlash.ShockwaveFlash"]
        },
        mediaplayer: {
            substrings: ["Windows Media"],
            active_x_strings: ["WMPlayer.OCX"]
        },
        quicktime: {
            substrings: ["QuickTime"],
            active_x_strings: ["QuickTime.QuickTime"]
        },
        real: {
            substrings: ["RealPlayer"],
            active_x_strings: ["rmocx.RealPlayer G2 Control", "RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)", "RealVideo.RealVideo(tm) ActiveX Control (32-bit)"]
        },
        silverlight: {
            substrings: ["Silverlight"],
            active_x_strings: ["AgControl.AgControl"]
        }
    },

    _utf8_encode : function (string) {
		var n, c, utftext = "";

		string = string.replace(/\r\n/g,"\n");

		for (n = 0; n < string.length; n++) {
			c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
		}
		return utftext;
	},

    // Base64 Encode - http://www.webtoolkit.info/
	_base64_encode : function ( input ) {
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4, output = "", i = 0;

		input = SlimStat._utf8_encode(input);

		while (i < input.length) {
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);

			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;

			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}

			output = output + SlimStat._base64_key_str.charAt(enc1) + SlimStat._base64_key_str.charAt(enc2) + SlimStat._base64_key_str.charAt(enc3) + this._base64_key_str.charAt(enc4);
		}
		return output;
	},

	_detect_single_plugin_not_ie : function ( plugin_name ) {
		var plugin, haystack, found, i, j;

		for ( i in navigator.plugins ) {
			haystack = '' + navigator.plugins[ i ].name + navigator.plugins[ i ].description;
			found = 0;

			for ( j in SlimStat._plugins[ plugin_name ].substrings ) {
				if ( haystack.indexOf( SlimStat._plugins[ plugin_name ].substrings[ j ] ) != -1 ) {
					found++;
				}
			}

			if ( found == SlimStat._plugins[ plugin_name ].substrings.length ) {
				return true;
			}
		}
		return false;
	},

   _detect_single_plugin_ie : function ( plugin_name ) {
		var i;

		for (i in SlimStat._plugins[plugin_name].active_x_strings) {
			try {
				new ActiveXObject( SlimStat._plugins[plugin_name].active_x_strings[i] );
				return true;
			}
			catch(e) {
				return false;
			}
		}
	},
	
	_detect_single_plugin : function ( plugin_name ) {
		if( navigator.plugins.length ) {
			this.detect = this._detect_single_plugin_not_ie;
		}
		else {
			this.detect = this._detect_single_plugin_ie;
		}
		return this.detect( plugin_name );
	},

	detect_plugins : function () {
		var a_plugin, plugins = [];

		for (a_plugin in SlimStat._plugins) {
			if (SlimStat._detect_single_plugin(a_plugin)) {
				plugins.push( a_plugin );
			}
		}
		return plugins.join( "," );
	},

    get_page_performance : function () {
		slim_performance = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance || {};
		if ( "undefined" == typeof slim_performance.timing ){
			return 0;
		}

		return slim_performance.timing.loadEventEnd - slim_performance.timing.responseEnd;
	},
	
	get_server_latency : function () {
		slim_performance = window.performance || window.mozPerformance || window.msPerformance || window.webkitPerformance || {};
		if ( "undefined" == typeof slim_performance.timing ){
			return 0;
		}

		return slim_performance.timing.responseEnd - slim_performance.timing.connectEnd;
	},

	send_to_server : function ( data, callback ) {
		if ( "undefined" == typeof SlimStatParams.ajaxurl || 'undefined' == typeof data ){
			if ( "function" == typeof callback ){
				callback();
			}
			return false;
		}

		try {
			if ( window.XMLHttpRequest ) {
				request = new XMLHttpRequest();
			}
			else if ( window.ActiveXObject ) { // code for IE6, IE5
				request = new ActiveXObject( "Microsoft.XMLHTTP" );
			}
		} catch ( failed ) {
			if ( "function" == typeof callback ){
				callback();
			}
			return false;
		}

		slimstat_data_with_client_info = data + "&sw=" + screen.width + "&sh=" + screen.height + "&bw=" + window.innerWidth + "&bh=" + window.innerHeight + "&sl=" + SlimStat.get_server_latency() + "&pp=" + SlimStat.get_page_performance() + "&pl=" + SlimStat.detect_plugins()

		if ( request ) {
			request.open( "POST", SlimStatParams.ajaxurl, true );
            request.setRequestHeader( "Content-type", "application/x-www-form-urlencoded" );
			request.send( slimstat_data_with_client_info );

			request.onreadystatechange = function() {
                if ( 4 == request.readyState ) {
					if ( "undefined" == typeof SlimStatParams.id ) {
						parsed_id = parseInt( request.responseText );
                        if ( !isNaN( parsed_id ) && parsed_id > 0 ) {
                            SlimStat._id = request.responseText;
                        }
					}
					else {
                        SlimStat._id = SlimStatParams.id;
                    }
                    if ( "function" == typeof callback ) {
                        callback();
                    }
				}
			}
			return true;
		}

		return false;
	},

	ss_track : function (e, c, note, callback) {
		// Check function params
		e = e ? e : window.event;
		type = ( "undefined" == typeof c ) ? 0 : parseInt( c );
		note_array = [];

		parsed_id = parseInt( SlimStat._id );
		if ( isNaN( parsed_id ) || parsed_id <= 0 ) {
			if ( "function" == typeof callback ) {
				callback();
			}
			return false;
		}

		node = ( "undefined" != typeof e.target ) ? e.target : ( ( "undefined" != typeof e.srcElement ) ? e.srcElement : false );
		if ( !node ) {
			if ("function" == typeof callback ) {
				callback();
			}
			return false;
		}
 
		// Safari bug
		if ( 3 == node.nodeType ) {
			node = node.parentNode;
		}

		parent_node = node.parentNode;
		resource_url = '';

		// This handler can be attached to any element, but only A carries the extra info we need
		switch ( node.nodeName ) {
			case 'FORM':
				if ( "undefined" != typeof node.action && node.action ) {
					resource_url = node.action;
				}
				break;

			case 'INPUT':
				// Let's look for a FORM element
				while ( "undefined" != typeof parent_node && parent_node.nodeName != "FORM" && parent_node.nodeName != "BODY" ) {
					parent_node = parent_node.parentNode;
				}
				if ( "undefined" != typeof parent_node.action && parent_node.action ) {
					resource_url = parent_node.action;
					break;
				}

			default:
				// Any other element
				if ( "A" != node.nodeName ) {
					while ( "undefined" != typeof node.parentNode && null != node.parentNode && "A" != node.nodeName && "BODY" != node.nodeName ) {
						node = node.parentNode;
					}
				}

				// Anchor in the same page
				if ( "undefined" != typeof node.hash && node.hash && node.hostname == location.hostname ) {
					resource_url = node.hash;
				}
				else if ( "undefined" != typeof node.href ) {
					resource_url = node.href;
				}

				// If this element has a title, we can record that as well
				if ( "function" == typeof node.getAttribute){
					if ( "undefined" != typeof node.getAttribute("title") && node.getAttribute("title") ) {
						note_array.push( "Title:" + node.getAttribute( "title" ) );
					}
					if ( "undefined" != typeof node.getAttribute("id") && node.getAttribute("id") ) {
						note_array.push( "ID:" + node.getAttribute( "id" ) );
					}
				}
        }

		// Event coordinates
		pos_x = -1; 
		pos_y = -1;
		position = "";

		if ( "undefined" != typeof e.pageX && "undefined" != typeof e.pageY  ) {
			pos_x = e.pageX;
			pos_y = e.pageY;
		}
		else if ( "undefined" != typeof e.clientX && "undefined" != typeof e.clientY &&
				"undefined" != typeof document.body.scrollLeft && "undefined" != typeof document.documentElement.scrollLeft &&
				"undefined" != typeof document.body.scrollTop && "undefined" != typeof document.documentElement.scrollTop ) {
			pos_x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			pos_y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}

		if ( pos_x > 0 && pos_y > 0 ) {
			position = pos_x + "," + pos_y;
		}

		// Event description and button pressed
		event_description = e.type;
		if ( "click" != e.type && "undefined" != typeof e.which ) {
			if ( "keypress" == e.type ){
				event_description += '; keypress:' + String.fromCharCode( parseInt( e.which ) );
			}
			else{
				event_description += '; which:' + e.which;
			}
		}

		// Custom description for this event
		if ( "undefined" != typeof note && note ){
			note_array.push( note );
		}

		note_string = SlimStat._base64_encode( note_array.join( ", " ) );
		requested_op = "update";
		
		if ( 1 == type ) {
			resource_url = SlimStat._base64_encode( resource_url.substring( resource_url.indexOf( location.hostname ) + location.hostname.length ) );
			requested_op = "add";
		}
		else{
			resource_url = SlimStat._base64_encode( resource_url );
		}

		SlimStat.send_to_server( "action=slimtrack&op=" + requested_op + "&id=" + SlimStat._id + "&ty=" + type + "&ref=" + SlimStat._base64_encode( document.referrer ) + "&res=" + resource_url + "&pos=" + position + "&des=" + SlimStat._base64_encode( event_description ) + "&no=" + note_string, callback );

		return true;
    },

	add_event : function ( obj, type, fn ) {
		if ( obj && obj.addEventListener ) {
			obj.addEventListener( type, fn, false );
		}
		else if ( obj && obj.attachEvent ) {
			obj[ "e" + type + fn ] = fn;
			obj[ type + fn ] = function() { obj[ "e" + type + fn ] ( window.event ); }
			obj.attachEvent( "on"+type, obj[type+fn] );
		}
		else {
			obj[ "on" + type ] = obj[ "e" + type + fn ];
		}
	},

	event_fire : function ( obj, evt ) {
		var fire_on_this = obj;

	    if ( document.createEvent ) {
	      var ev_obj = document.createEvent( 'MouseEvents' );
	      ev_obj.initEvent( evt, true, false );
	      fire_on_this.dispatchEvent( ev_obj );
	    }
	    else if ( document.createEventObject ) {
	      var ev_obj = document.createEventObject();
	      fire_on_this.fireEvent( 'on' + evt, ev_obj );
	    }
	},

	in_array : function ( needle, haystack ) {
		for ( var i = 0; i < haystack.length; i++ ) {
			if ( haystack[ i ].trim() == needle ) {
				return true;
			}
		}
		return false;
	},

	in_array_substring : function ( needle, haystack_of_substrings ) {
		for ( var i = 0; i < haystack_of_substrings.length; i++ ) {
			if ( needle.indexOf( haystack_of_substrings[ i ].trim() ) != -1 ) {
				return true;
			}
		}
		return false;
	}
};

SlimStat.add_event( window, "load", function() {
    if ( "undefined" == typeof SlimStatParams.disable_outbound_tracking ) {
        all_links = document.getElementsByTagName( "a" );
		var extensions_to_track = ( "undefined" != typeof SlimStatParams.extensions_to_track && SlimStatParams.extensions_to_track ) ? SlimStatParams.extensions_to_track.split( ',' ) : [];
		var to_ignore = ( "undefined" != typeof SlimStatParams.outbound_classes_rel_href_to_ignore && SlimStatParams.outbound_classes_rel_href_to_ignore ) ? SlimStatParams.outbound_classes_rel_href_to_ignore.split( ',' ) : [];
		var to_not_track = ( "undefined" != typeof SlimStatParams.outbound_classes_rel_href_to_not_track && SlimStatParams.outbound_classes_rel_href_to_not_track ) ? SlimStatParams.outbound_classes_rel_href_to_not_track.split( ',' ) : [];

        for (var i = 0; i < all_links.length; i++) {
			// We need a closure to keep track of some variables and carry them thorughout the process
			(function() {
				var cur_link = all_links[i];

				// Types
				// 0: external
				// 1: download
				// 2: internal

				cur_link.slimstat_actual_click = false;
				cur_link.slimstat_type = ( cur_link.href && ( cur_link.hostname == location.hostname || cur_link.href.indexOf('://') == -1 ) ) ? 2 : 0;
				cur_link.slimstat_track_me = true;
				cur_link.slimstat_callback = true;

				// Track other internal links?
				if ( 2 == cur_link.slimstat_type && ( "undefined" == typeof SlimStatParams.track_internal_links || "false" == SlimStatParams.track_internal_links ) ) {
					cur_link.slimstat_track_me = false;
				}

				// Do not invoke the callback or don't track links with given classes
				if (cur_link.slimstat_track_me && (to_ignore.length > 0 || to_not_track.length > 0)) {
					classes_current_link = ( "undefined" != typeof cur_link.className && cur_link.className ) ? cur_link.className.split(" ") : [];

					for (var cl = 0; cl < classes_current_link.length; cl++){
						if ( SlimStat.in_array_substring( classes_current_link[ cl ], to_ignore ) ) {
							cur_link.slimstat_callback = false;
						}
						if ( SlimStat.in_array_substring( classes_current_link[ cl ], to_not_track ) ) {
							cur_link.slimstat_track_me = false;
							break;
						}
					}
				}

				// Do not invoke the callback on links with given rel
				if ( cur_link.slimstat_track_me && "undefined" != typeof cur_link.attributes.rel && cur_link.attributes.rel.value ) {
					if ( SlimStat.in_array_substring( cur_link.attributes.rel.value, to_ignore ) ) {
						cur_link.slimstat_callback = false;
					}
					if ( SlimStat.in_array_substring( cur_link.attributes.rel.value, to_not_track ) ) {
						cur_link.slimstat_track_me = false;
					}
				}

				// Do not invoke the callback on links with given href
				if ( cur_link.slimstat_track_me && "undefined" != typeof cur_link.href && cur_link.href ) {
					if ( SlimStat.in_array_substring( cur_link.href, to_ignore ) ) {
						cur_link.slimstat_callback = false;
					}
					if ( SlimStat.in_array_substring( cur_link.href, to_not_track ) ) {
						cur_link.slimstat_track_me = false;
					}
				}

				// Track downloads (internal)
				if ( extensions_to_track.length > 0 && 2 == cur_link.slimstat_type && cur_link.pathname.indexOf('.') > 0 ) {
					extension_current_link = cur_link.pathname.split('.').pop().replace(/[\/\-]/g, '');
					cur_link.slimstat_track_me = SlimStat.in_array( extension_current_link, extensions_to_track );
					cur_link.slimstat_type = 1;
				}
		
				// Do not invoke the callback on links that open a new window
				if (cur_link.slimstat_track_me && cur_link.target && !cur_link.target.match(/^_(self|parent|top)$/i)){
					cur_link.slimstat_callback = false;
				}

				// Set attributes in source code
				cur_link.setAttribute("data-slimstat-tracking", cur_link.slimstat_track_me);
				cur_link.setAttribute("data-slimstat-callback", cur_link.slimstat_callback);
				cur_link.setAttribute("data-slimstat-type", cur_link.slimstat_type);

				SlimStat.add_event(cur_link, "click", function(e) {

					if (this.slimstat_track_me && !this.slimstat_actual_click){
						if (this.slimstat_callback){
							if ("function" == typeof e.preventDefault) {
								e.preventDefault();
							}
							this.slimstat_actual_click = true;
							SlimStat.ss_track(e, this.slimstat_type, "", function() {
								SlimStat.event_fire(cur_link, 'click');
							});
						}
						else{
							SlimStat.ss_track(e, this.slimstat_type, "", function() {});
						}
					}
				});
			})();
		}
    }
} );

var slimstat_data = "action=slimtrack";
if ( "undefined" != typeof SlimStatParams.id && parseInt( SlimStatParams.id ) > 0 ) {
	slimstat_data += "&op=update&id=" + SlimStatParams.id;
}
else if ( "undefined" != typeof SlimStatParams.ci ) {
	slimstat_data += "&op=add&id=" + SlimStatParams.ci + "&ref=" + SlimStat._base64_encode( document.referrer ) + "&res=" + SlimStat._base64_encode( window.location.href );
}

// Gathers all the information and sends it to the server
if ( slimstat_data.length ) {
	SlimStat.add_event( window, 'load', function(){
		setTimeout( function(){
			SlimStat.send_to_server(slimstat_data);
		}, 0 );
	} );
}