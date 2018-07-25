	// Detection du navigateur client
	function getBrowser() {
		this.isIE = (document.all) ? true : false;
		this.isNS4 = (document.layers) ? true : false;
		this.isIE4 = (document.all && !document.getElementById) ? true : false;
		this.isIE5 = (document.all && document.getElementById) ? true : false;
		this.isNS6 = (!document.all && document.getElementById) ? true : false;
		this.isOK=(document.getElementById || document.all || document.layers);
		return this;
	}
	
	function getObj(name) {
		var userBrowser= new getBrowser();
		if (userBrowser.isIE4 && document.all[name]) {
			this.obj = document.all[name];
			this.style = document.all[name].style;
		}
		else if (userBrowser.isNS4 && document.layers[name]) {
			this.obj = document.layers[name];
			this.style = document.layers[name];
		}
		else if ((userBrowser.isIE5 || userBrowser.isNS6) && document.getElementById(name)) {
			this.obj = document.getElementById(name);
			this.style = document.getElementById(name).style;
		}
		return this;
	}
	
	function writeToLayer(name, info) {
		var userBrowser= new getBrowser();
		if (userBrowser.isIE4) {
			document.all[name].innerHTML=info; 
		}
		else if (userBrowser.isNS4) {
			with(document.layers[name].document) {
				open();
				var strHtml="";
				strHtml="<span class=\""+ name +"\">";
				strHtml=strHtml+info;
				strHtml=strHtml+"</span>";
				write(strHtml);
				close();
			}
		}
		else if (userBrowser.isIE5 || userBrowser.isNS6) {
			document.getElementById(name).innerHTML=info;
		}
	}
	
	// change la visibilité des calques
	function setLayerVis() {
		// utilisation : "setLayerVis('<nomLayer>',0 ou 1);"
		var argv=setLayerVis.arguments;
		if((argv.length % 2) == 0) {
			for(i=0;i<argv.length;i+=2) {
				idCalque=argv[i];
				visCalque=argv[i+1]? "visible" : "hidden";		
				var elm=new getObj(idCalque);
				if (elm.obj) {
					elm.style.visibility=visCalque;
				}
			}
		}
	}
	
	//---------------------------------------------------------------------------
	
	function getLyrHeight(id) {
		var userBrowser= new getBrowser();
		var lyrobj=new getObj(id);
		if (lyrobj.obj) {
			if (userBrowser.isNS4) {
				return lyrobj.style.clip.bottom;
			} else if (userBrowser.isIE || userBrowser.isNS6) {
				return lyrobj.obj.offsetHeight;
			}
		} else { return null; }
	}
	
	function getLyrWidth(id) {
		var userBrowser= new getBrowser();
		var lyrobj=new getObj(id);
		if (lyrobj.obj) {
			if (userBrowser.isNS4) {
				return lyrobj.style.clip.width;
			} else if (userBrowser.isIE || userBrowser.isNS6) {
				return lyrobj.obj.offsetWidth;
			}
		} else { return null; }
	}
	
	function docPage() { 
	 	var userBrowser=new getBrowser();
		this.height=userBrowser.isIE && document.body.offsetHeight-5||innerHeight||0;
		this.width=userBrowser.isIE && document.body.offsetWidth-20||innerWidth||0;
	  	if(!this.width || !this.height) { return null;}
		return this;
	}