
; /* Start:"a:4:{s:4:"full";s:97:"/bitrix/templates/.default/components/bitrix/main.profile/company_profile/script.js?1397113553756";s:6:"source";s:83:"/bitrix/templates/.default/components/bitrix/main.profile/company_profile/script.js";s:3:"min";s:0:"";s:3:"map";s:0:"";}"*/
function removeElement(arr, sElement)
{
	var tmp = new Array();
	for (var i = 0; i<arr.length; i++) if (arr[i] != sElement) tmp[tmp.length] = arr[i];
	arr=null;
	arr=new Array();
	for (var i = 0; i<tmp.length; i++) arr[i] = tmp[i];
	tmp = null;
	return arr;
}

function SectionClick(id)
{
	var div = document.getElementById('user_div_'+id);
	if (div.className == "profile-block-hidden")
	{
		opened_sections[opened_sections.length]=id;
	}
	else
	{
		opened_sections = removeElement(opened_sections, id);
	}

	document.cookie = cookie_prefix + "_user_profile_open=" + opened_sections.join(",") + "; expires=Thu, 31 Dec 2020 23:59:59 GMT; path=/;";
	div.className = div.className == 'profile-block-hidden' ? 'profile-block-shown' : 'profile-block-hidden';
}

/* End */
;
; /* Start:"a:4:{s:4:"full";s:71:"/bitrix/components/bitrix/system.field.edit/script.min.js?1450163257462";s:6:"source";s:53:"/bitrix/components/bitrix/system.field.edit/script.js";s:3:"min";s:57:"/bitrix/components/bitrix/system.field.edit/script.min.js";s:3:"map";s:57:"/bitrix/components/bitrix/system.field.edit/script.map.js";}"*/
function addElement(e,n){if(document.getElementById("main_"+e)){var t=document.getElementById("main_"+e).getElementsByTagName("div");if(t&&t.length>0&&t[0]){var d=t[0].parentNode;d.appendChild(t[t.length-1].cloneNode(true))}}return}function addElementFile(e,n){var t=document.getElementById("main_"+e);var d=document.getElementById("main_add_"+e);if(t&&d){d=d.cloneNode(true);d.id="";d.style.display="";t.appendChild(d)}return}
/* End */
;; /* /bitrix/templates/.default/components/bitrix/main.profile/company_profile/script.js?1397113553756*/
; /* /bitrix/components/bitrix/system.field.edit/script.min.js?1450163257462*/

//# sourceMappingURL=page_96220dfd6eca2cc6329b4a87b7960366.map.js