let obj_struct = [];
let table = 'product';
let global_product_tags = [];
initialize();

function initialize()
{
	$('.add-button').on('click', () => {
		newItem(table);
	});

	$('.nav-button').on('click', function() {
		table = $(this).data('table');

		$('.nav').find('li').removeClass('active');
		$(this).parent().addClass('active');

		$('.table-header-td').empty();
		$('.row').remove();

		getList(table);
	});

	getList('product');
}

function initializeTable(data)
{
	let header = $('.table-header-td');

	for (let i = 0; i < obj_struct.length; i++) {
		const elem = $(`<td class="table-td table-header-td">
							${ obj_struct[i].name }
					  </td>`);
		header.append(elem);
	}
	let additionalButtonsCount = 4;

	if (table=='tag'){
		additionalButtonsCount = 3;
	}
	for (let i = 1; i < additionalButtonsCount; i++) {
		header.append(`<td class="table-td table-header-td"></td>`);
	}

	for (let i = 0; i < data.length; i++) {
		addNewObj(data[i]);
	}

	$('.cell-input[type=checkbox]').change(function() {
		$(this).val($(this).is(':checked'));
	});

	for (let i = 0; i < obj_struct.length; i++)
	{
		if (obj_struct[i].type === 'select')
		{
			setSelectFieldData(obj_struct[i].field);
		}
	}

	if (table==='product'){
		$.ajax({
			url: '/admin/getList',
			method: 'get',
			dataType: 'json',
			data: { table: 'tag' },
			success: function(data) {
				for (let i = 0; i < data[1].length; i++)
				{
					global_product_tags[data[1][i][0]] = data[1][i][1];
				}
				$.ajax({
					url: '/admin/getProductTagRelation',
					method: 'get',
					dataType: 'json',
					success: function(data) {
						setProductTags(data);
					}
				});

			}
		});
	}

}
function setProductTags(data){
	let productTagsNames = [];
	let productTagsIds = [];
	if (data !== undefined) {
		for (let i = 0; i < data.length; i++)
		{
			if (productTagsNames[data[i][0]] !== undefined){
				productTagsNames[data[i][0]] += ", " + global_product_tags[data[i][1]];
				productTagsIds[data[i][0]].push(data[i][1]);
			}else{
				productTagsNames[data[i][0]] = global_product_tags[data[i][1]];
				productTagsIds[data[i][0]] = [data[i][1]];
			}
		}


		$(`.row`).find(`*[data-field = TAGS]`).data('tags', [1]);
		for (let i = 0; i < productTagsNames.length; i++)
		{
			$(`.row-${i}`).find(`*[data-field = TAGS]`).val( productTagsNames[i] );
			$(`.row-${i}`).find(`*[data-field = TAGS]`).data('tags', productTagsIds[i]);
		}
		$(`.row`).find(`*[data-field = TAGS]`).on('click', function(){
			//openSelectTagModal($(this));
		});

	}
}

function addNewObj(obj) {
	const row = $(`<tr class="table-tr row row-${obj[0]}"></tr>`);
	for (let i = 0; i < obj.length; i++) {
		const type = obj_struct[i].type;
		const dataField = obj_struct[i].field;
		const elem = $(`
						<td class="table-td">
							<div class="cell-content-div">
								<div class="cell-text-div"></div>
							</div>
						</td>
					`);

		switch(type) {
			case 'id':
			{
				elem.find('.cell-text-div').append(`<input class="cell-input" type="number" data-field="${dataField}" disabled value="${obj[i]}">`);
				break;
			}
			case 'bool':
			{
				const checked = obj[i] ? 'checked' : '';
				elem.find('.cell-text-div')
					.append(`
									<input class="cell-input toggle" type="checkbox"  data-field="${dataField}"  value="${obj[i]}" ${checked}>
									<label class="toggle-item"></label>
								`);
				break;
			}
			case 'number':
			{
				elem.find('.cell-text-div').append(`<input class="cell-input" type="number" data-field="${dataField}" value="${obj[i]}">`);
				break;
			}
			case 'text':
			{
				elem.find('.cell-text-div').append(`<input class="cell-input" type="text" data-field="${dataField}" value="${obj[i]}">`);
				break;
			}
			case 'select':
			{
				elem.find('.cell-text-div').append(`
					<select class="cell-select" data-field="${dataField}">
						<option value="${obj[i]}" selected>${obj[i]}</option>
					</select>`
				);
				break;
			}
			case 'checkboxes':
			{
				elem.find('.cell-text-div').append(`<input class="cell-input" type="button" data-tags data-field="${dataField}" value="Без тега">`);

				elem.find('.cell-text-div').find('input').data('tags', [1]);
				elem.find('.cell-text-div').find('input').on('click', function(){
					openSelectTagModal($(this));
				});
				break;
			}
			default:
			{
				console.log(type + ' of obj_struct is undefined');
			}
		}

		row.append(elem);
	}

	if (table === 'order')
	{
		row.append(`
		<td class="table-td">
			<div class="cell-content-div">
				<div class="cell-button-div">
					<button class="btn save-button submit-button" onclick = "document.location='/admin/order/${obj[0]}/'">Продукты</button>
				</div>
			</div>
		</td>`
		);
	}

	if (table === 'product')
	{
		row.append(`
			<td class="table-td">
				<div class="cell-content-div">
					<div class="cell-button-div">
						<button class="btn save-button submit-button" onclick = "document.location='admin/image/${obj[0]}/'">Изображения</button>
					</div>
				</div>
			</td>`
		);
	}

	row.append(`
		<td class="table-td">
			<div class="cell-content-div">
				<div class="cell-button-div">
					<button class="btn save-button submit-button" onclick="saveItem(${obj[0]})">Сохранить</button>
				</div>
			</div>
		</td>`
	);

	row.append(`
		<td class="table-td">
			<div class="cell-content-div">
				<div class="cell-button-div">
					<button class="btn delete-button danger-button" onclick="openSubmitModal(${obj[0]})">Удалить</button>
				</div>
			</div>
		</td>`
	);

	$('.admin-table').append(row);
}

function newItem(table) {

	$.ajax({
		url: '/admin/newItem',
		method: 'get',
		dataType: 'json',
		data: { table },
		success: function(data) {
			addNewObj(data);
			setSelectFieldData("VINIL_STATUS");
			$('.cell-input[type=checkbox]').change(function() {
				$(this).val($(this).is(':checked'));
			});
		}
	});
}

function saveItem(id) {
	const inputs = $(`.row-${id}`).find('input');

	const obj = [];

	inputs.push($(`.row-${id}`).find('select'));

	for (let i = 0; i < inputs.length; i++)
	{
		const newField = {};
		newField.field = $(inputs[i]).data('field');
		newField.value = $(inputs[i]).val();
		obj.push(newField);
	}

	$.ajax({
		url: '/admin/setItem',
		method: 'post',
		dataType: 'json',
		data: { table, obj },
		success: function(data) {
			if (Array.isArray(data) && data.length)
			{
				alert(data[0]);
			}
			else
			{
				alert("Элемент сохранён");
			}
		}
	});

	if (table==='product'){
		let tagItem = $(`.row-${id}`).find('input[data-field = TAGS]');
		let TagsIds = $(tagItem).data('tags');
		$.ajax({
			url: '/admin/setProductTag',
			method: 'post',
			dataType: 'json',
			data: { productId: id, tags: TagsIds },
			success: function(data) {
				console.log(data);
			}
		});
	}
}

function getList(dataTable)
{
	$.ajax({
		url: '/admin/getList',
		method: 'get',
		dataType: 'json',
		data: { table: dataTable },
		success: function(data) {
			obj_struct = data[0];
			initializeTable(data[1]);
		}
	});
}

function openSelectTagModal(elem){

	const modal = $(`
		<div class="tags-modal">
			<div class="tags-modal-dialog">
				<div class="tags-modal-content">
					<div class="tags-modal-header">
						<span title="Close" class="cancel-button">×</span>
					</div>
					<div class="tags-modal-body">    
						<fieldset class="tags-modal-tags-set">
							<legend>Теги:</legend>	
						</fieldset>
					</div>
					<div class="tags-modal-footer">
						<button class="btn save-button submit-button" style="margin-right: 10px">Ок</button>
					</div>
				</div>
			</div>
		</div>
	`);

	let selectedTags = elem.data('tags');
	for (let i = 0; i < global_product_tags.length; i++)
	{
		if (global_product_tags[i] != undefined){
			let checked = selectedTags.includes(i) ? 'checked' : '';
			let Tag = $(`<div class="tags-modal-tag-div">
						<input type="checkbox" id="tag-id-${i}" data-tag-id="${i}" name="${global_product_tags[i]}" ${checked}>
						<label for="tag-id-${i}">${global_product_tags[i]}</label>
					</div>`);
			$(modal).find('fieldset').append(Tag);
		}
	}

	$(modal).find('.tags-modal-tag-div').find('input').on('change', function(){

		if ( $(this).data('tag-id') != '1' ){
			$(modal).find('.tags-modal-tag-div').find('input[data-tag-id = 1]').prop('checked', false);
		}else{
			$(modal).find('.tags-modal-tag-div').find('input').prop('checked', false);
			$(modal).find('.tags-modal-tag-div').find('input[data-tag-id = 1]').prop('checked', true);
		}
	});

	$(modal).find('.save-button').on('click', function(){
		tagInputs = $(modal).find('.tags-modal-tag-div').find('input')
		newSelectedTagsIds = [];
		newSelectedTagsNames = '';
		for (let i = 0; i < tagInputs.length; i++)
		{
			if ( $(tagInputs[i]).is(':checked') ){
				newSelectedTagsIds.push( $(tagInputs[i]).data('tag-id') )
				if (newSelectedTagsNames != '') {
					newSelectedTagsNames += ', ' + global_product_tags[$(tagInputs[i]).data('tag-id')];
				}else{
					newSelectedTagsNames = global_product_tags[$(tagInputs[i]).data('tag-id')];
				}
			}
		}
		$(elem).data('tags', newSelectedTagsIds);
		$(elem).val(newSelectedTagsNames);
		$(modal).remove();
	});

	$(modal).find('.cancel-button').on('click', function(){
		$(modal).hide();
	});

	$('body').append(modal);
}

function setSelectFieldData(field) {
	$.ajax({
		url: '/admin/getSelectFieldData',
		method: 'get',
		dataType: 'json',
		data: { field },
		success: function(data) {
			const selects = $(`*[data-field = ${field}]`);

			for (let i = 0; i < selects.length; i++)
			{
				for (let j = 0; j < data.length; j++)
				{
					if ( $(selects[i]).find('option:selected').val() != data[j] ) {
						$(selects[i]).append(`<option value="${data[j]}">${data[j]}</option>`);
					}
				}
			}
		}
	});

}

function openSubmitModal(id) {
	const modal = `
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
		</div>`

	$('body').append(modal);

	$('.cancel-button').on('click', () => {
		$('.submit-modal').remove();
	});

	$('.submit-modal').find('.submit-button').on('click', function() {
		$.ajax({
			url: '/admin/deleteItem',
			method: 'post',
			dataType: 'json',
			data: { table, id},
			success: function(data) {
				if (data) {
					$(`.row-${id}`).remove();
					$('.submit-modal').remove();
				}
				else {
					alert('Что-то пошло не так!');
				}
			}
		});
	});
}


