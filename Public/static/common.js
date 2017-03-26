systemConfirm = function(msg,func,params){
    if ($("#myConfirm").length > 0) {
        $("#myConfirm").remove();
    }
    var html = "<div class='modal fade' id='myConfirm' >"
        + "<div class='modal-backdrop in' style='opacity:0; '></div>"
        + "<div class='modal-dialog' style='z-index:2901; margin-top:60px; width:400px; '>"
        + "<div class='modal-content'>"
        + "<div class='modal-header'  style='font-size:16px; '>"
        + "<span class='glyphicon glyphicon-question-sign'>&nbsp;</span><button type='button' class='close' data-dismiss='modal'>"
        + "<span style='font-size:20px;  ' class='glyphicon glyphicon-remove'></span><tton></div>"
        + "<div class='modal-body text-center' id='myConfirmContent' style='font-size:18px; '>"
        + msg
        + "</div>"
        + "<div class='modal-footer ' style=''>"
        + "<button class='btn btn-danger ' id='confirmOk' >确定<tton>"
        + "<button class='btn btn-info ' data-dismiss='modal'>取消<tton>"
        + "</div>" + "</div></div></div>";
    $("body").append(html);

    $("#myConfirm").modal("show");

    $("#confirmOk").on("click", function() {
        $("#myConfirm").modal("hide");
        func(params); // 执行函数
    });
};

systemAlert = function(msg){
    if ($("#myAlert").length > 0) {
        $("#myAlert").remove();
    }

    var html = "<div class='modal fade' id='myAlert' >"
        + "<div class='modal-backdrop in' style='opacity:0; '></div>"
        + "<div class='modal-dialog' style='z-index:2901; margin-top:60px; width:400px; '>"
        + "<div class='modal-content'>"
        + "<div class='modal-header'  style='font-size:16px; '>"
        + "<span class='glyphicon glyphicon-info-sign  '>&nbsp;</span><button type='button' class='close' data-dismiss='modal'>"
        + "<span style='font-size:20px;  ' class='glyphicon glyphicon-remove'></span><tton></div>"
        + "<div class='modal-body text-center' id='myConfirmContent' style='font-size:18px; '>"
        + msg
        + "</div>"
        + "<div class='modal-footer ' style=''>"
        + "<button class='btn btn-info ' data-dismiss='modal'>确认<tton>"
        + "</div>" + "</div></div></div>";
    $("body").append(html);
    $("#myAlert").modal("show");
};


systemModalShow = function(msg,id){
    if(id == null){
        id = 'systemModal';
    }

    if ($("#"+id).length > 0) {
        $("#"+id).remove();
    }

    var html = "<div class='modal fade' id='"+id+"' z-index=20000>"
        + "</div>";
    $("body").append(html);
    $("#"+id).modal("show");
};

systemModalHide = function(id){
    if(id == null){
        id = 'systemModal';
    }

    if ($("#"+id).length == 0) {
        return;
    }

    $("#"+id).remove();
};


function getFormJson(frm) {
    var o = {};
    var a = $(frm).serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [ o[this.name] ];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
}

autoHideMsg = function(msg,m_seconds){
    if ($("#auto_hide_msg").length > 0) {
        $("#auto_hide_msg").remove();
    }

    var html = "<div class='modal fade' id='auto_hide_msg' >"
        + "<div class='modal-backdrop in' style='opacity:0; '></div>"
        + "<div class='modal-dialog' style='z-index:2901; margin-top:60px; width:400px; '>"
        + "<div class='modal-content'>"
        + "<div class='modal-header'  style='font-size:16px; '>"
        + "<span class='glyphicon glyphicon-info-sign '>&nbsp;</span>"
        + "</div>"
        + "<div class='modal-body text-center' id='myConfirmContent' style='font-size:18px; '>"
        + msg
        + "</div>"
        +  "</div></div></div>";
    $("body").append(html);
    if(m_seconds == null){
        m_seconds = 2000;
    }
    $('#auto_hide_msg').modal("show");

    setTimeout(function(){
        $('#auto_hide_msg').modal("hide");
    },m_seconds);

};

sysFormSubmit = function (form_target,ajax_data){
    var form_dom = $(form_target);
    var submit_data = getFormJson(form_target);
    var url = form_dom.attr('action');

    var disable_btn_dom = form_dom.find('[data-disable-while-submit=1]');
    var original_post_data = {
        'url':url,
        'type':'post',
        'data':submit_data,
        'beforeSend':function(){
            if(disable_btn_dom.length > 0){
                disable_btn_dom.prop('disabled', true);
            }
            //systemModalShow('','ajax_submit');
        },
        'success':function(data){

        },
        'error':function(){
            autoHideMsg('网络异常');
        },
        'complete':function(){
            if(disable_btn_dom.length > 0){
                disable_btn_dom.prop('disabled', false);
            }
            //systemModalHide('ajax_submit');
        }
    };

    if(ajax_data != null){
        original_post_data = $.extend(original_post_data, ajax_data);
    }

    $.ajax(original_post_data);
};