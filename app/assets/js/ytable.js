// -------------------------------------------------------------------------------------------------
// methode de base composant
// ybi: 14/02/2019
// -------------------------------------------------------------------------------------------------

var ytable_template = function() {
	
	this.buildTable = function(yt) { };
	this.buildPagination = function(yt, pagination) { };
	this.addRecord = function(yt, data, $th) { };
	this.updateSelectAll = function(state, $head) { };
	this.onSelect = function(state, $tr) { };
	this.showWait = function(yt) { };
	this.hideWait = function(yt) { };
	this.getRecordsPerPage = function(yt) { return null; };
	
};

var ytable = function(options) {
	
	if (!ytable.prototype.internalId) ytable.prototype.internalId = 0;
	
	this._componentId = ++ytable.prototype.internalId;
	
	this.defaultOptions = function() {
		return {
			'url': {
				'get': null
			},
			'template': null,
			'selectable': false,
			'selectall': false,
			'multiselection': false,
			'container': null,
			'recordsperpage': [10,25,50,100,250,500],
			'defaultrecordsperpage': 10,
			'fields': [],
			'events': {
				'addrecord': null,
				'selectrecord': null,
				'unselectrecord': null,
				'selectionchange': null,
				'loading': null,
				'recordsloaded': null,
				'error': null
			}
		};
	};
	
	this._events = {};
	this._options = $.extend({}, this.defaultOptions(), options);
	this._template = (options && options.template) ? options.template : new ytable_template();
	this._lastAjaxData = null;
	
/*******************
	EVENTS DEB 
*******************/	

	this.addEvents = function(name, e) { 
		if (!this._events[name]) this._events[name] = [];
		var newEvent = { 'id': ++ytable.prototype.internalId, 'e': e };
		this._events[name].push(newEvent);
		return newEvent;
	};
	
	this.raiseEvent = function(name, parameters) {
		if (!this._events[name] || this._events[name].length == 0) return;
		var eArguments = [this];
		if (typeof parameters != 'undefined') {
			if ($.isArray(parameters)) {
				eArguments = eArguments.concat(parameters);
			}
			else {
				eArguments.push(parameters);
			}
		}
		for(var i = 0; i < this._events[name].length; i++) {
			if ($.isFunction(this._events[name][i].e)) this._events[name][i].e.apply(this, eArguments);
		}
	};
	
	if (this._options && this._options.events) {
		for(var ev in this._options.events) this.addEvents(ev, this._options.events[ev]);
	}
	
/*******************
	EVENTS END 
*******************/	

/*******************
	SELECTION DEB 
*******************/	

	this.onSelectTR = function(state, $tr, calltemplate) {
		
		var selectRecords = [];
		var unselectRecords = [];
		
		if (state) {
			
			if (!this._options.multiselection) {
				
				var $table = $tr.closest("table");
				var curSelected = $table.find("tbody > tr.ytisselected");
				curSelected.each($.proxy(function(index, item) {
					var $tr = $(item);
					this.unselectRecords.push({ "record" : $tr.data("ytable-record"), 'tr': $tr });
					if ($tr != this.currentTR) this.ref._template.onSelect(false, $tr);
				}, { 'unselectRecords': unselectRecords, 'currentTR': $tr, 'calltemplate': calltemplate, 'ref': this } ));
				$table.find("tbody > tr").removeClass("ytisselected");
				
			}

			if (calltemplate) this._template.onSelect(state, $tr);
			
			$tr.addClass("ytisselected");
			selectRecords.push({ "record" : $tr.data("ytable-record"), 'tr': $tr });				
			
		}
		else {
			
			if (calltemplate) this._template.onSelect(state, $tr);
			
			$tr.removeClass("ytisselected");
			unselectRecords.push({ "record" : $tr.data("ytable-record"), 'tr': $tr });
		}
		
		this.updateSelectAll();
		
		if (selectRecords.length > 0) this.raiseEvent("selectrecord", [ selectRecords ]);
		if (unselectRecords.length > 0) this.raiseEvent("unselectrecord", [ unselectRecords ]);
		this.raiseEvent("selectionchange", [ selectRecords, unselectRecords ]);
		
		
	};
	
	this.updateSelectAll = function() {
		
		if (!(this._options.selectable && this._options.selectall) || !this._options.multiselection) return;
		
		var $thead = this.getContainer().find("table > thead");
		var $table = this.getContainer().find("table > tbody");
		
		var nbTr = $table.find("tr").length;
		var nbTrSelected = $table.find("tr.ytisselected").length;
		
		if (nbTr == 0) { this._template.updateSelectAll(false, $thead) }
		else if (nbTrSelected == 0) { this._template.updateSelectAll(false, $thead); }
		else if (nbTr == nbTrSelected) { this._template.updateSelectAll(true, $thead); }
		else { this._template.updateSelectAll(null, $thead); }
		
	};
	
	this.onSelectAll = function(state, calltemplate) {
		
		var $thead = this.getContainer().find("table > thead");
		var $table = this.getContainer().find("table > tbody");		
		
		if (calltemplate) this._template.updateSelectAll(state, $thead);
		
		var workData = { 'ref': this, 'data': [], 'state': state };
		
		$table.find("tr").each($.proxy(function(index, item) {
			var $this = $(item);
			if (this.state) {
				if (!$this.hasClass("ytisselected")) {
					$this.addClass("ytisselected");
					this.ref._template.onSelect(this.state, $this);
					this.data.push({ "record": $this.data("ytable-record"), "tr": $this });
				}
			}
			else {
				if ($this.hasClass("ytisselected")) {
					$this.removeClass("ytisselected");
					this.ref._template.onSelect(this.state, $this);
					this.data.push({ "record": $this.data("ytable-record"), "tr": $this });
				}
			}
		}, workData));
		
		if (workData.data.length > 0) {
			this.raiseEvent((state ? "selectrecord" : "unselectrecord"), [ workData.data ]);
		}
		
	};
	
	this.getSelected = function(options) {
		
		var $table = this.getContainer().find("table > tbody");		
		var workData = { 'ref': this, 'data': [], 'withTR': false };
		
		if (options && options["addTR"] === true) workData.withTR = true;
		
		$table.find("tr").each($.proxy(function(index, item) {
			var $this = $(item);
			if ($this.hasClass("ytisselected")) {
				if (this.withTR) {
					this.data.push({ "record": $this.data("ytable-record"), "tr": $this });
				}
				else {
					this.data.push($this.data("ytable-record"));
				}
			}
		}, workData));
		
		return workData.data;
		
	};
	
	this.selectRecords = function(records) {
		
		if (!this._options.recordId) throw new "recordId undefined";
		
		var $table = this.getContainer().find("table > tbody");
		$table.find("tr").each($.proxy(function(index, item) {
			var $this = $(item);
			var find = (this.records.indexOf($this.data("ytable-record")[this.ref._options.recordId]) != -1);
			this.ref._template.onSelect(find, $this);
			if (find) { $this.addClass("ytisselected"); } else { $this.removeClass("ytisselected"); }
		}, { 'records': records, 'ref': this }));
		
		this.updateSelectAll();
		
	};
	
/*******************
	SELECTION END 
*******************/	
	
/*******************
	FUNCTIONS DEB 
*******************/	
	this.getFields = function() { return this._options.fields; };
	
	this.getContainer = function() { return this._options.container; };

	this.loadData = function(data, options) {
		
		var $table = this.getContainer().find("table > tbody");
		
		if (options && options.clear === true) $table.html("");
		
		for(var i = 0; i < data.length; i++) {
			this.raiseEvent("addrecord", [data[i]]);
			var $tr = $("<tr></tr>");
			$tr.data("ytable-record", data[i]);
			this._template.addRecord(this, data[i], $tr);
			$table.append($tr);
		}
		
		if (this._options.selectable) {
			
			$table.find("tr").on("click", $.proxy(function(e) {
				var $tr = $(e.currentTarget);
				var selState = !$tr.hasClass("ytisselected");
				this.onSelectTR(selState, $tr, true);
			}, this));
		
		}
		
		this.raiseEvent("recordsloaded", [data.length]);
		
		var offset = (this._lastAjaxData && this._lastAjaxData["yt_offset"]) ? parseInt(this._lastAjaxData["yt_offset"]) : 0;
		var nb = (options && options.totalrecords) ? parseInt(options.totalrecords) : 0;
		
		this._template.buildPagination(this, this.getPaginationData(nb, offset));
		
	};
	
	this.getNumberPerPage = function() {
		var v = this._template.getRecordsPerPage(this);
		return ($.isNumeric(v)) ? v : this._options.defaultrecordsperpage;
	};
	
	this.getPaginationData = function(nb, offset) {
		
		var data = { 'items': [], 'text': '', 'options': { 'count': nb } };
		
		var nbPerPage = this.getNumberPerPage();
		if (!nbPerPage) return data;
		
		var cpt = nb / nbPerPage;
		
		cpt = Math.ceil(cpt);
		if (cpt == 0) cpt = 1;
		
		var offset = ($.isNumeric(offset)) ? parseInt(offset) : 0;
		offsetpage = Math.ceil((offset - 1) / nbPerPage) + 1;
		if (offsetpage > cpt) offsetpage = cpt;
		
		data.options['nbpp'] = nbPerPage;
		data.options['nbpages'] = cpt;
		data.options['current_item'] = offset + 1;
		data.options["current_page"] = offsetpage;
		data.options["start_item"] = ((offsetpage - 1) * nbPerPage) + 1;
		data.options["end_item"] = offsetpage * nbPerPage;
		if (data.options["end_item"] > nb) data.options["end_item"] = nb;
		
		var nbItemsPivot = 4;
		var nbMaxItems = (nbItemsPivot * 2) + 1;
		
		var startpage = 0;
		var endpage = 0;
		if ((data.options["current_page"] - nbItemsPivot) < 1) {
			startpage = 1;
			endpage = nbMaxItems;
		}
		else {
			if (data.options["current_page"] + nbItemsPivot > cpt) {
				startpage = cpt - nbMaxItems + 1;
				endpage = cpt;
			}
			else {
				startpage = data.options["current_page"] - nbItemsPivot;
				endpage = data.options["current_page"] + nbItemsPivot;
			}
		}
		
		if (nb == 0) {
			data.options["start_item"] = 0;
			data.options["end_item"] = 0;
			data.options["current_item"] = 0;
		}
		
		var cpage = startpage;
		while(cpage <= endpage && cpage <= cpt) {
			var item = { 'itype': 'num', 'text': cpage, 'offset': (cpage - 1) * nbPerPage, 'current': (cpage == data.options["current_page"]) };
			data.items.push(item);
			cpage++;
		}
		
		return data;
		
	};
	
	this.load = function(options, filters) {
		
		this.raiseEvent("loading");
		this._template.showWait();
		
		if (options && options.reset) {
			this._lastAjaxData = null;
		}
		
		var ajaxDT = this._lastAjaxData ? this._lastAjaxData : {};
		if (options && options.urldata) {
			ajaxDT["yt_offset"] = 0;
			ajaxDT = $.extend(ajaxDT, options.urldata);
		}
		
		ajaxDT['yt_limit'] = this.getNumberPerPage();
		
		if (filters) {
			if (filters["order"]) ajaxDT["yt_order"] = filters["order"];
			if (filters["offset"]) ajaxDT["yt_offset"] = filters["offset"];
		}
		
		this._lastAjaxData = ajaxDT;
		
		$.ajax({
			type: 'POST',
			url: this._options.url.get,
			async: true,
			dataType: 'json',
			ref: this,
			data: ajaxDT,
		}).done(function(json) {
			
			if (json.err) {
				this.ref.raiseEvent("error", [json.err, json.errMessage]);
			}
			else {
				this.ref.loadData(json.records, { 'clear': true, 'totalrecords': json.totalrecords });
			}
			
		}).fail(function(e) {
			this.ref.raiseEvent("error", [e.status, e.statusText, e]);
		}).always(function() {
			this.ref._template.hideWait();
		});				
		
	};
	
/*******************
	FUNCTIONS END 
*******************/		
	
	if (this._options.selectable) {
		this._options.fields.unshift({ 'key': '_isselect', 'caption': null, 'visible': true, '_isSelect': true });
	}
	
	for(var i = 0; i < this._options.fields.length; i++) {
		this._options.fields[i]["getCaption"] = function() {
			if ($.isFunction(this.caption)) return this.caption();
			return this.caption;
		};
		this._options.fields[i]["getValue"] = function(data) {
			if ($.isFunction(this.format)) return this.format(data);
			return data[this.key];
		};
		this._options.fields[i]["isSelect"] = function(data) { return this._isSelect === true; }
		this._options.fields[i]["isVisible"] = function() { return this.visible !== false; }
		this._options.fields[i]["isSortable"] = function() { return this.sortable === true; }
	}
		
	var $html = $('<table class="ytable ytable_' + this._componentId + '"><thead></thead><tbody></tbody><tfoot></tfoot></table');
	
	var $container = this.getContainer();
	$container.html($html);

	this._template.buildTable(this, $html);
	this._template.buildPagination(this, this.getPaginationData(0));
	
};