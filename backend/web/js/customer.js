/**
 * @author yong
 */

/*
 * 只需要调用此方法既可以用checkboxTree
 */
function checkboxTree(selector, defaultData){
    $(selector).treeview({
	    data: defaultData,
	    showIcon: false,
	    showCheckbox: true,
	    onNodeChecked: function(event, node) {
	    	getParentNode(node, 'checkNode', selector);
	    	getChildrenNode(node, 'checkNode', selector);
		},
		onNodeUnchecked: function (event, node) {
			getChildrenNode(node, 'uncheckNode', selector);
	    }
   });
}

function getParentNode(node, a, selector){
    if(node.parentId !== undefined){
        pnode=$(selector).treeview('getParent', node);
		$(selector).treeview(a, [ pnode.nodeId, { silent: true } ]);
		getParentNode(pnode, a, selector);
	}else{
	   return;
	}
}

function getChildrenNode(node, a, selector){
    if(node.nodes != undefined){
	    var cnodes = node.nodes;
		for(var i in cnodes){
		   var cnode = cnodes[i];
		   $(selector).treeview(a, [ cnode.nodeId, { silent: true } ]);
		   getChildrenNode(cnode, a, selector);
		}
	}else{
		 return;
	}
}

function date(selector){
	$.fn.datetimepicker.dates['zh-CN'] = {
			days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
			daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
			daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
			months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
			monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
			today: "今天",
			suffix: [],
			meridiem: ["上午", "下午"]
	};
	$(selector).datetimepicker({
	    language: 'zh-CN',
	    format: 'yyyy-mm-dd',
		autoclose: true,
		minView: 2,
	});
}

	