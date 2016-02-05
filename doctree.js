/*
 * PocketMine-Plugin-Tutorials
 *
 * Copyright (C) 2015 PEMapModder
 *
 * @author PEMapModder
 */

var runtimeAutoId = 0;

/**
 * Registers a new spoiler.
 * @param el
 * @returns jQuery $(div of spoiler button)
 */
function registerSpoiler(el){
	el.addClass("spoiler");
	var id = runtimeAutoId++;
	var onclick = 'switchSpoiler("' + id + '"); return false;';
	var button = $("<button class='button spoiler-opener'>Show</button>");
	var before = $("<div></div>");
	button.appendTo(before);
	before.append("<hr>");
	button.attr("data-spoiler-name", id);
	button.attr("onclick", onclick);
	before.insertBefore(el);
	el.attr("data-spoiler-name", id);
	el.css("display", "none");
	return before;
}

/**
 * Toggles a spoiler.
 * @param id
 * @returns {boolean} true if spoiler is opened, false if spoiler is closed
 */
var switchSpoiler = function(id){
	var el = $(".spoiler[data-spoiler-name='" + id + "']");
	var opener = $(".spoiler-opener[data-spoiler-name='" + id + "']");
	if(el.css("display") === "none"){
		el.css("display", "block");
		opener.text("Hide");
		return true;
	}
	el.css("display", "none");
	opener.text("Show");
	return false;
};

function Tree(name, id, depthClass){
	this.name = name;
	this.id = id;
	this.depthClass = depthClass;
	this.children = {};
}
Tree.prototype.addChild = function(child){
	this.children[child.name] = child;
};
Tree.prototype.toOlJQuery = function(){
	var a = $("<a></a>");
	a.addClass("branch");
	a.attr("data-target", this.id);
	a.attr("href", "#" + this.id);
	a.text(this.name);
	var out = $("<li></li>");
	a.appendTo(out);
	out.addClass(this.depthClass);
	var $ol = $("<ol></ol>");
	for(var name in this.children){
		if(this.children.hasOwnProperty(name)){
			this.children[name].toOlJQuery().appendTo($ol);
		}
	}
	$ol.appendTo(out);
	return out;
};

var tree;
var trees = {};

function gotoAnchor(anchor){
	var target = $("a[name='" + anchor + "']");
	target.parents(".tree").each(function(){
		var $this = $(this);
		if($this.css("display") == "none"){
			switchSpoiler($this.attr("data-spoiler-name"));
		}
	});
	$("html, body").animate({
		scrollTop: Math.max(0, target.parent().prev().offset().top + window.innerHeight * (-0.1))
	}, 200, "swing", function(){
		var header = target.parent().prev();
		header.css("background-color", "#B11D98");
		header.animate({
			backgroundColor: "#FFFFFF"
		}, 600);
	});
}

var hashBlocker = 0;

$(document).ready(function(){
	var maxDepth = 12;
	var nextAnchorId = 0;
	$(".tree").each(function(){
		var $this = $(this);
		var name = $this.attr("data-name");
		var parents = $this.parents(".tree");
		var anchorId = "anchor-auto-" + (nextAnchorId++);
		$this.prepend('<a name="' + anchorId + '"></a>');
		var clazz = "depth-" + parents.length;
		if(!$this.hasClass("no-index")){
			if(parents.length == 0){
				var tmpTree = new Tree(name, anchorId, clazz);
				trees[name] = tmpTree;
				if(this.id === "mainTree"){
					tree = tmpTree;
				}
			}else{
				var $parent = $(parents[0]);
				var parentTree = trees[$parent.attr("data-name")];
				var t;
				parentTree.addChild(t = new Tree(name, anchorId, clazz));
				trees[name] = t;
			}
		}
		var depth = parents.length;
		maxDepth = Math.max(maxDepth, depth);
		var $div = registerSpoiler($this);
		var bef = $("<span></span>");
		bef.text(name + " ");
		var id = $div.children("button").attr("data-spoiler-name");
		var onclick = 'switchSpoiler("' + id + '");';
		bef.attr("onclick", onclick);
		bef.prependTo($div);
		$div.attr("data-depth", depth);
		$div.addClass("heading");
		if(depth > 0){
			$div.before("<br>");
		}
	});
	$(".heading").each(function(){
		var $this = $(this);
		var depth = $this.attr("data-depth");
		$this.css("font-size", (32 - Math.floor(depth / maxDepth * 20)) + "px");
	});
	switchSpoiler("0");
	switchSpoiler("1");
	var $contents = $("#index");
	$contents.append("<p style='text-align: center;'><strong>Contents</strong></p>");
	var ol = tree.toOlJQuery();
	ol.children().each(function(){
		$contents.append(this);
	});
	$("a").click(function(){
		var $this = $(this);
		if(typeof $this.attr("href") !== typeof undefined){
			if($this.attr("href").charAt(0) === "#"){
				gotoAnchor($this.attr("href").substring(1));
			}
		}
	});
//			$(".branch").click(function(){
//				var $this = $(this);
//				var targetName = $this.attr("data-target");
//				gotoAnchor(targetName);
//			});
	$("#body").css("padding-bottom", $(window).height() / 3);
	var hasher = function(){
		if(hashBlocker > 0){
			setTimeout(hasher, 100);
			return;
		}
		var hash = window.location.hash;
		if(hash.charAt(0) === "#"){
			gotoAnchor(hash.substring(1));
		}
	};
	setTimeout(hasher, 100);
});

