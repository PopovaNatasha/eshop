
var obj_struct = new Array();
var table = 'product';

initialize();

function initialize()
{
	$('.add-button').on('click',function(){
		newItem(table);
	});

	$('.nav-button').on('click',function(){
		table = $(this).data('table');

		$('.nav').find('li').removeClass('active');
		$(this).parent().addClass('active');

		$('.table-header-td').empty();
		$('.row').remove();
		getList(table);
	});
	getList('product');
}


function initializeTable (data) {
	let header = $('.table-header-td');
	for (var i = 0; i < obj_struct.length; i++) {
		let elem = $(`<td class="table-td table-header-td">
							${obj_struct[i].name}
					  </td>`);
		header.append(elem);
	}
	header.append(`<td class="table-td table-header-td"></td>`);
	header.append(`<td class="table-td table-header-td"></td>`);


	for (var i = 0; i < data.length; i++) {
		addNewObj(data[i], i)

	}

}


function addNewObj(obj) {
	let row = $(`
				<tr class="table-tr row row-${obj[0]}">

				</tr>
			`)
		for (var i = 0; i < obj.length; i++) {
			let type = obj_struct[i].type;

			let elem = $(`
					<td class="table-td">
						<div class="cell-content-div">
							<div class="cell-text-div">
								
							</div>	
						</div>
					</td>
				`);

			switch(type) {
				case 'id':
			    {
			    	elem.find('.cell-text-div').append(`<input class="cell-input" type="number" data-name="" disabled value="${obj[i]}">`);
			    	break;

			    }
			  	case 'bool':
			    {
			    	elem.find('.cell-text-div').append(`<input class="cell-input" type="text" value="${obj[i]}">`);
			    	break;

			    }
			  	case 'number':
			    {
			    	elem.find('.cell-text-div').append(`<input class="cell-input" type="number" value="${obj[i]}">`);
			    	break;

			    }
			  	case 'text':
			    {
			    	elem.find('.cell-text-div').append(`<input class="cell-input" type="text" value="${obj[i]}">`);
			    	break;

			    }

			  	default:
			    {
			    	console.log( 'obj_struct type is undefined');

			    }
			}
			row.append(elem);
		}
		row.append(`
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-button-div">
						<button class="btn save-button submit-button">Сохранить</button>
					</div>
				</div>
			</td>`
		);
		row.append(`
				<td class="table-td">
					<div class="cell-content-div">
						<div class="cell-button-div">
							<button class="btn delete-button danger-button" onclick = "openSubmitModal(${obj[0]})" >Удалить</button>
						</div>
					</div>
				</td>`
		);
		$('.admin-table').append(row);

}
function newItem(table){
	$.ajax({
		url: '/admin/newItem',
		method: 'get',
		dataType: 'json',
		data: {table: table},
		success: function(data){
			addNewObj( data);
		}
	});
}

function getList(dataTable)
{
	$.ajax({
		url: '/admin/getList',         /* Куда отправить запрос */
		method: 'get',             /* Метод запроса (post или get) */
		dataType: 'json',          /* Тип данных в ответе (xml, json, script, html). */
		data: {table: dataTable},     /* Данные передаваемые в массиве */
		success: function(data){   /* функция которая будет выполнена после успешного запроса.  */
			obj_struct = data[0];
			initializeTable( data[1]);/* В переменной data содержится ответ от index.php. */
		}
	});
}


function openSubmitModal(id){

	let modal = `
		<div class="submit-modal">
			<div class="submit-modal-dialog">
				<div class="submit-modal-content">
					<div class="submit-modal-header">
						<a href="#" title="Close" class="cancel-button">×</a>
					</div>
					<div class="submit-modal-body">    
						<p>Вы уверены, что хотите удалить элемент?</p>
						<button class="btn save-button submit-button" style="margin-right: 10px">Удалить</button>
						<button class="btn danger-button cancel-button">Отменить</button>
					</div>
				</div>
			</div>
		</div>
	`
	$('body').append(modal);

	$('.cancel-button').on('click', function(){
		$('.submit-modal').remove();
	});

	$('.submit-modal').find('.submit-button').on('click', function(){
		$.ajax({
			url: '/admin/deleteItem',
			method: 'post',
			dataType: 'json',
			data: {table: table, id: id},
			success: function(data){
				console.log(data);
				if (data){
					$(`.row-${id}`).remove();
					$('.submit-modal').remove();
				}else{
					alert('Что-то пошло не так!');
				}
			}
		});
	});
}

