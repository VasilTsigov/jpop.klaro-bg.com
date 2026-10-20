function WAMapRef(mapObj)  {
  this.obj = mapObj;
  this.directions = false;
  this.icons = [];
  this.markers = [];
  this.addresses = [];
  this.points = [];
  this.getPointByAddress = getPointByAddressFunc;
  this.addressFailed = true;
  return this;
}

function WAMapPoint(theMarker, theAddress, theIcon)  {
  this.icon = theIcon;
  this.marker = theMarker;
  this.address = theAddress;
  return this;
}

function getPointByAddressFunc(value,attname)  {
  if (!attname) attname = "street";
  for (var x=0; x < this.addresses.length; x++) {
    if (eval("this.addresses[x]."+attname) == value)  {
      return WAMapPoint(this.markers[x],this.addresses[x],this.icons[x]);
    }
  }
  return false;
}

function getHTTPObject(){   
  if (window.ActiveXObject)        
    return new ActiveXObject("Microsoft.XMLHTTP");   
  else if (window.XMLHttpRequest)        
    return new XMLHttpRequest();   
  else {
    return null;
  }
}

function searchCache(searchStr, zip) {
	var xmlDoc = null;
  if (window.ActiveXObject) {//IE
    xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
  } else if(navigator.userAgent && navigator.userAgent.toLowerCase().indexOf('webkit') >= 0) { //webkit (Safari, Chrome)
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
      };
      xmlDoc.open("GET", "_promaps_cache/_promaps_geocache.xml", false);
      xmlDoc.send(null);
    }
    
    return coordinates;
  } else if (document.implementation.createDocument) {//gecko (Mozilla, Firefox, Opera)
    xmlDoc=document.implementation.createDocument("","",null);
  } else {
    return '';
  }
  if (xmlDoc != null) {
    xmlDoc.async=false;
    try {
      if(xmlDoc.load("_promaps_cache/_promaps_geocache.xml")) {
        var x=xmlDoc.getElementsByTagName("geocode_entry");
        var geocode, id;
        searchStr = searchStr.replace(/,/g, ''); //remove commas
        for (i=0; i < x.length; i++) {
          id = x[i].getAttribute("ID");
          if (id == searchStr) {
            return x[i].getElementsByTagName("geocode")[0].childNodes[0].nodeValue;
          }
        } 
      } 
    }
    catch(err) {
      return '';
    }
  }
  return '';
}

function addToCache(fullAddress,zip, geocode){
  httpObject = getHTTPObject();
  if (httpObject != null) {
    httpObject.open("POST", "google_javascript/promaps_geocache_php.php", true);
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


function wagmp_map_1() {
  if(GBrowserIsCompatible()) {
    if(!document.getElementById('wagmp_map_1')) return false;
    var map = new GMap2(document.getElementById('wagmp_map_1'));
    wagmp_map_1_obj = new WAMapRef(map);
    map.enableContinuousZoom();
    map.enableDoubleClickZoom();
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    var geocoder = new GClientGeocoder();
    
    var fromAddress = {
      enabled: false,
      street: '',
      city: '',
      state: '',
      zip: '',
      country: '',
      full: ''
    };

    var icon_0 = new GIcon();
    icon_0.image = 'google_javascript/images/traditionalflat_pacifica.png';
    icon_0.shadow = 'google_javascript/images/traditionalflat_shadow.png';
    icon_0.iconSize = new GSize(34,35);
    icon_0.shadowSize = new GSize(34,35);
    icon_0.iconAnchor = new GPoint(9,33);
    icon_0.infoWindowAnchor = new GPoint(19,0);
    icon_0.printImage = 'google_javascript/images/traditionalflat_pacifica.gif';
    icon_0.mozPrintImage = 'google_javascript/images/traditionalflat_pacifica_mozprint.png';
    icon_0.printShadow = 'google_javascript/images/traditionalflat_shadow.gif';
    icon_0.transparent = 'google_javascript/images/traditionalflat_pacifica_transparent.png';

    var address_0 = {
      street: '10, Kliment Ohridski blvd.',
      city: 'Sofia',
      state: '',
      zip: '',
      country: 'Bulgaria',
      infowindow: 'default',
      infowindowtext: '<span style="font: 12px Verdana, Arial, Helvetica, sans-serif; color: black;"><?php echo str_replace("'", "\'", "<strong>Address:</strong><br />10, Kliment Ohridski blvd.<br />Sofia   Bulgaria"); ?></span>',
      full: '10, Kliment Ohridski blvd., Sofia, Bulgaria',
      isdefault: true,
      addressType: 'address',
      loop: '',
      latitude: '',
      longitude: '',
      markerStyle: 'Google Traditional (flat)',
      markerColor: 'Pacifica'
    };
    
    if (address_0.addressType == "coordinates") {
      var cacheReturn = '';
    } else {
      var cacheReturn = searchCache(address_0.full, address_0.zip);
    }
    if (cacheReturn != "" || (address_0.addressType == "coordinates")) {
      if(address_0.addressType == "coordinates") {
        var latitude = address_0.latitude;
        var longitude = address_0.longitude;
      } else {
        var latitude = cacheReturn.substring(1,cacheReturn.indexOf(",")-1);
        var longitude = cacheReturn.substring(cacheReturn.indexOf(",")+1,(cacheReturn.length)-1);
      }
      var point = new GLatLng(latitude, longitude);
      if (point && !isNaN(latitude)) {
        wagmp_map_1_obj.addressFailed = false;
        var marker_0 = new GMarker(point, icon_0);
        GEvent.addListener(marker_0, 'click', function() {
          marker_0.openInfoWindowHtml(address_0.infowindowtext);
        });
        if(!fromAddress.enabled || 'address_0' != 'address_0') {
          if('address_0' == 'address_0')
          map.setCenter(point, 13);
          map.addOverlay(marker_0);
if('address_0' == 'address_0')
            marker_0.openInfoWindowHtml(address_0.infowindowtext);
        }
          wagmp_map_1_obj.markers.push(marker_0);
          wagmp_map_1_obj.addresses.push(address_0);
          wagmp_map_1_obj.icons.push(icon_0);
          wagmp_map_1_obj.points.push(point);
        } else {
        if (wagmp_map_1_obj.addressFailed) {
          map.setCenter(new GLatLng(30, -98), 3);
        }
        wagmp_map_1_obj.addressFailed = true;
      }
    } else if (address_0.addressType == "address") {
      geocoder.getLatLng (
      address_0.full,
      function(point) {
        if(point) {
          this.addressFailed = false;
          addToCache(address_0.full, '', point);
          var marker_0 = new GMarker(point, icon_0);
          GEvent.addListener(marker_0, 'click', function() {
            marker_0.openInfoWindowHtml(address_0.infowindowtext);
          });
          if(!fromAddress.enabled || 'address_0' != 'address_0') {
            if('address_0' == 'address_0')
            map.setCenter(point, 13);
            map.addOverlay(marker_0);
if('address_0' == 'address_0')
            marker_0.openInfoWindowHtml(address_0.infowindowtext);
          }
              wagmp_map_1_obj.markers.push(marker_0);
          wagmp_map_1_obj.addresses.push(address_0);
          wagmp_map_1_obj.icons.push(icon_0);
          wagmp_map_1_obj.points.push(point);
        
        } else {
          var noZipAddress = address_0.full.replace((address_0.zip+','), '');
          geocoder.getLatLng (noZipAddress,
            function(point) {
              if(point) {
                wagmp_map_1_obj.addressFailed = false;
                addToCache(address_0.full, address_0.zip, point);
                var marker_0 = new GMarker(point, icon_0);
                GEvent.addListener(marker_0, 'click', function() {
                  marker_0.openInfoWindowHtml(address_0.infowindowtext);
                });
                if (!fromAddress.enabled || 'address_0' != 'address_0') {
                  if('address_0' == 'address_0')
                  map.setCenter(point, 13);
                  map.addOverlay(marker_0);
if('address_0' == 'address_0')
            marker_0.openInfoWindowHtml(address_0.infowindowtext);
                }
                    wagmp_map_1_obj.markers.push(marker_0);
          wagmp_map_1_obj.addresses.push(address_0);
          wagmp_map_1_obj.icons.push(icon_0);
          wagmp_map_1_obj.points.push(point);
        
              } else {
                if (wagmp_map_1_obj.addressFailed) {
                  map.setCenter(new GLatLng(30, -98), 3);
                }
                  wagmp_map_1_obj.addressFailed = true;
              }
            }
          );
        }
      }
    );
  }


  }
}