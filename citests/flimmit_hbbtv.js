function Sas(sMsg){
    this.msgTagClientId = 81;
    this.clientId = null;
    this.response = null;
    this.getMessage(sMsg);

    return this;
}
Sas.prototype.getMessage = function(sMsg){
    if(typeof sMsg == 'string'){
        /* set message ID (81) Answerlength */
        this.message = sMsg;
        this.msgTag = sMsg.slice(0,2);
        this.length = this.hex2dec(sMsg.slice(2,6));
        this.chipId = this.hex2dec(sMsg.slice(6, (6+this.length*2)));

        if(this.msgTag == this.msgTagClientId && parseInt(this.chipId)){
           this.response = this.convertChipIdToClientId(this.chipId);
        }
        return this.response;
    }
}

Sas.prototype.hex2dec = function(number) {
  // Return error if number is not hexadecimal or contains more than ten characters (10 digits)
  if (!/^[0-9A-Fa-f]{1,10}$/.test(number)) return '#NUM!';

  // Convert hexadecimal number to decimal
  var decimal = parseInt(number, 16);

  // Return decimal number
  return (decimal >= 549755813888) ? decimal - 1099511627776 : decimal;
}
Sas.prototype.convertChipIdToClientId = function(sNumber) {
    var sNumber = (parseInt(sNumber) + 4294967296).toString();
    sNumber = sNumber.toString(),
    bLength = sNumber.length,
    bResult = 0,
    bDigit = 0;

    while(bLength > 0)
    {
        bLength--;
        bDigit = sNumber[bLength] * ( bLength % 2 + 1 );
        bResult = bResult + Math.floor(bDigit / 10) + ( bDigit % 10 );        
    }

    bDigit = bResult % 10;
    if(bDigit  ==  0){ 
        bResult = 0;
    } else {
        bResult = 10 - bDigit;
    }
    // setInstr(' sNumber: ' +  sNumber +  ' bResult: ' +  bResult + ' response: ' + sNumber + '' + bResult);

    return sNumber + '' + bResult;
}
S = {};

var DRMIds = [],
    capDRM,
    gwiObj,
    capObj;

function getDRMIds(){
  setInstr('Reading DRM ids');
    try {
        capObj = window.oipfObjectFactory.createCapabilitiesObject();
        if(window.oipfObjectFactory.isObjectSupported('application/oipfGatewayInfo') 
            && typeof window.oipfObjectFactory.createGatewayInfoObject == 'function') {
            gwiObj = window.oipfObjectFactory.createGatewayInfoObject();
            gwiObj.className = gwiObj.className + " hbbtv_plugin";
        }       
        
        setInstr('HbbTV: drm: init: hasDRM: ' + capObj.hasCapability('+DRM'));
        setInstr(capObj.xmlCapabilitie + 'found');
    } catch(e) {
        showStatus(false,'HbbTV: drm: init: createCapabilitiesObject: ERR: ' + e.message);
    }   


    try{
        var oXml = capObj.xmlCapabilities;
        var sXml = (new XMLSerializer()).serializeToString(oXml);
    } catch(e){
        showStatus(false,'reading xmlCapabilities: ERR: ' + e.message);
    }
    var foundRight = false;
    try{
        DRMIds = [];
        capDRM = capObj.xmlCapabilities.documentElement.getElementsByTagName('drm');   
        i = 0;
        for(v in capDRM){
            var sysId = capDRM[i].getAttribute('DRMSystemID');
            if (typeof capDRM[i] == 'object' && typeof sysId == 'string' && !sysId.endsWith(':0')){
                DRMIds.push(capDRM[i].getAttribute('DRMSystemID'));
            }
            i++;
        }
        setInstr('Found DRM Ids: \r\n' + DRMIds.join('\r\n'));
    } catch(e){
        showStatus(false,'HbbTV: drm: init: getDRM stuff: ERR: ' + e.message);
    }
}