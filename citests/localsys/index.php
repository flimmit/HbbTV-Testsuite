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


window.onload = function() {
  menuInit();
  initApp();

  registerMenuListener(function(liid) {
    if (liid=='exit') {
      document.location.href = '../index.php';
    } else {
      console.log('run : ' , liid);
      runStep(liid);
    }
  });
  setInstr('');
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
  setInstr('Executing step...');


  showStatus(true, '');

  if (countdownTimeout) {
    clearTimeout(countdownTimeout);
  }

function parseLocalSystem(locSys){
return {
            "deviceID": locSys.deviceID || '',
            "vendorName": locSys.vendorName || '',
            "modelName": locSys.modelName || '',
            "vendorName": locSys.vendorName || '',
            "familyName": locSys.familyName || '',
            "softwareVersion": locSys.softwareVersion || '',
            "serialNumber": locSys.serialNumber || '',
            "releaseVersion": locSys.releaseVersion || '',
            "majorVersion": locSys.majorVersion || '',
        }
}

function getClientIdFromLocalSys(locSys)
{
    var deviceId = locSys.deviceID || false;
    if (typeof deviceId == 'string') {
        var deviceIfnormation = deviceId.split(',');
        return deviceIfnormation[0];
    }
    return false;
}

function outPutLocalSystem(locSys){
  var output = "Output: <br />\n";
  var keys = Object.keys(locSys);
  for (var i = locSys.length - 1; i >= 0; i--) {
      output += keys[i] + ' : ' +  locSys[i] + "<br />\n";
  }

  if(!locSys.length){
    output += 'localSystem is empty!';
  }
  setInstr(output);

  return output;
}
if (name=='localsys') {
        try{
          setInstr('Reading localSystem ...');

        var locSys;
        var report = '';

        if (typeof oipfObjectFactory != 'undefined') {
            try {
                var oCfg = oipfObjectFactory.createConfigurationObject();
                if (typeof oCfg.localSystem == 'object') {
                    locSys = parseLocalSystem(oCfg.localSystem);
                } else {
                    locSys = {};
                }
            } catch (e) {
              showStatus(false, 'Could not read localSystem, error message: ' + e.message);
            }


        } else {
              showStatus(false, 'oipfObjectFactory not supported');
        }
        outPutLocalSystem(locSys);

        var clientId = getClientIdFromLocalSys(locSys);
        if(clientId){
            showStatus(true, 'Got client Id in localSystem: ' + clientId);
        } else {
            showStatus(false, 'Could not find the client ID in localSystem. ');
        }
        outPutLocalSystem(locSys);
      }catch(e){
            showStatus(false, 'ERROR: ' + e.message);
      }
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
  <li name="localsys" automate="ignore">Test 1: read localSystem</li>
  <li name="exit">Return to test menu</li>
</ul>
<div id="status" style="left: 700px; top: 480px; width: 400px; height: 200px;"></div>
<div id="pluginContainer"></div>
</body>
</html>
