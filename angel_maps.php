<!DOCTYPE html>
<html>
    <head>
        <script src="http://maps.googleapis.com/maps/api/js?key=API_KEY"></script>
        <title>JIRA</title>
    </head>
    <body>
        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "googlemaps";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            } 

            $sql = "SELECT * FROM markers";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $temp = (object) $result->fetch_assoc();
                $name = $temp->name;
                $address = $temp->address;
                $lat = $temp->lat;
                $lng = $temp->lng;
                $type = $temp->type;
                // output data of each row
                $arr = array();
                while ($row = $result->fetch_assoc()){
                    array_push($arr,(object) $row);
                }
            }
        ?>
        <script type="text/javascript">
            var markers = <?php echo json_encode($arr) ?>;
            var type = <?php echo json_encode($type) ?>;
            console.log(markers);
            var myUPLB=new google.maps.LatLng(markers[0].lat,markers[0].lng);

            function initialize(){
                var mapProp = {
                    center:myUPLB,
                    zoom:15,
                    mapTypeId:google.maps.MapTypeId.ROADMAP
                };

                var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
                var infowindow = new google.maps.InfoWindow();
                var polypath = [];
                for (let i = 0 ; i < markers.length ; i++){ 
                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(markers[i].lat, markers[i].lng),
                        map: map,
                        icon: './markers/' + markers[i].type + '.png'
                    });

                    if (markers[i].name === "SM City Calamba"){
                        map.setCenter(new google.maps.LatLng(markers[i].lat, markers[i].lng));
                        cityCircle = new google.maps.Circle({
                            strokeColor: '#f8bbd0',
                            strokeOpacity: 0.4,
                            strokeWeight: 2,
                            fillColor: '#cd82a4',
                            fillOpacity: 0.35,
                            map: map,
                            center: new google.maps.LatLng(markers[i].lat, markers[i].lng),
                            radius: 250
                        });
                    }

                    polypath.push(new google.maps.LatLng(markers[i].lat, markers[i].lng));
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent('<div><h3>'+markers[i].name+'</h3></div><div>'+markers[i].address+'</div>');
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }
                var flightPath = new google.maps.Polyline({
                    path: polypath.sort(),
                    geodesic: true,
                    strokeColor: '#6e3c65',
                    strokeOpacity: 1.0,
                    strokeWeight: 2
                });

                flightPath.setMap(map);
            }

            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
        <div id="googleMap" style="width:1500px;height:1500px;"></div>
    </body>
</html>