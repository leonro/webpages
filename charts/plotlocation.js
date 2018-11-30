<!DOCTYPE html>
<html>
  <head>
    <style>
       #map {
        height: 650px;
        width: 100%;
       }
    </style>
  </head>
  <body>
    <h3>Locations of measurement positions in Austria</h3>
    <div id="map"></div>
    <script>
      var customLabel = {
        Secularvariation: {
          label: 'C',
          image: '../data/images/backgrounds/markeryellow.png' 
        },
        Variometer: {
          label: 'V',
          image: '../data/images/backgrounds/markergreen.png'
        },
        Observatory: {
          label: 'O',
          image: '../data/images/backgrounds/markerred.png'
        }
      };

      function initMap() {
        var aust = {lat: 47.7, lng: 13.5};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom:7.8,
          center: aust
        });
        var infoWindow = new google.maps.InfoWindow;

          // Change this depending on the name of your PHP or XML file
          // ../data/components/com_jumi/files/getlocation.php
          downloadUrl('../data/components/com_jumi/files/getlocation.php', function(data) {
            var xml = data.responseXML;
            //console.log("Hello");
            var markers = xml.documentElement.getElementsByTagName('marker');
            //console.log(xml);
            Array.prototype.forEach.call(markers, function(markerElem) {
              var name = markerElem.getAttribute('name');
              var id = markerElem.getAttribute('id');
              var web = markerElem.getAttribute('web');
              var type = markerElem.getAttribute('type');
              var point = new google.maps.LatLng(
                  parseFloat(markerElem.getAttribute('lat')),
                  parseFloat(markerElem.getAttribute('lng')));

              var infowincontent = document.createElement('div');
              var strong = document.createElement('strong');
              strong.textContent = name
              infowincontent.appendChild(strong);
              infowincontent.appendChild(document.createElement('br'));

              var text = document.createElement('text');
              //text.textContent = id
              //infowincontent.appendChild(text);
              //infowincontent.appendChild(document.createElement('br'));

              var link = document.createElement('a');
              var linkText = document.createTextNode(id);
              link.appendChild(linkText);
              link.title = id;
              link.href = web;
              infowincontent.appendChild(link);

              var icon = customLabel[type] || {};
              var marker = new google.maps.Marker({
                map: map,
                position: point,
                label: icon.label,
                icon: icon.image
              });
              marker.addListener('click', function() {
                infoWindow.setContent(infowincontent);
                infoWindow.open(map, marker);
              });
            });
          });
        }



      function downloadUrl(url, callback) {
        var request = window.ActiveXObject ?
            new ActiveXObject('Microsoft.XMLHTTP') :
            new XMLHttpRequest;

        request.onreadystatechange = function() {
          if (request.readyState == 4) {
            request.onreadystatechange = doNothing;
            callback(request, request.status);
          }
        };

        request.open('GET', url, true);
        request.send(null);
      }

      function doNothing() {}
    </script>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCrQrRtvCzka1hJW6KEvvSD8-PAk4z-oCk&callback=initMap">
    </script>
  </body>
</html>
