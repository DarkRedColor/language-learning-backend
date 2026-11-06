$(document).ready(function(){
// SEARCH BY FILTER
String.prototype.score = function (word, fuzziness) {
	'use strict';

	// If the string is equal to the word, perfect match.
	if (this === word) { return 1; }

	//if it's not a perfect match and is empty return 0
	if (word === "") { return 0; }

	var runningScore = 0,
		charScore,
		finalScore,
		string = this,
		lString = string.toLowerCase(),
		strLength = string.length,
		lWord = word.toLowerCase(),
		wordLength = word.length,
		idxOf,
		startAt = 0,
		fuzzies = 1,
		fuzzyFactor,
		i;

	// Cache fuzzyFactor for speed increase
	if (fuzziness) { fuzzyFactor = 1 - fuzziness; }

	// Walk through word and add up scores.
	// Code duplication occurs to prevent checking fuzziness inside for loop
	if (fuzziness) {
	  for (i = 0; i < wordLength; i+=1) {

		// Find next first case-insensitive match of a character.
		idxOf = lString.indexOf(lWord[i], startAt);

		if (idxOf === -1) {
		  fuzzies += fuzzyFactor;
		} else {
		  if (startAt === idxOf) {
			// Consecutive letter & start-of-string Bonus
			charScore = 0.7;
		  } else {
			charScore = 0.1;

			// Acronym Bonus
			// Weighing Logic: Typing the first character of an acronym is as if you
			// preceded it with two perfect character matches.
			if (string[idxOf - 1] === ' ') { charScore += 0.8; }
		  }

		  // Same case bonus.
		  if (string[idxOf] === word[i]) { charScore += 0.1; }

		  // Update scores and startAt position for next round of indexOf
		  runningScore += charScore;
		  startAt = idxOf + 1;
		}
	  }
	} else {
	  for (i = 0; i < wordLength; i+=1) {
		idxOf = lString.indexOf(lWord[i], startAt);
		if (-1 === idxOf) { return 0; }

		if (startAt === idxOf) {
		  charScore = 0.7;
		} else {
		  charScore = 0.1;
		  if (string[idxOf - 1] === ' ') { charScore += 0.8; }
		}
		if (string[idxOf] === word[i]) { charScore += 0.1; }
		runningScore += charScore;
		startAt = idxOf + 1;
	  }
	}

	// Reduce penalty for longer strings.
	finalScore = 0.5 * (runningScore / strLength    + runningScore / wordLength) / fuzzies;

	if ((lWord[0] === lString[0]) && (finalScore < 0.85)) {
	  finalScore += 0.15;
	}

	return finalScore;

  };

  // http://ejohn.org/apps/livesearch/jquery.livesearch.js
  jQuery.fn.liveUpdate = function(list) {
	list = jQuery(list);

	if (list.length) {
		var elem = list.children('.text, .bg-cards, .char, .adv, .item')
	  	var rows = elem.children('.elem, .bg-name, .label, a p, .item p, .item .title')

		cache = rows.map(function() {
		  	return this.innerHTML.toLowerCase();
		});

	  this
		.keyup(filter).keyup()
		// .parents('form').submit(function() {
		//   return false;
		// });
	}

	return this;

	function filter() {
	  var term = jQuery.trim(jQuery(this).val().toLowerCase()),
		scores = [];

	  if (!term) {
		rows.show();
		elem.show();
		$('.add-bg, .form_store').show();
	  } else {
		rows.hide();
		elem.hide();
		$('.add-bg, .form_store').hide();
		cache.each(function(i) {
		  var score = this.score(term);
		  if (score > 0) {
			scores.push([score, i]);
		  }
		});

		jQuery.each(scores.sort(function(a, b) {
		  return b[0] - a[0];
		}), function() {
		  jQuery(rows[this[1]]).show();
		  jQuery(elem[this[1]]).show();
		});
	  }
	}
  };
  $("#filterInput").liveUpdate("#filterList, #admin_seasons_list, #legendList");


// keyword highlight
$(function(){
    $('#filterInput, #admin_seasons_list, #legendList').on('keyup change', function(ev) {
        // pull in the new value
        var term = $(this).val().trim();

        // remove any old highlighted terms
        $('#filterList, #admin_seasons_list, #legendList').removeHighlight();

        // disable highlighting if empty
        if ( term ) {
            // highlight the new term
            $('#filterList, #admin_seasons_list, #legendList').highlight( term );
        }
	}).after(function(){
		// filtered items show first
		let sortItems = $("#filterList span.highlight,  #admin_seasons_list span.highlight, #legendList span.highlight").parents('#filterList > *, #admin_seasons_list > *');
		sortItems.sort(function(a, b){
			return $(b).hasClass("highlight") - $(a).hasClass("highlight");
		}).prependTo("#filterList, #admin_seasons_list, #legendList");
	});
})

jQuery.fn.highlight = function(pat) {
	function innerHighlight(node, pat) {
	 var skip = 0;
	 if (node.nodeType == 3) {
	  var pos = node.data.toUpperCase().indexOf(pat);
	  if (pos >= 0) {
	   var spannode = document.createElement('span');
	   spannode.className = 'highlight';
	   var middlebit = node.splitText(pos);
	   var endbit = middlebit.splitText(pat.length);
	   var middleclone = middlebit.cloneNode(true);
	   spannode.appendChild(middleclone);
	   middlebit.parentNode.replaceChild(spannode, middlebit);
	   skip = 1;
	  }
	 }
	 else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
	  for (var i = 0; i < node.childNodes.length; ++i) {
	   i += innerHighlight(node.childNodes[i], pat);
	  }
	 }
	 return skip;
	}
	return this.each(function() {
	 innerHighlight(this, pat.toUpperCase());
	});
   };

   jQuery.fn.removeHighlight = function() {
	function newNormalize(node) {
	   for (var i = 0, children = node.childNodes, nodeCount = children.length; i < nodeCount; i++) {
		   var child = children[i];
		   if (child.nodeType == 1) {
			   newNormalize(child);
			   continue;
		   }
		   if (child.nodeType != 3) { continue; }
		   var next = child.nextSibling;
		   if (next == null || next.nodeType != 3) { continue; }
		   var combined_text = child.nodeValue + next.nodeValue;
		   new_node = node.ownerDocument.createTextNode(combined_text);
		   node.insertBefore(new_node, child);
		   node.removeChild(child);
		   node.removeChild(next);
		   i--;
		   nodeCount--;
	   }
	}

	return this.find("span.highlight").each(function() {
	   var thisParent = this.parentNode;
	   thisParent.replaceChild(this.firstChild, this);
	   newNormalize(thisParent);
	}).end();
};
});