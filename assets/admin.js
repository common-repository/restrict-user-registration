/**
 * Restrict User Registration Plugin JS
 * Author: @Samuel_Elh
 */


window.addEventListener('load', function() {

	var area1 = document.getElementById('add-username');
	var area2 = document.getElementById('add-email');
	var area3 = document.getElementById('add-service');

	var arr1 = area1.value.split(',');
	var arr2 = area2.value.split(',');
	var arr3 = area3.value.split(',');

	for(var i=0; i<arr1.length; ++i){
		if(arr1[i] !== '' && null !== arr1[i]) {
			var div = document.createElement('div');
			div.setAttribute('id', arr1[i].replace(/[^a-zA-Z0-9]/g, '-'));
			div.innerHTML = '<span class="cont" onclick="rur_edit(this,\''+arr1[i]+'\',\'add-username\');"  title="edit">'+arr1[i]+'</span><span class="del" onclick="rur_remove(\''+arr1[i]+'\',\'add-username\')" title="remove"></span>';
			document.getElementById('add-username-cont').appendChild(div);
			area1.style.display = 'none';
		}	
	};
	for(var i=0; i<arr2.length; ++i){
		if(arr2[i] !== '' && null !== arr2[i]) {
			var div = document.createElement('div');
			div.setAttribute('id', arr2[i].replace(/[^a-zA-Z0-9]/g, '-'));
			div.innerHTML = '<span class="cont" onclick="rur_edit(this,\''+arr2[i]+'\',\'add-email\');"  title="edit">'+arr2[i]+'</span><span class="del" onclick="rur_remove(\''+arr2[i]+'\',\'add-email\')" title="remove"></span>';
			document.getElementById('add-email-cont').appendChild(div);
			area2.style.display = 'none';
		}
	};
	for(var i=0; i<arr3.length; ++i){
		if(arr3[i] !== '' && null !== arr3[i]) {
			var div = document.createElement('div');
			div.setAttribute('id', arr3[i].replace(/[^a-zA-Z0-9]/g, '-'));
			div.innerHTML = '<span class="cont" onclick="rur_edit(this,\''+arr3[i]+'\',\'add-service\');"  title="edit">'+arr3[i]+'</span><span class="del" onclick="rur_remove(\''+arr3[i]+'\',\'add-service\')" title="remove"></span>';
			document.getElementById('add-service-cont').appendChild(div);
			area3.style.display = 'none';
		}	
	};

}, false);


function rur_add(target) {
	var word = window.prompt('Enter data:');
	if(word == 0 || word === null) {
        if(word !== null)
            alert('Please type something');
        return false;
	}
	word = word.replace(/"/g, '');
	var eles = document.getElementById(target).value.split(',');
	var count = 0;
	for(var i=0; i<eles.length; ++i){
		if(eles[i] !== word) {
			count += 0;
		} else {
			count += 1;
		}
	};
	if( count > 0 ) {
		alert('Data "'+word+'" already added');
	} else {
		document.getElementById(target).value += ','+word+',';
		var div = document.createElement('div');
		div.setAttribute('id', word.replace(/[^a-zA-Z0-9]/g, '-'));
		div.innerHTML = '<span class="cont" onclick="rur_edit(this,\''+word.replace(/'/g, "\\'").replace(/"/g, '')+'\',\''+target+'\');" title="edit">'+word+'</span><span class="del" onclick="rur_remove(\''+word.replace(/'/g, "\\'").replace(/"/g, '')+'\',\''+target+'\')" title="remove"></span>';
		document.getElementById(target+'-cont').appendChild(div);
	}
}
function rur_remove(word, targeted) {
	document.getElementById(word.replace(/[^a-zA-Z0-9]/g, '-')).remove();
	var target = document.getElementById(targeted);
	var eles = target.value.split(',');
	target.value = '';
	for(var i=0; i<eles.length; ++i){
		if(eles[i] !== word) {
			target.value += eles[i]+',';
		}
	};
}
function rur_edit(selector, word, targeted) {
	var edit = window.prompt('Edit data:', word);
	if(edit == 0 || edit === null) {
        if(edit !== null)
            alert('Please enter something');
        return false;
	}
	edit = edit.replace(/"/g, '');
	var target = document.getElementById(targeted);
	var eles = target.value.split(',');
	var count = 0;
	for(var i=0; i<eles.length; ++i){
		if(eles[i] !== edit) {
			count += 0;
		} else {
			count += 1;
		}
	};
	if(count > 0) {
		if( word !== edit )
			alert('This data is already added.');
		return false;
	} else {
		target.value = '';
		for(var i=0; i<eles.length; ++i){
			if(eles[i] == word) {
				target.value += edit+',';
			} else {
				target.value += eles[i] == 0 || null == eles[i] ? '' : eles[i]+',';			
			}
		};
	}
	selector.innerHTML = edit;
	selector.setAttribute('onclick', 'rur_edit(this, \''+edit.replace(/'/g, "\\'").replace(/"/g, '')+'\',\''+targeted+'\');');
	selector.parentElement.setAttribute('id', edit.replace(/[^a-zA-Z0-9]/g, '-'));
	document.querySelector( '#'+selector.parentElement.getAttribute('id')+' .del' ).setAttribute('onclick', 'rur_remove(\''+edit.replace(/'/g, "\\'").replace(/"/g, '')+'\',\''+targeted+'\');');
}