let currentPage=1;
let data = {
    action:'insert',
    source:'register.html',
    staff_type:'4'
};

$(document).ready(function(){

    $(".slidebox").hide();
    $("#part-1").show().addClass('show');

    $(".next-button").click(function(){
        changePage(currentPage+1, $(this));
    });

    $(".prev-button").click(function(){
        console.log($(this));
        changePage(currentPage-1, $(this));
    });

    $.post('php/functions/get_staff_info.php',{action:'all_position_info'},function(result){
        result = JSON.parse(result);
        result = result.map((val, index)=>{
            return {id:val.id,name:val.name};
        });
        result.forEach(element => {
            $("#position").append(
                `<option value="${element.id}">${element.name}</option>
                `
            )
        });
    })

    $(".end").click(function(){

        let pwd1 = $("#pwd1").val();
        let pwd2 = $("#new_pwd").val();
        if(pwd1 !== pwd2){
            alert("密碼不符！");
            $("#pwd-1").val('');
            $("#new_pwd").val('');
            changePage(5,$(this), true);
        }


        $('.field').each(function(){
            let field = $(this).attr('id');
            let val = $(this).val();
            data[field] = val
        })
        delete(data['pwd1']);

        console.log(data);

        $.post('php/functions/staff.php', data, function(result){
            result = JSON.parse(result);
            if(result.msg=='SUCCESS'){
                changePage(7, null, true);
            }else{
                alert("資料輸入有誤！請重新輸入");
                location.reload();
            }
        })
    })


})

function changePage(p, t, force=false){
    //remove all error
    $(".field").removeClass('error');
    
    console.log(t);
    if(!force && t.hasClass('end')){
        return;
    }
    
    //get data
    let field = $(`#part-${currentPage} .field`);
    if(!field.hasClass('can-ignore')){
        let val = field.val();
        if(val==''){
            //no input
            field.addClass('error');

            if(!t.hasClass('end')){
                return;
            }
        }
    }


    $(`#part-${currentPage}`).hide();
    $(`#step-${currentPage}`).removeClass('on-step');

    $(`#part-${p}`).show();
    $(`#step-${p}`).addClass('on-step');

    currentPage = p;

}
