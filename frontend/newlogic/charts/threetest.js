eco.charts.threetest = function() {

  return {
    render : function() {
      var renderer = null,
      scene = null,
      camera = null,
      chart = null,
      canvas = null;
      var hAngle = Math.PI/2;
      var vAngle = Math.PI/4;
      var hRotateSpeed = 1.0;
      var vRotateSpeed = 0.5;
      var camDist = 15;
      var centre = new THREE.Vector3( 0, 0, -15 );
      var rotateStart = new THREE.Vector2();
      var rotateEnd = new THREE.Vector2();
      var rotateDelta = new THREE.Vector2();
      var chartDivisions = [];
      var data = [];
      var dataDivisions = [];
      var previousItem = 0;
      var currentItem = 0;
      //TEMPORARY: Program needs FULL colour mapping still
      var colours = [
        0x0000ff, 0x00ffff,
        0x00ff00, 0xffff00,
        0xff0000, 0xff00ff
      ];
      var selectColour = 0xeffeff;

      function updateOverlay() {
        var overlay = document.getElementById( "overlay" );
        overlay.innerHTML = "Current item: " + currentItem + "<br/>";
        var keys = Object.keys(data[currentItem]);
        var keyCount = keys.length;
        for( var i = 0; i < keyCount; i++ ) {
          overlay.innerHTML += keys[i] + ": " + data[currentItem][keys[i]] + "<br/>";
        }
      }

      function render() {
        requestAnimationFrame( render );
        renderer.render( scene, camera );
        updateOverlay();
      }

      window.onload = function() {
        data = [
          { name: "A", value: 5 },
          { name: "B", value: 6 },
          { name: "C", value: 1 },
          { name: "D", value: 4 }
        ]
        canvas = document.getElementById( "webglcanvas" );
        canvas.addEventListener( 'mousedown', onMouseDown );
        canvas.addEventListener( 'mousewheel', onMouseWheel );
        canvas.addEventListener( 'DOMMouseScroll', onMouseWheel );
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera( 45,
          canvas.width/canvas.height, 1, 4000 );
        
        renderer = new THREE.WebGLRenderer(
          { canvas: canvas, antialias: true, alpha:true } );
        renderer.setClearColor( 0xffffff, 1 );
        renderer.setSize( canvas.width, canvas.height );
        renderer.shadowMapEnabled = true;

        var spotLight = new THREE.SpotLight( 0xffffff );
        var targetpoint = new THREE.Object3D();
        spotLight.position.set( centre.x+20, centre.y+80, centre.z+70 );
        spotLight.target.position.copy( centre );
        spotLight.castShadow = true;
        spotLight.shadowCameraNear = 0.1;
        spotLight.shadowCameraFov = 45;
        spotLight.shadowDarkness = 10;
        spotLight.shadowMapWidth = 1024;
        spotLight.shadowMapHeight = 1024;
        scene.add( spotLight );

        var ambientLight = new THREE.AmbientLight( 0x222222 );
        scene.add( ambientLight );

        scene.fog = new THREE.Fog( 0xffffff, 30, 40 );

        var p_geometry = new THREE.PlaneGeometry( 100, 100 );
        var p_material = new THREE.MeshPhongMaterial( { color:0xffffff } );
        var plane = new THREE.Mesh( p_geometry, p_material );
        plane.position.copy( centre );
        plane.position.y -= 1;
        plane.rotation.x = -Math.PI/2;
        plane.castShadow = false;
        plane.receiveShadow = true;
        scene.add( plane );


        chart = createPiChart( data );
        chart.position.copy( centre );
        chart.rotation.x = Math.PI/2;
        chart.rotation.z = hAngle;
        chart.castShadow = true;
        chart.receiveShadow = false;
        scene.add( chart );
        updateCamera();
        chartDivisions[currentItem].material.color.setHex( selectColour );
        render();
      }

      function updateCurrentItem() {
        var itemCount = dataDivisions.length;
        var angle = Math.PI*2 - hAngle;
        if( angle < 3*Math.PI/2 ) angle += Math.PI/2;
        else angle -= 3*Math.PI/2;
        for( var i = 1; i < itemCount; i++ ) {
          if( angle <= dataDivisions[i] ) {
            currentItem = i-1;
            if( currentItem != previousItem ) {
              chartDivisions[currentItem].material.color.setHex( selectColour );
              chartDivisions[previousItem].material.color.set( colours[previousItem] );
              previousItem = currentItem;
            }
            return;
          }
        }
        currentItem = itemCount-1;
        if( currentItem != previousItem ) {
          chartDivisions[currentItem].material.color.setHex( selectColour );
          chartDivisions[previousItem].material.color.set( colours[previousItem] );
          previousItem = currentItem;
        }
      }

      function createPiChart( segments )
      {
        var radius = 3;
        var height = 1;
        var totalSize = 0;
        var segmentCount = segments.length;
        var thetaStart = 0;
        var thetaDelta = 0;
        var segment;
        var piChart = new THREE.Object3D();
        dataDivisions = [];
        chartDivisions = [];
        for( var i = 0; i < segmentCount; i++ ) {
          totalSize += segments[i].value;
        }
        for( var i = 0; i < segmentCount; i++ ) {
          dataDivisions.push( thetaStart );
          var factor = segments[i].value/totalSize;
          thetaDelta = Math.PI * 2 * factor;
          segment = new THREE.Mesh(
            createExtrudedSegment( radius, height+factor, thetaStart, thetaDelta ),
            new THREE.MeshPhongMaterial( { color: colours[i] } )
          );
          segment.castShadow = true;
          segment.receiveShadow = false;
          segment.position.z -= factor;
          chartDivisions.push( segment );
          piChart.add( segment );
          thetaStart += thetaDelta;
        }
        return piChart;
      }

      function getData( jsonFile ) {
        
      }

      //Notes: All parameters must be supplied, thetaLength must be less than 2*Pi
      function createExtrudedSegment( radius, height, thetaStart, thetaLength ) {
        var segments = 16 + Math.floor( thetaLength * 16 / Math.PI );
        var base = createSegmentGeometry( radius, segments, thetaStart, thetaLength );
        var extrusionSettings = {
          bevelEnabled: false,
          amount: height,
          steps: 2 };
        var segment = new THREE.ExtrudeGeometry( base, extrusionSettings );
        return segment;
      }

      //Notes: All parameters must be supplied, thetaLength must be less than 2*Pi
      function createSegmentGeometry( radius, segments, thetaStart, thetaLength ) {
        var segment = new THREE.Shape();
        segment.moveTo( 0, 0 );
        var angle;
        for ( var i = 0; i <= segments; i++ ) {
          angle = thetaStart + ( (i/segments) * thetaLength );
          var c_x = radius * Math.cos( angle );
          var c_y = radius * Math.sin( angle );
          segment.lineTo( c_x, c_y );
        }
        segment.lineTo( 0, 0 );
        return segment; 
      }

      function updateCamera() {
        var newCam = new THREE.Vector3();
        newCam.copy( centre );
        newCam.z += ( camDist * Math.cos( vAngle ));
        newCam.y += ( camDist * Math.sin( vAngle ));
        camera.position.copy( newCam );
        camera.lookAt( centre );
      }

      function rotateHoriz( angle ) {
        hAngle -= hRotateSpeed * angle;
        if( hAngle > 2*Math.PI ) hAngle -= Math.PI*2;
        if( hAngle < 0 ) hAngle += Math.PI*2;
        updateCurrentItem();
        chart.rotation.z = hAngle;
      }

      function rotateVert( angle ) {
        vAngle = Math.max( 0, Math.min( 2*Math.PI, vAngle + vRotateSpeed * angle ));
        updateCamera();
      }

      function onMouseDown( event ) {
        if( event.button == 0 ) {
          rotateStart.set( event.clientX, event.clientY );
          document.addEventListener( 'mousemove', onMouseMove );
          document.addEventListener( 'mouseup', onMouseUp );
        }
      }

      function onMouseWheel( event ) {
        var delta = 0;
        if( event.wheelDelta !== undefined ) {
          delta = event.wheelDelta;
        } else if( event.detail !== undefined ) {
          delta = -event.detail;
        }
        if( delta > 0 ) {
          camDist = Math.max( 10, camDist-1 );
        } else {
          camDist = Math.min( 20, camDist+1 );
        }
        updateCamera();    
      }

      function onMouseMove( event ) {
        rotateEnd.set( event.clientX, event.clientY );
        rotateDelta.subVectors( rotateEnd, rotateStart );
        var width = canvas.width;
        var height = canvas.height;
        rotateHoriz( 2 * Math.PI * rotateDelta.x / canvas.width );
        rotateVert( 2 * Math.PI * rotateDelta.y / canvas.height );
        rotateStart.copy( rotateEnd );
      }

      function onMouseUp( event ) {
        if( event.button == 0 ) {
          document.removeEventListener( 'mousemove', onMouseMove );
          document.removeEventListener( 'mouseup', onMouseUp );
        }
      }
      
    }
  }
}
