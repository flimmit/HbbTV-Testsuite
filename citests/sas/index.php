<?php
$ROOTDIR='../../';
require("$ROOTDIR/base.php");
sendContentType();
openDocument();
$n = rand();
echo "<script type=\"text/javascript\" src=\"../flimmit_hbbtv.js?n=$n\"></script>\n";

?>
<script type="text/javascript">
//<![CDATA[
var testPrefix = <?php echo json_encode(getTestPrefix()); ?>;

var drmObj;

function getDrmObj() {
  if(!drmObj){
        try {

          if (window.oipfObjectFactory.isObjectSupported('application/oipfDrmAgent')) {
              // console.log('Factory: oipfDrmAgent: supported');
              drmObj = window.oipfObjectFactory.createDrmAgentObject();
              drmObj.className = drmObj.className + " hbbtv_plugin";
          } else {
              // console.log('Factory: oipfDrmAgent: NOT supported');
              drmObj = document.getElementById('drmObject');
          }
      } catch (e) {
          // console.log('MM: error on HBBTV init: '  + e.message );
      }
    }
    return drmObj;
}
window.onload = function() {
  menuInit();
  initApp();

  // try{
  //   var a = app.createApplication('http://simplitv.tannhaeuser.peko/oldapp/simplilauncher/index.html');
  //   a.show();
  // }catch(e){
  //     setStatus(false, 'ERROR: ' + e.message);
  // }



  // S.HbbTV.init();


  registerMenuListener(function(liid) {
    if (liid=='exit') {
      document.location.href = '../index.php';
    } else {
      console.log('run : ' , liid);
      runStep(liid);
    }
  });
  setInstr('Test 1 must have been successful, before Test 2 can be started');
  try {
    document.getElementById('video').bindToCurrentChannel();
  } catch (ignore) {
  }
  runNextAutoTest();
};
var countdownTimeout = null;
function countDown(secs, msg, runfunc) {
  countdownTimeout = null;
  setInstr('In '+secs+' seconds, '+msg);
  if (secs<=0) {
    runfunc();
    return;
  }
  secs--;
  countdownTimeout = setTimeout(function() {countDown(secs, msg, runfunc);}, 1000);
}


function runStep(name) {


  showStatus(true, '');

  if (countdownTimeout) {
    clearTimeout(countdownTimeout);
  }


if(name=='readDRM'){
    setInstr('Sending DRM message to request the Client ID ...');
    try {  
      getDRMIds();
    }catch(e){
        showStatus(false, 'Error occured - could not read DRM ids. (' + e.message + ')');
    }
} else if (name=='clientid') {
      setInstr('Sending DRM requests for Client IDs...');
      var succss = false;

      var reqId = 0;
      if (!DRMIds || DRMIds.length < 1) {
        showStatus(true,'No CA system id\'s available!'); 
        return;
      }

      try{
          getDrmObj().onDRMMessageResult = function(r,a,b) {
            try {
                if(b == 0){
                    var s = new Sas(a);
                    showStatus(true, 'Got clientID: ' + s.response);        
                    callResults[lastCallDRM] = s.response;
                } else { //error
                    showStatus(true, 'failed !');        
                    callResults[lastCallDRM] = 'fail!';
                }
            } catch(e) {
                showStatus(false, 'Could not understand: ' + r + 'a: ' + a + ' b' + b + ' ERROR : ' + e.message);
                callResults[lastCallDRM] = 'fail!';
            }
            printCallResults();
            tryCreateSasObject(reqId++);
          };
          getDrmObj().onDRMSystemMessage = function(m, DRMSystemID){ setIntr('DRM message: ' + m);};
          getDrmObj().onDRMSystemStatusChange = function(DRMSystemID){ setIntr('DRM system ID: ' + DRMSystemID);};
        }catch(e) {
            showStatus(false, 'Error occured - could not bind callbacks to DRM agent' + e.message);
            // getDrmObj().addEventListener('onDRMMessageResult', onDrmResult);
        } 


        //func to start async drm call
        var tryCreateSasObject = function(idx) {
            if (idx >= DRMIds.length) {
                printCallResults();
                return;
            }
            lastCallDRM = DRMIds[idx];
            try {
                showStatus(false, 'sending message for (' + lastCallDRM + ')..');
                getDrmObj().sendDRMMessage("application/vnd.oipf.cspg-hexbinary", '81', lastCallDRM);
                showStatus(false, 'DRM message sent. Waiting for response..');
            } catch (e) {
                callResults[lastCallDRM] = 'fail!';
                showStatus(false, 'Error occured - could not send DRM Message. (' + e.message + ')');
                printCallResults();
            }
        };

        var printCallResults = function() {
            setInstr(
                'Status Results: <br>' + callResults.length + 
                (callResults.map(function(val, id) {
                    return id + '...' + val;
                }).join('<br>'))
            );
        }

        //initial call
        var lastCallDRMId = '';
        var callResults = [];
        reqId = 0;
        tryCreateSasObject(reqId);



      // if (succss) {
      //   showStatus(true, 'Starting application...');
      //   app.destroyApplication();
      // } else {
      //   showStatus(false, 'Starting application via appmgr failed');
      // }
  }
}

//]]>
</script>

</head><body>

<div id="bgdiv" style="left: 0px; top: 0px; width: 1280px; height: 720px; background-color: #132d48;" />

<?php echo appmgrObject(); ?>
<?php echo videoObject(700, 590, 160, 90); ?>
<object type="application/oipfDrmAgent" id="drmObject" width="0" height="0"></object>

<div class="txtdiv txtlg" style="left: 110px; top: 60px; width: 500px; height: 30px;">MIT-xperts HBBTV tests</div>
<div id="instr" class="txtdiv" style="left: 700px; top: 110px; width: 400px; height: 360px;"></div>
<ul id="menu" class="menu" style="left: 100px; top: 100px;">
  <li name="readDRM" automate="ignore">Test 1: read DRM</li>
  <li name="clientid" automate="ignore">Test 2: get Client ID</li>
  <li name="exit">Return to test menu</li>
</ul>
<div id="status" style="left: 700px; top: 480px; width: 400px; height: 200px;"></div>
<div id="pluginContainer"></div>
</body>
</html>
