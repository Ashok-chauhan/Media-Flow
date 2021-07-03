
function updateCategories() {
	var numCats = document.getElementById('num_cats').value;
	var formElem = document.getElementById('catForm');
	var catList = new Array();

	var activeCatListElem = document.getElementById('sortable');
	var activeChildList = activeCatListElem.childNodes;
	if (activeChildList.length > 0) {
		for (var i in activeChildList) {
			if (activeChildList[i].tagName == 'LI') {
				var numCats = catList.length;
				catList[numCats] = activeChildList[i];
				var inputID = 'cat_' + numCats;
				
				/*var inputElem = document.createElement("input");
				inputElem.type = 'hidden';
				inputElem.id = inputID;
				inputElem.name = inputID;
				inputElem.value = catList[numCats].id + '__active';
				formElem.appendChild(inputElem);*/
			}
		}
	}

	/*var inactiveCatListElem = document.getElementById('inactiveCatList');
	var inactiveChildList = inactiveCatListElem.childNodes;
	if (inactiveChildList.length > 0) {
		for (var i in inactiveChildList) {
			if (inactiveChildList[i].tagName == 'LI') {
				var numCats = catList.length;
				catList[numCats] = inactiveChildList[i];
				var inputID = 'cat_' + numCats;
				var inputElem = document.createElement("input");
				inputElem.type = 'hidden';
				inputElem.id = inputID;
				inputElem.name = inputID;
				inputElem.value = catList[numCats].id + '__inactive';
				formElem.appendChild(inputElem);
			}
		}
	}*/
}
