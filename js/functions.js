function createRequest(){
	try{
		request = new XMLHttpRequest();
	} catch(tryMS) {
		try{
			request = new ActiveXObject("Msxml2.XMLHTTP");
		}catch(otherMS){
			try{
				request = new ActiveXObject("Microsoft.XMLHTTP");
			}catch(failed){
				request = null;
			}
		}
	}
	return request;
}

function cathelper(cat_name){
	return cat_name;
}

function grabword (theelement,scriptname,catname){
 
	
		
request = createRequest();
category_name = cathelper(catname);
if(request ==null){
	alert("Unable to create request");
	return;
}

updateCategories() ;
publication_id = $('#publication_id').val();
num_cats = $('#num_cats').val();
var order = $('#sortable').sortable("serialize") + '&publication_id='+publication_id+'&num_cats='+num_cats; 



var url = scriptname;
request.open("POST", url, true);
request.onreadystatechange = displayDetails;
request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
request.send(order);
 
}

function displayDetails(){
	
	request = createRequest();
	if(request.readyState==4){ 
		$.unblockUI();
		if(request.status ==200){
			//alert(request.responseText);
			var mySplitResult  = request.responseText.split('##', 4);
			cat_status = mySplitResult[0];
			//alert(mySplitResult[0]);alert(mySplitResult[1]);alert(mySplitResult[2]);alert(mySplitResult[3]);
			if(cat_status == 'story_deleted')
			{
				msgtext = 'Story deleted Successfully';
				var statusdiv = document.getElementById('story_'+mySplitResult[1]); 
				statusdiv.style.display = 'none'; 
			}
			else if(cat_status == 'active' || cat_status == 'inactive')
			{
				msgtext = 'Category '+category_name+' is '+cat_status;
				
				var category_class = category_name.replace(/\s*/g, "");
				var category_id_x = mySplitResult[3];
				var statusdiv = document.getElementById(category_class+"_"+category_id_x+"_img"); 
				statusdiv.innerHTML = mySplitResult[1];
				var refreshdiv = document.getElementById(category_class+"_refresh"); 
				refreshdiv.innerHTML = mySplitResult[2];
				
			}else
			{
				msgtext = category_name +   '  Refreshed Successfully';
			}
			document.getElementById('message').style.display = 'block';
			document.getElementById('message').style.background = 'green';
			detailDiv = document.getElementById("message");
			
			detailDiv.innerHTML = msgtext;
		}else{
			document.getElementById('message').style.display = 'block';
			document.getElementById('message').style.background = 'red';
			detailDiv = document.getElementById("message");
			detailDiv.innerHTML = category_name +  ' Refresh Failed...';
		}
	}
	
	else {
		 $.unblockUI();
	}

}
function show_category_type_model()
{
	left = ($(window).width()/2)-100;
	/*$.blockUI({ message: $('#category_type_model'), css: { left: left+'px',border: 'none' } }); */
	/*left = ($(window).width()/2)-100;
	top = ($(window).height()/2)-100;
	$('#category_type_model').css('top',top);
	$('#category_type_model').css('left',left);
	$('#category_type_model').css('display','block');*/
}
function close_category_type_model()
{
	$.unblockUI();
}

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
				
			}
		}
	}

}

// added by ashok
function refreshcategory(cid){  
	//alert("http://ci3.com/api/refreshcat/"+cid);
   $("#loader").addClass('loader');
   
	  $.ajax({url: "http://ci3.com/api/refreshcat/"+cid,
		 animation: "spinner",
		 success: function(result){
		//console.log('success');
	  $("#message").html('<span style="background:green;font-weight:bold; color:white;"> Successfuly refreshed</span>');
	 $("#loader").removeClass('loader');
	
	  //$("#message").append(result);
	  },
	  error: function (jqXhr, textStatus, errorMessage) { // error callback 
					  $('#message').html('<span style="background:red;font-weight:bold; color:white;">Error: ' + errorMessage +'</span>');
			$("#loader").removeClass('loader');
	   
		  }
	});
	  }
	  


