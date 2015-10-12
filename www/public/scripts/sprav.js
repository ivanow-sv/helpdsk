
function pickerTime(elem) {
	$(elem).timepicker({
		timeOnlyTitle: 'Выберите время',
		timeText: 'Время',
		hourText: 'Часы',
		minuteText: 'Минуты',
		secondText: 'Секунды',
		currentText: 'Сейчас'
	});
}

function editBells(id)
{
	$("#formEditWrapper").togglePopup(); 
	$("#editForm #id").val(id);
	$("#editForm #starts").val($("#starts_"+id).text());
	$("#editForm #ends").val($("#ends_"+id).text());
	
}
function deleteBells(id)
{
	$("#formDelWrapper").togglePopup(); 
	$("#delForm #id").val(id);
}

function showSpravChangeKafForm(elemid, id,text,title_small)
{
	$("#"+elemid).togglePopup();
	$("#SpravChangeForm #id").val(id);
	$("#SpravChangeForm #title_small").val(title_small);
	$("#SpravChangeForm textarea").val(text);
	}

