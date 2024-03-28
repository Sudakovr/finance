const _U = "/finance/";
BX.ready(function(){ 
    $('.popup_form').on('submit', function(e){
        e.preventDefault()
        $.ajax({
            type: "POST",
            url: '?f=post&e='+$(this).find("[name='elem_id']").val(),
            data: $(this).serialize(),
            success: function(data){
                location.href='';
            }
        });
    });
    $('.button_popup_cancel').click(function(e){
        e.preventDefault()
        $('.finance_popup').removeClass("finance_popup_show")
        $('.finance_popup_box_data').html('')
    })

    $('.sendToEdit').click(function(e){
        e.preventDefault()
        let _this = $(this)
        let _id = _this.data("id")
        let _in = _this.data("in")
        let _pf = _this.data("pf")
        $('.finance_popup').addClass("finance_popup_show")
        $.get('?f=get&e='+_id+'&type_io='+_in+'&pf='+_pf, function(data){
            $('.finance_popup_box_data').html(data)
        })
    })

    $('.sendToFact').click(function(e){
        e.preventDefault()
        let _this = $(this)
        let _id = _this.data("id")
        let _in = _this.data("in")
        if(_in == "in"){
            _in_s = ".finance_in";
        }else{
            _in_s = ".finance_out";
        }
        let _box = $('.finance_popup_box_confirm')
        _box.css('display','block')
        _box.html('<div class="popup_confirm"><b>Перенести в факт?</b></div><div><button class="ui-btn ui-btn-success btn-fact ui-btn-xs">Да</button><button class="ui-btn ui-btn-cancel ui-btn-xs">Нет</button></div>')
        $('.finance_popup_confirm').addClass("finance_popup_show")
        $('.btn-fact').on('click', function(){
            $.get('?f=edit&e='+_id+'&type_io='+_in,function(){})
            _this.parent().parent().remove()
            _this.parent().parent().removeClass("color_red")
            let _nt = _this.parent().parent().clone().appendTo(_in_s+" .npl table")
            _nt.find(".fxn").removeClass("fxn")
            _nt.find(".sendToFact").remove()
            $('.finance_popup_confirm').removeClass("finance_popup_show")
        })
        $('.ui-btn-cancel').on('click', function(){$('.finance_popup_confirm').removeClass("finance_popup_show")})
    });
    $('.sendToActive').click(function(e){
        e.preventDefault()
        let _this = $(this)
        let _id = _this.data("id")
        let _box = $('.finance_popup_box_confirm')
        _box.css('display','block')
        _box.html('<div class="popup_confirm"><b>Удалить?</b></div><div><button class="ui-btn ui-btn-success btn-active ui-btn-xs">Да</button><button class="ui-btn ui-btn-cancel ui-btn-xs">Нет</button></div>')
        $('.finance_popup_confirm').addClass("finance_popup_show")
        $('.btn-active').on('click', function(){
            $.get('?f=del&e='+_id,function(){})
            _this.parent().parent().remove()
            $('.finance_popup_confirm').removeClass("finance_popup_show")
        })
        $('.ui-btn-cancel').on('click',function(){$('.finance_popup_confirm').removeClass("finance_popup_show")})
    })

    $('input[name="plan_data"],input[name="fact_data"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            "format": "DD.MM.YYYY",
            "applyLabel": "Выбрать",
            "cancelLabel": "Очистить",
            "fromLabel": "От",
            "toLabel": "До",
            "customRangeLabel": "Произвольный",
            "daysOfWeek": [
                "Вс",
                "Пн",
                "Вт",
                "Ср",
                "Чт",
                "Пт",
                "Сб"
            ],
            "monthNames": [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль", 
                "Август",
                "Сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
            firstDay: 1
        }
    });
    $('input[name="plan_data"],input[name="fact_data"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' — ' + picker.endDate.format('DD.MM.YYYY'));
    });
    $('input[name="plan_data"],input[name="fact_data"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
	
	$('.pager').click(function(e){
		e.preventDefault();
		let _this = $(this)
		let _pnum = $(this).data('page');
		let _type_io = $(this).parent().data('type_io');
		let _type_pf = $(this).parent().data('type_pf');

		_url = _this.attr('href')
		
		$.ajax({
			url: _url,
			method: 'get',
			dataType: 'html',
			data: {},
			beforeSend: function(){
				_this.parents().eq(1).css('opacity','.3')
			},
			success: function(data){
				$('.table_pager').css('opacity','1')
				_this.parents().eq(1).html(data)
			}
		})
	})
	
})

/* show/hide depts filter */
function letsGo(id){
    $('.idhide').hide()
    $('.id'+id).show()
}

function show_hide(cl){
	let _el = $(cl)
	let _pa = $('.hidden_box')
	let _vis = $(cl).is(':hidden')
	if(_vis){
		_pa.hide()
		_el.toggle('show')
	}else{
		_el.toggle('hide')
	}
}