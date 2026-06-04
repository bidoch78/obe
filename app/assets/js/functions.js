////////////////////////////////////////////////////////////////////////////////
// param: Mask & Value
////////////////////////////////////////////////////////////////////////////////

window['format'] = function( m, v){
    if (!m || isNaN(+v)) {
        return v; //return as it is.
    }
    //convert any string to number according to formation sign.
    var v = m.charAt(0) == '-'? -v: +v;
    var isNegative = v<0? v= -v: 0; //process only abs(), and turn on flag.
   
    //search for separator for grp & decimal, anything not digit, not +/- sign, not #.
    var result = m.match(/[^\d\-\+#]/g);
    var Decimal = (result && result[result.length-1]) || '.'; //treat the right most symbol as decimal
    var Group = (result && result[1] && result[0]) || ',';  //treat the left most symbol as group separator
   
    //split the decimal for the format string if any.
    var m = m.split( Decimal);
    //Fix the decimal first, toFixed will auto fill trailing zero.
    v = v.toFixed( m[1] && m[1].length);
    v = +(v) + ''; //convert number to string to trim off *all* trailing decimal zero(es)

    //fill back any trailing zero according to format
    var pos_trail_zero = m[1] && m[1].lastIndexOf('0'); //look for last zero in format
    var part = v.split('.');
    //integer will get !part[1]
    if (!part[1] || part[1] && part[1].length <= pos_trail_zero) {
        v = (+v).toFixed( pos_trail_zero+1);
    }
    var szSep = m[0].split( Group); //look for separator
    m[0] = szSep.join(''); //join back without separator for counting the pos of any leading 0.

    var pos_lead_zero = m[0] && m[0].indexOf('0');
    if (pos_lead_zero > -1 ) {
        while (part[0].length < (m[0].length - pos_lead_zero)) {
            part[0] = '0' + part[0];
        }
    }
    else if (+part[0] == 0){
        part[0] = '';
    }
   
    v = v.split('.');
    v[0] = part[0];
   
    //process the first group separator from decimal (.) only, the rest ignore.
    //get the length of the last slice of split result.
    var pos_separator = ( szSep[1] && szSep[ szSep.length-1].length);
    if (pos_separator) {
        var integer = v[0];
        var str = '';
        var offset = integer.length % pos_separator;
        for (var i=0, l=integer.length; i<l; i++) {
           
            str += integer.charAt(i); //ie6 only support charAt for sz.
            //-pos_separator so that won't trail separator on full length
            if (!((i-offset+1)%pos_separator) && i<l-pos_separator ) {
                str += Group;
            }
        }
        v[0] = str;
    }

    v[1] = (m[1] && v[1])? Decimal+v[1] : "";
    return (isNegative?'-':'') + v[0] + v[1]; //put back any negation and combine integer and fraction.
};

////////////////////////////////////////////////////////////////////////////////
// param: Mask & Value
////////////////////////////////////////////////////////////////////////////////

shuffle = function(array) {
  var m = array.length, t, i;

  // While there remain elements to shuffle…
  while (m) {

    // Pick a remaining element…
    i = Math.floor(Math.random() * m--);

    // And swap it with the current element.
    t = array[m];
    array[m] = array[i];
    array[i] = t;
  }

  return array;
};

htmlconv = function(text, options) {
	
	var htmlconv = ((options && options.htmlconv) ? options.htmlconv : false);
	var convrl = ((options && options.convrl) ? options.convrl : false);

	/* possibilité de pb avec cette procédure
		http://stackoverflow.com/questions/1147359/how-to-decode-html-entities-using-jquery
		http://phpjs.org/functions/in_array/
	*/
	if (htmlconv) {
		var decoded = $("<div/>").text(txt).html();
		text = decoded;
	}
	else {
		if (convrl) {
			text = text.replace(/\r\n|\r|\n/g,'<br/>');
		}
	}
		
	return text;
			
};

urldecode = function (str) {
	return decodeURIComponent((str + '').replace(/\+/g, '%20'));
};

if (!window.location.getParameter) {
	
	window.location.getParameter = function(key) {
	
		key = key.toLowerCase();
		var q=window.location.search.substring(1);
		if(q && q.length > 2) {
		
			var params=q.split("&");
			var l=params.length;
			for (var i=0;i<l;i++) {
			
				var pair=params[i].split("=");
				if (pair[0].toLowerCase()==key) return pair[1];
			}
		}	
		
	};

}