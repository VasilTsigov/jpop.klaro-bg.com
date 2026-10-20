function WAMapRef(mapObj)  {
  this.obj = mapObj;
  this.directions = false;
  this.icons = [];
  this.markers = [];
  this.addresses = [];
  this.points = [];
  this.infowindows = [];
  this.getPointByAddress = getPointByAddressFunc;
  this.openWindowByAddress = openWindowByAddressFunc;
  this.addressFailed = true;
  return this;
}

function WAMapPoint(theMarker, theAddress, theIcon, theInfoWindow)  {
  this.icon = theIcon;
  this.marker = theMarker;
  this.address = theAddress;
  this.infowindow = theInfoWindow;
  return this;
}

function openWindowByAddressFunc(value,attname)  {
  var point = this.getPointByAddress(value,attname);
  for (var x=0; x < this.infowindows.length; x++) {
      this.infowindows[x].close();
  }
  point.infowindow.open(this.obj,point.marker);
}

function getPointByAddressFunc(value,attname)  {
  if (!attname) attname = "street";
  for (var x=0; x < this.addresses.length; x++) {
    if (eval("this.addresses[x]."+attname) == value)  {
      return WAMapPoint(this.markers[x],this.addresses[x],this.icons[x],this.infowindows[x]);
    }
  }
  return false;
}

function getHTTPObject(){
  var xmlHttp;
  try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
  catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      return false;
      }
    }
  }
  return xmlHttp;
}

function searchCache(searchStr, zip) {
  var xmlDoc = null;
  var coordinates = '';
  xmlDoc = getHTTPObject();
  if(xmlDoc) {
    xmlDoc.onreadystatechange = function() {
      if(xmlDoc.readyState == 4) {
        var x = xmlDoc.responseXML.getElementsByTagName("geocode_entry");
        var geocode, id;
        searchStr = searchStr.replace(/,/g, ''); //remove commas
        for (i=0; i < x.length; i++) {
          id = x[i].getAttribute("ID");
          if (id == searchStr) {
            coordinates = x[i].getElementsByTagName("geocode")[0].childNodes[0].nodeValue;
            return coordinates;
          }
        }
      }
    }
    xmlDoc.open("GET", "../webassist/_promaps_cache/_promaps_geocache.xml", false);
    xmlDoc.send(null);
  } else {
    return '';
  }
  return '';
}

function addToCache(fullAddress,zip, geocode){
  httpObject = getHTTPObject();
  if (httpObject != null) {
    httpObject.open("POST", "../webassist/google_javascript/promaps_geocache_php.php", true);
    httpObject.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    var entryId = fullAddress.replace(/,/g, '');
    if (zip != '') {
      fullAddress = fullAddress.replace(zip+',', '');
    }
    entryId = escape(entryId);
    fullAddress = escape(fullAddress);
    geocode = escape(geocode);
    httpObject.send("sender=promapsforgoogle&action=add&entryId="+entryId+"&address="
+fullAddress+"&geocode="+geocode);
  }
}

var map1 = false;

function initMap_1(lat,lng)  {
  // specify map options
  /*  
The Zoom controls:
	SMALL displays a mini-zoom control, consisting of only + and - buttons. This style is appropriate for small maps. On touch devices, this control displays as + and - buttons that are responsive to touch events.
    LARGE displays the standard zoom slider control. On touch devices, this control displays as + and - buttons that are responsive to touch events.
    DEFAULT picks an appropriate zoom control based on the map's size and the device on which the map is running.

The MapType control:
    HORIZONTAL_BAR displays the array of controls as buttons in a horizontal bar as is shown on Google Maps.
    DROPDOWN_MENU displays a single button control allowing you to select the map type via a dropdown menu.
    DEFAULT displays the "default" behavior, which depends on screen size and may change in future versions of the API

Map Types:	
	ROADMAP displays the normal, default 2D tiles of Google Maps.
    SATELLITE displays photographic tiles.
    HYBRID displays a mix of photographic tiles and a tile layer for prominent features (roads, city names).
    TERRAIN displays physical relief tiles for displaying elevation and water features (mountains, rivers, etc.).
  */
  var myOptions = {
          center: new google.maps.LatLng(lat, lng),
          zoom: 13,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          panControl: true,
          zoomControl: true,
          mapTypeControl: true,
          scaleControl: true,
          streetViewControl: true,
          overviewMapControl: true,
          mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DEFAULT
          },
          zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE
          }
  };
   // draw the map
   map = new google.maps.Map(document.getElementById('wagmp_map_1'), myOptions);

  // declare WA custom object to keep track of points
  wagmp_map_1_obj = new WAMapRef(map);
  return map;
}

function wagmp_add_marker_1(point,infowindow,icon,address,isDefault,fromAddress)  {
  // create the map marker
  var marker = new google.maps.Marker({
    position: point,
    shadow: icon.shadow,
    icon: icon.image,
    shape: icon.shape,
    title: address.full
  });
  // create the map if it hasn't been created yet;
  if (!map1) map1 = initMap_1(point.lat(),point.lng());
  
  // add the marker to the map
  marker.setMap(map1);
  google.maps.event.addListener(marker, 'click', function() {
	for (var x=0; x < wagmp_map_1_obj.infowindows.length; x++) {
		wagmp_map_1_obj.infowindows[x].close();
	}
    infowindow.open(map1,marker);
  });
  // open info window for default address
  if (isDefault) {
	  infowindow.open(map1,marker);
      setTimeout("google.maps.event.trigger(wagmp_map_1_obj.markers[" + wagmp_map_1_obj.markers.length + "], 'click')",1000);
	  //map1.setCenter(point);
  }
  // record marker data to WA object
  wagmp_map_1_obj.markers.push(marker);
  wagmp_map_1_obj.addresses.push(address);
  wagmp_map_1_obj.icons.push(icon);
  wagmp_map_1_obj.points.push(point);
  wagmp_map_1_obj.infowindows.push(infowindow);
}

function wagmp_map_1() {
  // return if display div is not on the page
  if(!document.getElementById('wagmp_map_1')) return false;
  
  // define the from address
  var fromAddress = {
    enabled: false,
    street: '',
    city: '',
    state: '',
    zip: '',
    country: '',
    full: ''
  };
  // define the addresses    
  var defaultIndex = 0;
  var loopIndex = 0;
  var address = new Array();
  var icons = new Array();
  
  address[loopIndex] = {
      street: 'Kliment Ohridski 10',
      city: 'Sofia',
      state: 'Sofia',
      zip: '',
      country: 'Bulgaria',
      infowindow: 'default',
      infowindowtext: '<span style="font: 12px Verdana, Arial, Helvetica, sans-serif; color: black;"><?php echo str_replace("'", "\'", "<strong>Address:</strong><br />Kliment Ohridski 10<br />Sofia, Sofia  Bulgaria"); ?></span>',
      full: 'Kliment Ohridski 10, Sofia, Sofia, Bulgaria',
      addressType: 'address',
      isdefault: true,
      loop: '',
      latitude: '',
      longitude: '',
      markerStyle: 'Google Traditional (pillow)',
      markerColor: 'Pacifica'
  };
  
  if (address[loopIndex].isdefault) defaultIndex = loopIndex;
  
  icons[loopIndex] = {
	  image: new google.maps.MarkerImage('../webassist/google_javascript/images/traditionalpillow_pacifica.png', new google.maps.Size(34,35), new google.maps.Point(0,0), new google.maps.Point(9,33)),
	  shadow: new google.maps.MarkerImage('../webassist/google_javascript/images/traditionalpillow_shadow.png', new google.maps.Size(34,35), new google.maps.Point(0,0), new google.maps.Point(9,33)),
	  shape: {
        coord: [1, 1, 1, 20, 18, 20, 18 , 1],
        type: 'poly'
      }
  }
  
  loopIndex++;

 // only init geocoder when necessary
  var geocoder = false;
  for (var addressIndex=0; addressIndex < address.length; addressIndex++)  {
    // init info window
    var infowindow = new google.maps.InfoWindow({
      content: address[addressIndex].infowindowtext
    });
    // search cache for latitude and longitude if not already using coordinates
    if (address[addressIndex].addressType == "coordinates") {
      var cacheReturn = '';
    } else {
      var cacheReturn = searchCache(address[addressIndex].full, address[addressIndex].zip);
    }
    // if we have the latitude and longitude
    if (cacheReturn != "" || (address[addressIndex].addressType == "coordinates")) {
      if(address[addressIndex].addressType == "coordinates") {
        var latitude = address[addressIndex].latitude;
        var longitude = address[addressIndex].longitude;
      } else {
        var latitude = cacheReturn.substring(1,cacheReturn.indexOf(",")-1);
        var longitude = cacheReturn.substring(cacheReturn.indexOf(",")+1,(cacheReturn.length)-1);
      }
      // create the map point
      var point = new google.maps.LatLng(latitude,longitude);
      // add the marker
      wagmp_add_marker_1(point,infowindow,icons[addressIndex],address[addressIndex],(addressIndex == defaultIndex),fromAddress);
    } else {
      // is an address that needs to be geocoded load geocoder if not already loaded
      if (!geocoder) geocoder = new google.maps.Geocoder();
        // find lat and long and add the point to the map
        doGeocode_1(geocoder,point,infowindow,icons[addressIndex],address,addressIndex,defaultIndex,fromAddress);
    }
  }
}

function doGeocode_1(geocoder,point,infowindow,icon,address,addressIndex,defaultIndex,fromAddress)  {
  // get lat and long
  geocoder.geocode( {'address': address[addressIndex].full }, function(point, status) {
    if (status == "OK")  {
      // return function for geocode declares the point based on the returned lat and long
      var point = new google.maps.LatLng(point[0]['geometry']['location']['lat'](),point[0]['geometry']['location']['lng']());
      // add the returned point to the cache
      addToCache(address[addressIndex].full, '', point);
      // add the marker
      wagmp_add_marker_1(point,infowindow,icon,address[addressIndex],(addressIndex == defaultIndex),fromAddress);
    }  else  {
      var noZipAddress = address[addressIndex].full.replace((address[addressIndex].zip+','), '');
      geocoder.geocode( {'address': noZipAddress }, function(point, status) {
        if (status == "OK")  {
          // return function for geocode declares the point based on the returned lat and long
          var point = new google.maps.LatLng(point[0]['geometry']['location']['lat'](),point[0]['geometry']['location']['lng']());
          // add the returned point to the cache
          addToCache(address[addressIndex].full, '', point);
          // add the marker
          wagmp_add_marker_1(point,infowindow,icon,address[addressIndex],(addressIndex == defaultIndex),fromAddress);
        }
      });
    }
  });
}