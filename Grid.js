////////////////////////////////////
//
// Grid
// MIT-style license. Copyright 2012 Matt V. Murphy
//
////////////////////////////////////
(function(window, document, undefined) {
	"use strict";
	
	var GridProto;
	var Grid = function(element, options) {
		if ((this.element = (typeof(element) === "string") ? $(element) : element)) {
			this.css = { idRulePrefix : "#" + this.element.id + " ", sheet : null, rules : {} };
			this.columns = 0;
			this.columnWidths = [];
			this.cellData = { head : [], body : [], foot : [] };
			this.alignTimer = null;
			this.rawData = [];
			this.usesTouch = (window.ontouchstart !== undefined);
			this.startEvt = (this.usesTouch) ? "touchstart" : "mousedown";
			this.moveEvt = (this.usesTouch) ? "touchmove" : "mousemove";
			this.endEvt = (this.usesTouch) ? "touchend" : "mouseup";
			this.setOptions(options);
			this.init();
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	(GridProto = Grid.prototype).nothing = function(){};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.setOptions = function(options) {
		var hasOwnProp = Object.prototype.hasOwnProperty, 
		    option;
		
		this.options = {
			srcType : "", // "dom", "json", "xml"
			srcData : "", 
			onLoad : this.nothing, 
			supportMultipleGridsInView : false, 
			fixedCols : 0, 
			colAlign : [], // "left", "center", "right"
			colBGColors : [], 
		};
		
		if (options) {
			for (option in this.options) {
				if (hasOwnProp.call(this.options, option) && options[option] !== undefined) {
					this.options[option] = options[option];
				}
			}
		}
		this.options.fixedCols = (!this.usesTouch) ? this.options.fixedCols : 0;
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.init = function() {
		var srcType = this.options.srcType, 
		    srcData = this.options.srcData, 
		    data;
		
		this.generateSkeleton();
		this.addEvents();
		
		// DOM:
		if (srcType === "dom" && (srcData = (typeof(srcData) === "string") ? $(srcData) : srcData)) {
			this.convertData(this.convertDomDataToJsonData(srcData));
			
		// JSON:
		} else if (srcType === "json" && (data = parseJSON(srcData))) {
			this.convertData(data);
			
		// XML:
		} else if (srcType === "xml" && (data = parseXML(srcData))) {
			this.convertData(this.convertXmlDataToJsonData(data));
		}
		
		this.generateGrid();
		this.displayGrid();
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateSkeleton = function() {
		var doc = document, 
		    elems = [["base", "g_Base", "docFrag"], 
		             ["head", "g_Head", "base"], 
		             ["headFixed", "g_HeadFixed", "head"], 
		             ["headStatic", "g_HeadStatic", "head"], 
		             ["foot", "g_Foot", "base"], 
		             ["footFixed", "g_FootFixed", "foot"], 
		             ["footStatic", "g_FootStatic", "foot"], 
		             ["body", "g_Body", "base"], 
		             ["bodyFixed", "g_BodyFixed", "body"], 
		             ["bodyFixed2", "g_BodyFixed2", "bodyFixed"], 
		             ["bodyStatic", "g_BodyStatic", "body"]];
		
		this.parentDimensions = { x : this.element.offsetWidth, y : this.element.offsetHeight };
		this.docFrag = doc.createDocumentFragment();
		for (var i=0, elem; elem=elems[i]; i++) {
			(this[elem[0]] = doc.createElement("DIV")).className = elem[1];
			this[elem[2]].appendChild(this[elem[0]]);
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.addEvents = function() {
		var wheelEvent;
		
		// Simulate mouse scrolling over non-scrollable content:
		if (this.options.fixedCols > 0 && !this.usesTouch && !msie) {
			try {
				wheelEvent = (WheelEvent("wheel")) ? "wheel" : undefined;
			} catch (e) {
				wheelEvent = (document.onmousewheel !== undefined) ? "mousewheel" : "DOMMouseScroll";
			}
			if (wheelEvent) {
				addEvent(this.bodyFixed, wheelEvent, bind(this.simulateMouseScroll, this));
			}
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.convertDomDataToJsonData = function(data) {
		var sections = { "thead" : "Head", "tbody" : "Body", "tfoot" : "Foot" }, 
		    section, rows, row, cells, arr, arr2, i, j, k, 
		    json = {};
		
		// Cycle through all table rows, change sections when needed:
		if (((data || {}).tagName || "").toLowerCase() === "table") {
			for (i=0, j=0, rows=data.rows; row=rows[i]; i++) {
				if (row.sectionRowIndex === 0 && (section = sections[row.parentNode.tagName.toLowerCase()])) {
					json[section] = arr = (json[section] || []);
					j = arr.length;
				}
				arr[j++] = arr2 = [];
				k = (cells = row.cells).length;
				while (k) { arr2[--k] = cells[k].innerHTML; }
			}
		}
		
		return json;
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.convertXmlDataToJsonData = function(data) {
		var sections = { "thead" : "Head", "tbody" : "Body", "tfoot" : "Foot" }, 
		    cellText = (msie < 9) ? "text" : "textContent", 
		    nodes, node, section, rows, row, cells, cell, tag, n, i, j, 
		    arr, arr2, a, a2, 
		    json = {};
		
		// By section:
		if ((nodes = (data.getElementsByTagName("table")[0] || {}).childNodes)) {
			for (n=0; node=nodes[n]; n++) {
				if ((section = sections[node.nodeName]) && (rows = node.childNodes)) {
					json[section] = arr = (json[section] || []);
					a = arr.length;
					
					// By row:
					for (i=0; row=rows[i]; i++) {
						if (row.nodeName === "tr" && (cells = row.childNodes)) {
							arr[a++] = arr2 = [];
							a2 = 0;
							
							// By cell:
							for (j=0; cell=cells[j]; j++) {
								if ((tag = cell.nodeName) === "td" || tag === "th") {
									arr2[a2++] = cell[cellText] || "";
								}
							}
						}
					}
				}
			}
		}
		
		return json;
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.convertData = function(data) {
		var base, cols, h, b, f;
		
		//this.addSelectionColumn(data);
		this.rawData = data.Body || [];
		if ((base = data.Head || data.Body || data.Foot || null)) {
			cols = this.columns = base[0].length;
			h = this.cellData.head;
			b = this.cellData.body;
			f = this.cellData.foot;
			while (cols) { h[--cols] = []; b[cols] = []; f[cols] = []; }
			
			cols = this.columns;
			if (data.Head) {
				this.convertDataItem(h, data.Head, "<DIV class='g_C g_HR g_R", cols, true);
			} else {
				this.css.rules[".g_Head"] = { display : "none" };
			}
			if (data.Body) {
				this.convertDataItem(b, data.Body, "<DIV class='g_C g_BR g_R", cols, false);
			} else {
				this.css.rules[".g_BodyFixed"] = { display : "none" };
			}
			if (data.Foot) {
				this.convertDataItem(f, data.Foot, "<DIV class='g_C g_FR g_R", cols, true);
			} else {
				this.css.rules[".g_Foot"] = { display : "none" };
			}
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.convertDataItem = function(arr, rows, rowClass, cols, allowColResize) {
		var rowIdx = rows.length, 
		    rowDiv, row, colIdx;
		
		while (rowIdx) {
			//rowDiv = rowClass + (--rowIdx) + "'>";
			rowDiv = rowClass + (--rowIdx);
			row = rows[rowIdx];
			colIdx = cols;
			while (colIdx) {
				
				//***************add add see*******************
				--colIdx;
				var tds = document.getElementById("demoTable").getElementsByTagName("td");
				var nowIndex = rowIdx * cols + colIdx;
				
				if (tds[nowIndex].className == "g_hRisk" && !allowColResize) { //高風險值
					arr[colIdx][rowIdx] = rowDiv + " g_hRisk'>" + (row[colIdx] || "&nbsp;");
				}
				else if (tds[nowIndex].className == "g_lRisk" && !allowColResize) //中風險值
				{
					arr[colIdx][rowIdx] = rowDiv + " g_lRisk'>" + (row[colIdx] || "&nbsp;");
				}
				else if (tds[nowIndex].className == "g_title" && !allowColResize) //title
				{
					arr[colIdx][rowIdx] = rowDiv + " g_title' id='row" + rowIdx +"'>" + (row[colIdx] || "&nbsp;");
				}
				else if (tds[nowIndex].className == "g_title2" && !allowColResize) //非風險值的部分
				{
					arr[colIdx][rowIdx] = rowDiv + " g_title2'>" + (row[colIdx] || "&nbsp;");
				}
				else if (tds[nowIndex].className == "g_body2" && !allowColResize) //非風險值的部分
				{
					arr[colIdx][rowIdx] = rowDiv + " g_body2'>" + (row[colIdx] || "&nbsp;");
				}
				else if (tds[nowIndex].className == "finacial_title" && !allowColResize) //財務指標的部分
				{
					arr[colIdx][rowIdx] = rowDiv + " finacial_title'>" + (row[colIdx] || "&nbsp;");
				}
				else if ( rowIdx % 2 == 0 && !allowColResize )
				{
					arr[colIdx][rowIdx] = rowDiv + " g_lGrow'>" + (row[colIdx] || "&nbsp;");
				}
				else {
					arr[colIdx][rowIdx] = rowDiv + "'>" + (row[colIdx] || "&nbsp;");
				}
				
				//*********************************************
			
				//arr[--colIdx][rowIdx] = rowDiv + (row[colIdx] || "&nbsp;");
			}
		}
		/*if (allowColResize && (rowIdx = rows.length)) {
			colIdx = cols;
			while (colIdx) {
				arr[--colIdx][0] = ("<SPAN class='g_RS g_RS" + colIdx + "'>&nbsp;</SPAN>") + arr[colIdx][0];
			}
		}*/
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.addSelectionColumn = function(data) {
		var html, rows, i;
		
		if (this.options.showSelectionColumn) {
			this.options.colBGColors.unshift(this.options.colBGColors[0] || "");
			this.options.colSortTypes.unshift("none");
			this.options.colAlign.unshift("left");
			if (!this.usesTouch) {
				this.options.fixedCols++;
			}
			
			if ((rows = data.Head) && (i = rows.length)) {
				while (i) { rows[--i].unshift(""); }
			}
			if ((rows = data.Body) && (i = rows.length)) {
				html = "<LABEL class=g_SH><INPUT tabIndex='-1' type=";
				html += ((this.options.allowMultipleSelections) ? "checkbox class=g_Cb" : "radio  class=g_Rd");
				html += ">&nbsp;</LABEL>";
				while (i) { rows[--i].unshift(html); }
			}
			if ((rows = data.Foot) && (i = rows.length)) {
				while (i) { rows[--i].unshift(""); }
			}
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateGrid = function() {
		this.hasHead = ((this.cellData.head[0] || []).length > 0);
		this.hasBody = ((this.cellData.body[0] || []).length > 0);
		this.hasFoot = ((this.cellData.foot[0] || []).length > 0);
		this.hasHeadOrFoot = (this.hasHead || this.hasFoot);
		this.hasFixedCols = (this.options.fixedCols > 0);
		
		this.generateGridHead();
		this.generateGridBody();
		this.generateGridFoot();
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateGridHead = function() {
		var hHTML;
		
		if (this.hasHead) {
			hHTML = this.generateGridSection(this.cellData.head);
			this.headStatic.innerHTML = hHTML.fullHTML;
			if (this.hasFixedCols) {
				this.headFixed.innerHTML = hHTML.fixedHTML;
			}
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateGridBody = function() {
		var bHTML;
		
		if (this.hasBody) {
			bHTML = this.generateGridSection(this.cellData.body);
			this.bodyStatic.innerHTML = bHTML.fullHTML;
			if (this.hasFixedCols) {
				this.bodyFixed2.innerHTML = bHTML.fixedHTML;
			}
		} else {
			this.bodyStatic.innerHTML = "<DIV class='g_EmptySetMsg'>No results returned.</DIV>";
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateGridFoot = function() {
		var fHTML;
		
		if (this.hasFoot) {
			fHTML = this.generateGridSection(this.cellData.foot);
			this.footStatic.innerHTML = fHTML.fullHTML;
			if (this.hasFixedCols) {
				this.footFixed.innerHTML = fHTML.fixedHTML;
			}
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.generateGridSection = function(cols) {
		var replaceFunc = function($1, $2) { return cols[parseInt($2, 10)].join("</DIV>"); }, 
		    replaceRgx = /@(\d+)@/g, 
		    fixedCols = this.options.fixedCols, 
		    fHtml = [], sHtml = [], 
		    colIdx = cols.length;
		
		while (colIdx) {
			if ((--colIdx) < fixedCols) {
				fHtml[colIdx] = "<DIV class='g_Cl g_Cl" + colIdx + " g_FCl'>@" + colIdx + "@</DIV></DIV>";
				sHtml[colIdx] = "<DIV class='g_Cl g_Cl" + colIdx + " g_FCl'></DIV>";
			} else {
				sHtml[colIdx] = "<DIV class='g_Cl g_Cl" + colIdx + "'>@" + colIdx + "@</DIV></DIV>";
			}
		}
		
		return { fixedHTML : (fixedCols) ? fHtml.join("").replace(replaceRgx, replaceFunc) : "", 
		         fullHTML : sHtml.join("").replace(replaceRgx, replaceFunc) };
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.displayGrid = function() {
		var srcType = this.options.srcType, 
		    srcData = this.options.srcData, 
		    replace = false;
		
		// Setup scrolling:
		this.lastScrollLeft = 0;
		this.lastScrollTop = 0;
		this.body.onscroll = bind(this.syncScrolls, this);
		
		// Prep style element:
		try {
			this.css.sheet.parentNode.removeChild(this.css.sheet);
		} catch (e) {
			(this.css.sheet = document.createElement("STYLE")).id = this.element.id + "SS";
			this.css.sheet.type = "text/css";
		}
		
		// Insert grid into DOM:
		if (srcType === "dom" && (srcData = (typeof(srcData) === "string") ? $(srcData) : srcData)) {
			if ((replace = (this.element === srcData.parentNode))) {
				this.element.replaceChild(this.docFrag, srcData);
			}
		}
		if (!replace) {
			this.element.appendChild(this.docFrag);
		}
		
		// Align columns:
		this.alignTimer = window.setTimeout(bind(this.alignColumns, this, false, true), 16);
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.alignColumns = function(reAlign, fromInit) {
		var sNodes = [this.headStatic.children || [], this.bodyStatic.children || [], this.footStatic.children || []], 
		    fNodes = [this.headFixed.children || [], this.bodyFixed2.children || [], this.footFixed.children || []], 
		    allowColumnResize = this.options.allowColumnResize, 
		    colBGColors = this.options.colBGColors, 
		    colAlign = this.options.colAlign, 
		    fixedCols = this.options.fixedCols, 
		    rules = this.css.rules, 
		    colWidth, nodes;
		
		// Compute base styles first, or remove old column width styling if realigning the columns:
		if (reAlign !== true) {
			this.computeBaseStyles();
		} else {
			for (var i=0, len=this.columns; i<len; i++) {
				rules[".g_Cl" + i].width = "auto";
			}
			this.setRules();
		}
		
		// Compute column width, alignment and background styles:
		this.columnWidths = [];
		for (var i=0, len=this.columns; i<len; i++) {
			nodes = (i < fixedCols) ? fNodes : sNodes;
			colWidth = Math.max((nodes[0][i] || {}).offsetWidth || 0, 
			                    (nodes[1][i] || {}).offsetWidth || 0, 
			                    (nodes[2][i] || {}).offsetWidth || 0);
			
			this.columnWidths[i] = colWidth;
			rules[".g_Cl" + i] = { "width" : colWidth + "px", "text-align" : (colAlign[i] || "left") };
			if ((colBGColors[i] || "#ffffff") !== "#ffffff") {
				rules[".g_Cl" + i]["background-color"] = colBGColors[i];
			}
			if (allowColumnResize) {
				rules[".g_RS" + i] = { "margin-left" : (colWidth - 2) + "px" };
			}
		}
		this.setRules();
		if (fromInit === true) {
			this.options.onLoad.call(this);
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.computeBaseStyles = function() {
		var rules = this.css.rules, 
		    headHeight = (this.hasHead) ? this.head.offsetHeight : 0, 
		    footHeight = (this.hasFoot) ? this.foot.offsetHeight : 0, 
		    sBarSize = { "x" : this.body.offsetWidth - this.body.clientWidth, 
		                 "y" : this.body.offsetHeight - this.body.clientHeight };
		
		rules[".g_C"] = { "visibility" : "visible" };
		//rules[".g_Cl"] = { "background-color" : "#fff" };
		rules[".g_BodyStatic"] = { "padding" : headHeight + "px 0px " + footHeight + "px 0px" };
		if (this.hasHead) {
			rules[".g_Head"] = { "right" : sBarSize.x + "px" };
		}
		if (this.hasFoot) {
			rules[".g_Foot"] = { "bottom" : sBarSize.y + "px", "right" : sBarSize.x + "px" };
		}
		if (this.hasFixedCols) {
			rules[".g_BodyFixed" + ((msie < 8) ? "2" : "")] = { "top" : headHeight + "px", "bottom" : sBarSize.y + "px" };
		}
		
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.syncScrolls = function(event) {
		var sL = (this.hasHeadOrFoot) ? this.body.scrollLeft : 0, 
		    sT = (this.hasFixedCols) ? this.body.scrollTop : 0;
		
		if (sL !== this.lastScrollLeft) {
			this.lastScrollLeft = sL;
			if (this.hasHead) {
				this.headStatic.style.marginLeft = (-1 * sL) + "px";
			}
			if (this.hasFoot) {
				this.footStatic.style.marginLeft = (-1 * sL) + "px";
			}
		}
		if (sT !== this.lastScrollTop) {
			this.lastScrollTop = sT;
			this.bodyFixed2.style.marginTop = (-1 * sT) + "px";
		}
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.simulateMouseScroll = function(event) {
		var event = event || window.event, 
		    deltaY = 0;
		
		if (event.deltaY !== undefined) {
			deltaY = event.deltaY;
		} else if (event.wheelDelta !== undefined) {
			deltaY = event.wheelDelta * (-1/40);
		} else if (event.detail !== undefined) {
			deltaY = event.detail;
		}
		
		this.body.scrollTop += (deltaY * 33);
		this.syncScrolls();
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	GridProto.setRules = function() {
		var idRulePrefix = (this.options.supportMultipleGridsInView) ? this.css.idRulePrefix : "", 
		    hasOwnProp = Object.prototype.hasOwnProperty, 
		    rules = this.css.rules, 
		    sheet = this.css.sheet, 
		    cssText = [], c = 0, 
		    rule, props, prop, 
		    doc = document;
		
		for (rule in rules) {
			if (hasOwnProp.call(rules, rule) && (props = rules[rule])) {
				cssText[c++] = idRulePrefix + rule + "{";
				for (prop in props) {
					if (hasOwnProp.call(props, prop)) {
						cssText[c++] = prop + ":" + props[prop] + ";";
					}
				}
				cssText[c++] = "} ";
			}
		}
		
		if (!sheet.styleSheet) {
			sheet.appendChild(doc.createTextNode(cssText.join("")));
		}
		if (!$(sheet.id)) {
			(doc.head || doc.getElementsByTagName("head")[0]).appendChild(sheet);
		}
		if (sheet.styleSheet) {
			sheet.styleSheet.cssText = cssText.join("");
		}
	};
	

	//////////////////////////////////////////////////////////////////////////////////
	GridProto.delegateHeaderEvent = function(event) {
		var event = event || window.event, 
		    target = event.target || event.srcElement, 
		    targetClass = target.className || "";
		
		if (event.button !== 2) {
			if (this.options.allowColumnResize && targetClass.indexOf("g_RS") > -1) {
				return this.initResizeColumn(event, target, targetClass);
			} else if (this.hasBody && this.options.allowClientSideSorting) {
				while (targetClass.indexOf("g_Cl") === -1 && targetClass !== "g_Head") {
					targetClass = (target = target.parentNode).className || "";
				}
				if (targetClass.indexOf("g_Cl") > -1) {
					this.sortColumn(parseInt(/g_Cl(\d+)/.exec(targetClass)[1], 10));
				}
			}
		}
	};
	

	//////////////////////////////////////////////////////////////////////////////////
	//
	// Utility Methods
	//
	//////////////////////////////////////////////////////////////////////////////////
	var getIEVersion = function() {
		var nav, version;
		
		if ((nav = navigator).appName === "Microsoft Internet Explorer") {
			if (new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})").exec(nav.userAgent)) {
				version = parseFloat(RegExp.$1);
			}
		}
		return (version > 5) ? version : undefined;
	};
	

	//////////////////////////////////////////////////////////////////////////////////
	var addEvent = (document.addEventListener) ? 
	  function(elem, type, listener) { elem.addEventListener(type, listener, false); } : 
	  function(elem, type, listener) { elem.attachEvent("on" + type, listener); };
	
	//////////////////////////////////////////////////////////////////////////////////
	var stopEvent = function(event) {
		if (event.stopPropagation) {
			event.stopPropagation();
			event.preventDefault();
		} else {
			event.returnValue = false;
			event.cancelBubble = true;
		}
		return false;
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	var removeEvent = (document.addEventListener) ? 
	  function(elem, type, listener) { elem.removeEventListener(type, listener, false); } : 
	  function(elem, type, listener) { elem.detachEvent("on" + type, listener); };
	
	//////////////////////////////////////////////////////////////////////////////////
	var getEventPositions = function(event, type) {
		var pageX = event.pageX, 
		    pageY = event.pageY, 
		    doc, elem;
		
		// Client position:
		if (type === "client") {
			if (pageX !== undefined || pageY !== undefined) {
				return { x : pageX - window.pageXOffset, y : pageY - window.pageYOffset };
			}
			return { x : event.clientX, y : event.clientY };
		}
		
		// Page position:
		if (pageX === undefined || pageY === undefined) {
			elem = ((doc = document).documentElement.scrollLeft !== undefined) ? doc.documentElement : doc.body;
			return { x : event.clientX + elem.scrollLeft, y : event.clientY + elem.scrollTop };
		}
		return { x : pageX, y : pageY };
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	var bind = function(func, that) {
		var a = slice.call(arguments, 2);
		return function() { return func.apply(that, a.concat(slice.call(arguments))); };
	};
	
	//////////////////////////////////////////////////////////////////////////////////
	var indexOf = ([].indexOf) ? 
	  function(arr, item) { return arr.indexOf(item); } : 
	  function(arr, item) {
	  	for (var i=0, len=arr.length; i<len; i++) { if (arr[i] === item) { return i; } } return -1;
	  };
	

	//////////////////////////////////////////////////////////////////////////////////
	var $ = function(elemId) { return document.getElementById(elemId); }, 
	    slice = Array.prototype.slice, 
	    msie = getIEVersion();
	
	// Expose:
	window.Grid = Grid;
	
})(this, this.document);