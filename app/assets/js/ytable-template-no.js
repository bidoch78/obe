// -------------------------------------------------------------------------------------------------
// No template
// ybi: 14/02/2019
// -------------------------------------------------------------------------------------------------

var ytable_template_no = function() {
	
	this.buildTable = function(yt) {
		
		var fields = yt.getFields();
		
		var html = "<tr>";
		for(var i = 0; i < fields.length; i++) {
			if (fields[i].isVisible()) {
				if (fields[i].isSelect()) {
					if (yt._options.selectall && yt._options.multiselection) {
						html += '<th><input type="checkbox" class="ytselectall"></th>';
					}
					else {
						html += '<th></th>';
					}
				}
				else {
					html += '<th data-key="' + fields[i].key + '" class="' + (fields[i].isSortable() ? 'ytsortable' : '') + '">' + fields[i].getCaption() + '</th>';
				}
			}
		}
		html += '</tr>';
		
		yt.getContainer().find("thead").html(html);
		
		yt.getContainer().find("thead .ytsortable").on("click", $.proxy(function(e) {
			
			var $this = $(e.currentTarget);
			var key = $this.attr("data-key");
			var order = $this.attr("data-order");
			switch(order) {
				case 'desc':
					$this.attr("data-order", ""); 
					order = ""; key = "";
					break;
				case 'asc': 
					$this.attr("data-order", "desc"); 
					order = "desc";
					break;
				default:
					order = "asc";
					$this.attr("data-order", "asc");
			}
			
			yt.load(null, { 'order': key + ' ' + order.toUpperCase() });
			
		}, yt));
		
		yt.getContainer().find("thead .ytselectall").on("click", $.proxy(function(e) {
			var $this = $(e.currentTarget);
			this.onSelectAll($this.is(":checked"), false);
		}, yt));
		
	};
	
	this.buildPagination = function(yt, pagination) {
		
		var fields = yt.getFields();
		var nbFieldsDisp = 0;
		for(var i = 0; i < fields.length; i++) {
			if (fields[i].isVisible()) nbFieldsDisp++;
		}
		
		var html = "";
		html += '<tr colspan="' + nbFieldsDisp + '"><td><div>';
				
			for(var i = 0; i < pagination.items.length; i++) {
				var item = pagination.items[i];
				var txt = item.text;
				if (item.current) txt = '<b>' + txt + '</b>';
				html += '<a class="changepage" href="" data-offset="' + item.offset + '">' + txt + '</a> ';
			}
			
			html += '<select class="rperpage">';
				
			html += '</select>';
				
		html += "</div></td></tr>";
		
		var $html = $(html);
		
		$html.find("a.changepage").on("click", $.proxy(function(e) {
			this.load(null, { 'offset': $(e.currentTarget).attr("data-offset") });
			return false;
		}, yt));
		
		yt.getContainer().find("tfoot").html($html);
		
	};
	
	this.addRecord = function(yt, data, $th) {
		
		var html = '';
		var fields = yt.getFields();
		for(var i = 0; i < fields.length; i++) {
			if (fields[i].isVisible()) {
				if (fields[i].isSelect()) {
					html += '<td><input type="checkbox" class="ytselect"></td>';
				}
				else {
					html += '<td>' + fields[i].getValue(data) + '</td>';					
				}
			}
		}
		
		$th.html(html);

		$th.find(".ytselect").on("click", $.proxy(function(e) {
			e.stopImmediatePropagation();
			var $this = $(e.currentTarget);
			this.onSelectTR($this.is(":checked"), $this.closest("tr"));
		}, yt));
		
	};
	
	this.updateSelectAll = function(state, $head) {
		if (state === true) { $head.find(".ytselectall").prop({"checked": true, "indeterminate": false }); return; }
		if (state === null) { $head.find(".ytselectall").prop({"checked": false, "indeterminate": true }); return; }
		$head.find(".ytselectall").prop({"checked": false, "indeterminate": false });
	};
	
	this.onSelect = function(state, $tr) {
		$tr.find(".ytselect").prop("checked", state);
	};

	this.showWait = function(yt) {
		
	};

	this.hideWait = function(yt) {
		
	};
	
	this.getRecordsPerPage = function(yt) {
		return null;
	};
	
};