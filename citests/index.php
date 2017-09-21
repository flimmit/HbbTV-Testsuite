<?php
$ROOTDIR='..';
require("$ROOTDIR/base.php");
sendContentType();
openDocument();

?>
<script type="text/javascript">
    //<![CDATA[
    var testPrefix = "";
    var pinEntry = false, pinCode = '';
    window.onload = function() {
        initVideo();
        onMenuSelect = function() {
            document.getElementById('descr').innerHTML = opts[selected].getAttribute('descr');
        };
        menuInit();
        registerMenuListener(function(liid) {
            document.location.href = liid+'/';
        });
        registerKeyEventListener();
        initApp();
        menuSelectByName('<?php echo $referer; ?>');
        document.getElementById('relid').innerHTML = releaseinfo;
        runNextAutoTest();
    };
    function handleKeyCode(kc) {
        if (kc===VK_BLUE && !automate.timer) {
            pinEntry = !pinEntry;
            setKeyset(0x1+0x2+0x4+0x8+0x10+(pinEntry?0x100:0));
            document.getElementById("automatepin").style.display = pinEntry ? "block" : "none";
            pinCode = '';
            updatePinCode();
            return true;
        }
        if (pinEntry && kc>=VK_0 && kc<=VK_9) {
            pinCode += ''+(kc-VK_0);
            updatePinCode();
            if (pinCode.length>=4) {
                pinEntry = false;
                automate.pin = parseInt(pinCode, 10);
                setTimeout(function() {
                    document.getElementById("automatepin").style.display = "none";
                    runNextAutoTest(true);
                }, 1000);
            }
            return true;
        }
        if (pinEntry && kc===VK_BACK) {
            if (pinCode.length>0) {
                pinCode = pinCode.substring(0, pinCode.length-1);
                updatePinCode();
            } else {
                pinEntry = false;
                document.getElementById("automatepin").style.display = "none";
            }
            return true;
        }
        return false;
    }
    function updatePinCode() {
        var i, txt = '';
        for (i=0; i<4; i++) {
            if (pinCode.length<=i) {
                txt += "_ ";
            } else {
                txt += pinCode.substring(i, i+1)+" ";
            }
        }
        document.getElementById("automatepinentry").innerHTML = txt;
    }

    //]]>
</script>

</head><body>

<?php
echo videoObject();
echo appmgrObject();
?>

<div id="bgdiv" style="left: 0px; top: 0px; width: 1280px; height: 720px; background-color: #132d48;" />
<div class="txtdiv txtlg" style="left: 111px; top: 60px; width: 500px; height: 30px;">MIT-xperts HBBTV testsuite</div>
<div class="txtdiv" style="left: 111px; top: 640px; width: 500px; height: 30px;">Testsuite release: <span id="relid"></span></div>
<div style="left: 690px; top: 56px; width: 590px; height: 130px; background-color: #ffffff;">
    <div class="txtdiv" style="left: 10px; top: 4px; width: 500px; height: 30px; color: #000000;">HBBTV testsuite project initiated/maintained by:</div>
    <div class="imgdiv" style="left: 10px; top: 34px; width: 356px; height: 44px; background-image: url(logo.png);"></div>
</div>
<div class="txtdiv" style="left: 700px; top: 200px; width: 450px; height: 500px;"><u>Instructions:</u><br />
    Please select the desired test using the cursor keys, then press OK. After that, test-specific instructions will appear. More information is available under &quot;About / Imprint&quot;.<br />
    In case you have questions and/or comments, you can reach us at info&#160;&#x0040;&#160;mit-xperts&#x002e;com<br /><br />
    <u>Test description:</u><br />
    <span id="descr">&#160;</span>
</div>
<ul id="menu" class="menu" style="left: 100px; top: 100px;">
    <li name="sas" descr="Read DRM of connected CI+ module and try to get CA client Id via the CI+ SAS resource">CI+ SAS CA client Id</li>
    <li name="localsys" descr="Get Client Id from localSystem">Client Id from localSystem</li>
    <li name="exit">Return to test menu</li>
</ul>

<div id="automatepin" class="txtdiv" style="left: 400px; top: 200px; width: 440px; background-color: #000000; color: #ffffff; padding: 20px; text-align: center; font-size: 24px; line-height: 30px; display: none;">
    Enter your 4-digit automation ID<br/>(user account number):<br/><br/>
    <span id="automatepinentry">_ _ _ _</span>
</div>

</body>
</html>

