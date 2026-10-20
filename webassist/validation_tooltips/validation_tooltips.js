/* version 1.0.0 */
var WA_SpryValidationTooltipObjects = new Array();

function doResize()  {
  WA_SpryValidationTooltipObjects[0].onresize();
}

function WA_SpryValidationTooltip(arrowHeight,arrowOffset,tipOffset,arrowPointer,arrowPlacement,border,roundedness,bordercolor,bgcolor,textcolor,closecolor,opacity,spryObject) {	
	this.version = '1.0.0';
	this.applied = false;
    this.arrowHeight = arrowHeight;
    this.arrowOffset = arrowOffset;
    this.tipOffset = tipOffset;
    this.arrowPointer = (arrowPlacement == 'right') ? 'center' : arrowPointer;
    this.arrowPlacement = arrowPlacement;
    this.border = border;
    this.roundedness = roundedness;
    this.bordercolor = bordercolor;
    this.bgcolor = bgcolor;
    this.textcolor = textcolor;
    this.closecolor = closecolor;
    this.opacity = opacity;
    this.spryObject = spryObject || false;
	this.repositionTip = false;
	this.refreshText = false;
	this.isReversed = (arrowPlacement.indexOf("bottom") >= 0 || arrowPlacement == "right");
	this.isBottom = (arrowPlacement.indexOf("bottom") >= 0);
	this.isTop = (arrowPlacement.indexOf("top") >= 0);
	this.isLeft = (arrowPlacement == "left");
	this.isRight = (arrowPlacement == "right");
	this.borderPattern = (this.isRight || this.isLeft)?"x 0 y 0":"0 x 0 y";

	var self = this;
	
	this.init = function() {
		if(this.canInit()){
			this.createTooltips();
		}
		else{
			var tryagain = setTimeout(function(){ self.init(); },100);
		}
	};
	
	this.canInit = function(){

		var onloadDidFire = false;
		
		if(typeof Spry !== 'undefined' && Spry.Widget ){
			var hasSpryForm = Spry.Widget.Form;
			var hasWidgetQueueElements = Spry.Widget.Form.onSubmitWidgetQueue && Spry.Widget.Form.onSubmitWidgetQueue[0] && Spry.Widget.Form.onSubmitWidgetQueue[0].element;
			var validations = {};
			
			// Check that any present validations have triggered their onload events, as in IE8 in some circumstances select or textarea validations were applied after tooltips were applied
			if(Spry.Widget.ValidationTextarea){
				validations.ValidationTextarea = Spry.Widget.ValidationTextarea.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationConfirm){
				validations.ValidationConfirm = Spry.Widget.ValidationConfirm.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationPassword){
				validations.ValidationPassword = Spry.Widget.ValidationPassword.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationTextField){
				validations.ValidationTextField = Spry.Widget.ValidationTextField.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationSelect){
				validations.ValidationSelect = Spry.Widget.ValidationSelect.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationCheckbox){
				validations.ValidationCheckbox = Spry.Widget.ValidationCheckbox.onloadDidFire;
			}
			
			if(Spry.Widget.ValidationRadio){
				validations.ValidationRadio = Spry.Widget.ValidationRadio.onloadDidFire;
			}
			
			var allFired = true;
			
			for(var x in validations){
				allFired = validations[x];
				if(!allFired){
					break;
				}
			}
			
			onloadDidFire = hasSpryForm && hasWidgetQueueElements && allFired;
		}
		
		return onloadDidFire;
	};
	
	this.onresize = function()  {
		  if (this.repositionTip) clearTimeout(this.repositionTip);
		  if (this.refreshText) clearTimeout(this.refreshText);
		  this.repositionTip = setTimeout(function(){ self.reposition(); },100);
		  this.refreshText = setTimeout(function(){ self.reposition(); },500); // work around for Chrome not handling resize without doubling this up.
	}
	this.addEventListener = function(element, eventType, handler, capture){
		try{
			if (element.addEventListener){
				element.addEventListener(eventType, handler, capture);
			}
			else if (element.attachEvent){
				element.attachEvent("on" + eventType, handler, capture);
			}
		}
		catch (e) {}
	};

	this.addEventListener(window,"load",function(){ self.init(); });
	this.createTooltips = function()  {
	  this.buildTooltips();
	  WA_SpryValidationTooltipObjects[WA_SpryValidationTooltipObjects.length] = this;
	  this.addEventListener(window,"resize",doResize);
	};
	
	this.hasRelativeParent = function(theSpryInput){
		var relativeParentPresent = false;
		var ele = theSpryInput;
		
		while(ele.parentNode){
			var positioning = ele.currentStyle['position'];
			if(positioning && positioning.toLowerCase() == 'relative'){
				relativeParentPresent = true;
				break;
			}
			ele = ele.parentNode;
		}
		return relativeParentPresent;
	}
	
	this.BrowserSniff = function(){
		var b = navigator.appName.toString();
		var up = navigator.platform.toString();
		var ua = navigator.userAgent.toString();
	
		this.mozilla = this.ie = this.opera = this.safari = false;
		var re_opera = /Opera.([0-9\.]*)/i;
		var re_msie = /MSIE.([0-9\.]*)/i;
		var re_gecko = /gecko/i;
		var re_safari = /(applewebkit|safari)\/([\d\.]*)/i;
		var r = false;
	
		if ( (r = ua.match(re_opera))) {
			this.opera = true;
			this.version = parseFloat(r[1]);
		} else if ( (r = ua.match(re_msie))) {
			this.ie = true;
			this.version = parseFloat(r[1]);
		} else if ( (r = ua.match(re_safari))) {
			this.safari = true;
			this.version = parseFloat(r[2]);
		} else if (ua.match(re_gecko)) {
			var re_gecko_version = /rv:\s*([0-9\.]+)/i;
			r = ua.match(re_gecko_version);
			this.mozilla = true;
			this.version = parseFloat(r[1]);
		}
		this.windows = this.mac = this.linux = false;
	
		this.Platform = ua.match(/windows/i) ? "windows" :
						(ua.match(/linux/i) ? "linux" :
						(ua.match(/mac/i) ? "mac" :
						ua.match(/unix/i)? "unix" : "unknown"));
		this[this.Platform] = true;
		this.v = this.version;
	
		if (this.safari && this.mac && this.mozilla) {
			this.mozilla = false;
		}
	};
	
	this.is = new this.BrowserSniff();
	
	this.findTipPosition = function(theSpryInput) {
	  var theInputPos = this.findPos(theSpryInput);
	  var theFullPos = this.findPagePos(theSpryInput);
	  if( !this.is.ie || ( this.is.ie && this.is.v > 8) || this.hasRelativeParent(theSpryInput) ) theFullPos = theInputPos;
	  var xpos = (theFullPos[0]);
	  var ypos = (theFullPos[1]);
	  var tipOffset = this.tipOffset;
	  var arrowOffset = this.arrowOffset;
	  var arrowHeight = this.arrowHeight;
	  
	  if(this.arrowPlacement == 'right'){
		  xpos += theSpryInput.offsetWidth;
	  }
	  else{
		  if (this.arrowPlacement.indexOf("center") > 0){
			  xpos += parseInt(theSpryInput.offsetWidth/2);
		  }
			  
		  if (this.arrowPlacement.indexOf("right") > 0) {
			  xpos += theSpryInput.offsetWidth;
		  }
		  
		  switch(this.arrowPointer){
			  case 'slant-left':
				xpos -= ( arrowOffset - tipOffset + this.border);
				break;
				
			  case 'slant-right':
				xpos -= (2*arrowHeight)+(this.border) + ( arrowOffset - tipOffset );
				break;
				
			  case 'center':
				xpos -= (arrowHeight + arrowOffset - tipOffset);
				break;
		  }
	  }
	  
	  return [xpos, ypos];
	};
	
	this.buildTooltips = function() {
	  if(typeof Spry !== 'undefined' && Spry && Spry.Widget && Spry.Widget.Form &&  Spry.Widget.Form.onSubmitWidgetQueue){
		  var queue = Spry.Widget.Form.onSubmitWidgetQueue;
		for(var x =0; x < queue.length; x++){
		  if (queue[x].element && (queue[x].selectElement || queue[x].input || queue[x].checkboxElements || queue[x].radioElements) && (!this.spryObject || this.spryObject.element == queue[x].element) )  {
			var theSpryDiv = queue[x].element;
			var nestedSpans = theSpryDiv.getElementsByTagName("span");

			if (queue[x].input) var theSpryInput = queue[x].input;
			if (queue[x].selectElement) var theSpryInput = queue[x].selectElement;
			if (queue[x].checkboxElements) var theSpryInput = queue[x].checkboxElements[0];
			if (queue[x].radioElements) var theSpryInput = queue[x].radioElements[0];
			queue[x].toolTipCreated = true;
			queue[x].toolTipElement = theSpryInput;
			queue[x].toolTip = this;
			queue[x].toolTipSpans = new Array();
			queue[x].toolTipCloses = new Array();
			  
			var thePosition = this.findTipPosition(theSpryInput);
			var leftPos = thePosition[0];
			var ypos = thePosition[1];
			for (var z=0; z<nestedSpans.length; z++)  {
			  var nestedSpan = nestedSpans[z];
			  var nestedSpanClass = nestedSpan.className;
			  if (!nestedSpanClass || (nestedSpanClass.indexOf("Msg") == -1 && nestedSpanClass.indexOf("serverInvalidState")) == -1) continue;
			  nestedSpan.style.border = "0";
			  var childNodes = nestedSpan.childNodes;
			  var errorFloat = document.createElement("div");
			  errorFloat.className = "WA_SpryValidationTooltip_errorFloat";
			  if (this.opacity) errorFloat.style.cssText = 'opacity: '+(this.opacity/100)+'; filter: alpha(opacity='+this.opacity+'); -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity='+this.opacity+')"';
			  errorFloat.style.position = 'absolute';
			  errorFloat.style.zIndex = '9999';
			  var errorWrapper = document.createElement("div");
			  errorWrapper.className = "WA_SpryValidationTooltip_errorWrapper";
			  if (this.roundedness){
				  errorWrapper.style.cssText = '-moz-border-radius: '+this.roundedness+'px; -webkit-border-radius: '+this.roundedness+'px; border-radius: '+this.roundedness+'px '+this.roundedness+'px '+this.roundedness+'px '+this.roundedness+'px; -webkit-background-clip: padding-box; background-clip: padding-box;';
			  }
			  errorWrapper.style.borderColor = this.bordercolor;
			  errorWrapper.style.borderWidth = (this.border) ? this.border+"px" : 0;
			  errorWrapper.style.borderStyle = 'solid';
			  errorWrapper.style.backgroundColor = this.bgcolor;
			  errorWrapper.style.color = this.textcolor;
			  if(this.arrowPlacement=="right"){
				  errorWrapper.style.marginLeft = this.arrowHeight + 'px';
			  }
			  nestedSpan.style.visibility = "hidden";
			  nestedSpan.style.display = "inline";
			  this.inputHeight = theSpryInput.offsetHeight;
			  for (var y=0; y<childNodes.length; y++)  {
				 errorWrapper.appendChild(childNodes[y]);  
			  }
			  for (var y=childNodes.length-1; y>=0; y--)  {
				 nestedSpan.removeChild(childNodes[y]);  
			  }
			  errorFloat.appendChild(errorWrapper);
			  nestedSpan.appendChild(errorFloat);
			  var closeButton = document.createElement("div");
			  closeButton.className = "WA_SpryValidationTooltip_closeValidation";
			  var closeText = document.createTextNode("x");
			  closeButton.appendChild(closeText);
			  errorFloat.insertBefore(closeButton,errorWrapper);
			  
			  if (this.arrowHeight > 0) {
				var arrowGroup = this.buildArrow(errorFloat);
				if(!this.isTop){
					errorFloat.insertBefore(arrowGroup, closeButton);
				}
				else{
					errorFloat.appendChild(arrowGroup);
				}
			  }
			  
  			  errorFloat.style.left = leftPos + 'px';
			  var eleTop = '';
			  if (this.arrowPlacement=="right")  {
				eleTop = (ypos - (errorFloat.offsetHeight/2) +  theSpryInput.offsetHeight/2) + "px";
				errorFloat.style.top = eleTop;
			  } else if (arrowPlacement.indexOf("bottom") >= 0) {
				eleTop = (ypos + theSpryInput.offsetHeight) + "px";
				errorFloat.style.top = eleTop;
			  } else  {
				eleTop = (ypos - errorFloat.offsetHeight) + "px";
				errorFloat.style.top = eleTop;
			  }
			  
			  closeButton.style.position = 'absolute';
			  closeButton.style.right = (this.border + parseInt(this.roundedness/3) + 3)  + 'px';
			  closeButton.style.marginTop = this.border+"px";
			  closeButton.style.color = this.closecolor;
			  closeButton.setAttribute("name",x);
			  closeButton.onclick = function() { Spry.Widget.Form.onSubmitWidgetQueue[parseInt(this.getAttribute('name'))].reset(); };
			  errorFloat.parentNode.style.display = "";
			  errorFloat.parentNode.style.visibility = "";
			  var arrayPos = queue[x].toolTipSpans.length;
			  queue[x].toolTipSpans[arrayPos] = errorFloat;
			  queue[x].toolTipCloses[arrayPos] = closeButton;
			}
		  }
		}
	  }
	  this.applied = true;
	  
	};
	
	this.findPagePos = function(obj) {
	  var curleft = curtop = 0;
	  if (obj.offsetParent) {
		do {
		  curleft += obj.offsetLeft;
		  curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	  }
	  return [curleft,curtop];
	}

	this.findPos = function(obj) {
	  curleft = obj.offsetLeft;
	  curtop = obj.offsetTop;
	  return [curleft,curtop];
	};
	
	this.refreshTextNodes = function(searchNode) {
	  if (!searchNode) searchNode = document.body;
	  var childNodes = searchNode.childNodes;
	  for (var x=0; x<childNodes.length; x++) {
		  var currentNode = childNodes[x];
		  if (currentNode.nodeType === 1) {
			  this.refreshTextNodes(currentNode);
		  } else if (currentNode.data && currentNode.data!="") {
			  currentNode.nodeValue = currentNode.nodeValue + " ";
			  currentNode.nodeValue = currentNode.nodeValue.substring(0,currentNode.nodeValue.length-1);
		  }
	  }
	}
	
	this.reposition = function()  {
	  if(typeof Spry !== 'undefined' && Spry && Spry.Widget && Spry.Widget.Form &&  Spry.Widget.Form.onSubmitWidgetQueue){
		  var queue = Spry.Widget.Form.onSubmitWidgetQueue;
		for(var x =0; x < queue.length; x++){
		  if (queue[x].toolTipCreated)  {
			var theSpryInput = queue[x].toolTipElement;
			var toolTipObj = queue[x].toolTip;
			var thePosition = toolTipObj.findTipPosition(theSpryInput);
			var leftPos = thePosition[0];
			var ypos = thePosition[1];
			for (var y=0; y<queue[x].toolTipSpans.length; y++)  {
			  var errorFloat = queue[x].toolTipSpans[y];
			  var nestedSpan = errorFloat.parentNode;
			  nestedSpan.style.visibility = "hidden";
			  nestedSpan.style.display = "inline";
			  this.refreshTextNodes(errorFloat);
  			  errorFloat.style.left = leftPos + 'px';
			  var eleTop = '';
			  if (this.arrowPlacement=="right")  {
				eleTop = (ypos - (errorFloat.offsetHeight/2) +  theSpryInput.offsetHeight/2) + "px";
				errorFloat.style.top = eleTop;
			  } else if (arrowPlacement.indexOf("bottom") >= 0) {
				eleTop = (ypos + theSpryInput.offsetHeight) + "px";
				errorFloat.style.top = eleTop;
			  } else  {
				eleTop = (ypos - errorFloat.offsetHeight) + "px";
				errorFloat.style.top = eleTop;
			  }
			  
			  nestedSpan.style.visibility = "";
			  nestedSpan.style.display = "";
			}
		  }
		}
	  }
	};
	
	this.buildArrow = function(errorFloat)  {
		var arrowGroupParent = document.createElement("div");
		arrowGroupParent = this.buildArrowBase(arrowGroupParent);
		var adjustment = this.border;
		
		if(this.isRight){
			arrowGroupParent.style.top = ( (errorFloat.offsetHeight+this.border)/2) +"px"; // center arrow poing vertically on the field;
			arrowGroupParent.style.left = '0px';
		}
		else{
			if(this.isBottom){
				arrowGroupParent.style.top = + this.border + 'px';
			}
			else if(this.isTop){
				arrowGroupParent.style.top = - this.border + 'px';
				
			}
			arrowGroupParent.style.left = this.arrowOffset + this.tipOffset + 'px';
		}
		
		arrowGroupParent.style.position = "relative";
		arrowGroupParent.style.zIndex = '10000';
		return arrowGroupParent;
	};
	
	this.buildArrowBase = function(arrowGroupParent)  {
		var arrowWidth = 2 * (this.arrowHeight + this.border);
		var StartWidth = arrowWidth;
		var leftMargin =  0;
		
		// build arrow from widest point down to smallest point. For bottom located arrows, we'll reverse the array before adding to the parent.
		var slices = [];
		
		while (arrowWidth > 0){
			var arrowSection = document.createElement("div");
			arrowSection.className = "WA_SpryValidationTooltip_downArrow";
			var useBorderLeft = this.border;
			var useBorderRight = this.border;
			if (this.arrowPointer == "slant-left" && this.border > 0) useBorderLeft ++;
			if (this.arrowPointer == "slant-right" && this.border > 0) useBorderRight ++;
			
			if (this.arrowPlacement == "right"){
				var useWidth = StartWidth-arrowWidth - this.border*2;
			}
			else{
				var useWidth = arrowWidth - this.border*2;
			}
			if (useWidth<0){
				useBorderLeft += useWidth/2;
				useBorderRight += useWidth/2;
				useWidth = 0;
			}
			
			if (this.arrowPlacement == "right"){
				var arrowHeight = StartWidth-arrowWidth;
				arrowSection.style.height = ((useWidth>0)?useWidth:0) + "px";
				arrowSection.style.width = "1px";
			}
			else{
				arrowSection.style.width = ((useWidth>0)?useWidth:0)+"px";
				arrowSection.style.height = "1px";
			}

			arrowSection.style.borderStyle = 'solid';
			arrowSection.style.borderWidth =  this.borderPattern.replace("x",(useBorderLeft)+"px").replace("y",useBorderRight+"px");
			arrowSection.style.borderColor = this.bordercolor;
			arrowSection.style.backgroundColor = this.bgcolor;

			
			if (this.arrowPlacement == "right") {
				arrowSection.style.position =  "absolute";
				arrowSection.style.left = (leftMargin++) +"px";
				arrowSection.style.marginTop =  (-1 * leftMargin) +"px";
			}
			else{
				arrowSection.style.marginLeft = (leftMargin) +"px";
				leftMargin +=  (this.arrowPointer == "slant-left") ? 0 : (this.arrowPointer == "center") ?  1 : 2;
				
			}
			
			slices.push(arrowSection);
			arrowWidth -= 2;
		}
		
		if(this.isBottom){
			slices.reverse();
		}
		
		for(var i = 0; i < slices.length; i++){
			arrowGroupParent.appendChild( slices[i] );
		}
		
		return arrowGroupParent;
	};
}