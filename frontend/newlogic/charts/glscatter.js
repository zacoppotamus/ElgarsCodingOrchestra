eco.charts.scatter3d = function() {

  return {
    title: '3D Scatter Graph',

    options : {
      width : 1024,
      height : 768,
      margin : {
        top: 30,
        right: 30,
        bottom: 30,
        left: 30
      }
    },
    
    render: function( data, xValue, yValue, zValue, target ) {
      
      var options = this.options,
        width = options.width,
        height = options.height;

      var margin = {
        top: options.margin.top,
        right: options.margin.right,
        bottom: options.margin.bottom,
        left: options.margin.left
      };

      var state = { rotate: false, drag: false };
      var canvas = document.createElement("canvas");
      canvas.id = 'webglcanvas';
      canvas.height = height;
      canvas.width = width;
      canvas.addEventListener( 'mousedown', onMouseDown );
      canvas.addEventListener( 'mousewheel', onMouseWheel );
      canvas.addEventListener( 'DOMMouseScroll', onMouseWheel );
      target.appendChild( canvas );
      var centre = new THREE.Vector3( 0, 0, 0 );
      var size = 150;
      var camDist = 350,
        minCamDist = 300,
        maxCamDist = 400;
      var hAngle = Math.PI/2,
        vAngle = Math.PI/4;
      var hRotateSpeed = 1,
        vRotateSpeed = 0.5;
      var mouseStart = new THREE.Vector2(),
        mouseEnd = new THREE.Vector2(),
        mouseDelta = new THREE.Vector2();

      var overlay = document.createElement("div");
      overlay.id = 'webgloverlay';

      var dimensions = [];
      var dCount = 0;
      if( xValue != "" ) {
        dCount++;
        dimensions.push( xValue );
      }
      if( yValue != "" ) {
        dCount++;
        dimensions.push( yValue );
      }
      if( zValue != "" ) {
        dCount++;
        dimensions.push( zValue );
      }
      if( dCount == 0 ) alert( "Warning: no dimensions enabled!" );

      var scene = new THREE.Scene();
      var camera = new THREE.PerspectiveCamera( 45, 
        width/height, 1, 4000 );
      camera.position.z = 150;
      var renderer = new THREE.WebGLRenderer( 
        { canvas: canvas, antialias: true, alpha: true } );
      renderer.setClearColor( 0x444444, 1 );
      renderer.setSize( width, height );
      var graph = createScatterGraph( data, dimensions, dCount );
      updateCamera();

      scene.add( graph );
      animate();
      ////////////////////////////////////////////////////////////////////////
      ////////////////////////////////////////////////////////////////////////

      function createScatterGraph( data, dimensions, dCount ) {

        var graph = new THREE.Object3D();
        var geometry = new THREE.Geometry();

        var scale = [];
        for( var i = 0; i < dCount; i++ ) {
          scale.push( getScale( data, dimensions[i] ) );
        }

        var axis = [];
        for( var i = 0; i < dCount; i++ ) {
          var lineGeo = new THREE.Geometry();
          var origin = new THREE.Vector3();
          var end = new THREE.Vector3();
          var colour;
          if( i == 0 ) {
            colour = 0xff0000;
            end.x = size;
          } else if( i == 1 ) {
            colour = 0x0000ff;
            end.y = size;
          } else if( i == 2 ) {
            colour = 0x00ff00;
            end.z = size;
          }
          lineGeo.vertices.push( origin );
          lineGeo.vertices.push( end );
          var lineMat = new THREE.LineBasicMaterial( { color: colour } );
          var line = new THREE.Line( lineGeo, lineMat );
          axis.push( line );
        }

        var rescale = [];

        for( var i = 0; i < dCount; i++ ) {
          var range = scale[i].high;
          rescale.push( size / range );
        }

        for( var k in data ) {
          var vertex = new THREE.Vector3();
          for( var i = 0; i < dCount; i++ ) {
            if( data[k].hasOwnProperty( dimensions[i] ) ) {
              if( i == 0 ) vertex.x = data[k][dimensions[i]] * rescale[i];
              else if( i == 1 ) vertex.y = data[k][dimensions[i]] * rescale[i];
              else if( i == 2 ) vertex.z = data[k][dimensions[i]] * rescale[i];
            }
          }
          geometry.vertices.push( vertex );
        }

        var material = new THREE.ParticleSystemMaterial( { size : 1 } );
        var points = new THREE.ParticleSystem( geometry, material );

        graph.add( points );
        for( var i = 0; i < dCount; i++ ) graph.add( axis[i] );
        return graph;
      }

      function getScale( data, dimension ) {
        var size = data.length;
        var scale;
        if( size > 0 ) {
          if( data[0].hasOwnProperty( dimension ) ) {
            scale = { low: data[0][dimension], high: data[0][dimension] };
            for( var i = 1; i < size; i++ ) {
              if( data[i].hasOwnProperty( dimension ) ) {
                if( scale.low > data[i][dimension] )
                  scale.low = data[i][dimension];
                else if( scale.high < data[i][dimension] )
                  scale.high = data[i][dimension];
              }
            }
            return scale;
          }
          else {
            alert( "Invalid data." );
            return { low: 0, high: 0 };
          }
        }
        else {
          alert( "Could not generate scale: No data" );
          return { low: 0, high: 0 };
        }
      }

      function animate() {
        requestAnimationFrame( animate );
        render();
      }

      function render() {
        renderer.render( scene, camera );
      }

      function updateCamera() {
        var newCam = new THREE.Vector3();
        newCam.copy( centre );
        newCam.x += camDist * Math.cos( vAngle ) * Math.sin( hAngle );
        newCam.y += camDist * Math.sin( vAngle );
        newCam.z += camDist * Math.cos( vAngle ) * Math.cos( hAngle );
        camera.position.copy( newCam );
        camera.lookAt( centre );
      }

      function rotateHoriz( angle ) {
        hAngle -= hRotateSpeed * angle;
        if( hAngle > 2*Math.PI ) hAngle -= Math.PI*2;
        if( hAngle < 0 ) hAngle += Math.PI*2;
        updateCamera();
      }

      function rotateVert( angle ) {
        vAngle = Math.max( -Math.PI/2, Math.min( Math.PI/2, vAngle + vRotateSpeed * angle ));
        updateCamera();
      }

      function onMouseDown( event ) {
        if( event.button == 0 ) {
          state.rotate = true;
          if( !(state.drag) ) {
            mouseStart.set( event.clientX, event.clientY );
            document.addEventListener( 'mousemove', onMouseMove );
            document.addEventListener( 'mouseup', onMouseUp );
          }
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
          camDist = Math.max( minCamDist, camDist-10 );
        } else {
          camDist = Math.min( maxCamDist, camDist+10 );
        }
        updateCamera();    
      }

      function onMouseMove( event ) {
        if( state.rotate ) {
          mouseEnd.set( event.clientX, event.clientY );
          mouseDelta.subVectors( mouseEnd, mouseStart );
          var width = canvas.width;
          var height = canvas.height;
          rotateHoriz( 2 * Math.PI * mouseDelta.x / canvas.width );
          rotateVert( 2 * Math.PI * mouseDelta.y / canvas.height );
          mouseStart.copy( mouseEnd );
        }
      }

      function onMouseUp( event ) {
        if( event.button == 0 ) {
          document.removeEventListener( 'mousemove', onMouseMove );
          document.removeEventListener( 'mouseup', onMouseUp );
        }
      }
    }
  }
};

