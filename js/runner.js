let run_groups = [];
let run_types = {};
let searchValue;
$(document).ready(function () {

    //get rungroup data

    //refresh every 5 min
    fetchRungroup();
    setInterval(function () {
        fetchRungroup();
    }, 1000 * 60 * 5)

    $("input").removeClass('error');



    //search runner then show detail
    $("#runner_searchbox").keyup(function () {

        //clear everything in the table first
        searchValue = this.value;

        fetchRunner(searchValue);



    });


    $("#runner_form").hide();

    /*************************
     * 
     * Run Group Suggestion
     * 
     ************************/

    $("#run_group_sug").hide();

    $("#r_run_group").keyup(function(){
        searchValue = this.value;
        suggestRungroup(searchValue);
    });

    $("#r_run_group").focusout(function(){
        $("#run_group_sug").fadeOut();
    })



    $("#run_group_sug").on('click', 'div', function () {
        $("#r_run_group").val($(this).text());
        $("#run_group_sug").hide();
    })



    /*************************
     * 
     * Click row to edit
     * 
     ************************/
    //這邊寫得有夠爛，看到的人幫我改一下ㄎㄎ
    //主要是要把表格內的文字轉移到右邊的form進行修改
    //未來嘗試用vue.js
    $("#runner_table>tbody").on('click', 'tr', function () {

        buttonShow('r',0,1,1);
        $("input").removeClass('error');



        let fields = ['id'];

        $("#runner_table>thead th").each(function () {
            fields.push($(this).attr("field"));
        })

        // put into form
        let datas = [];
        datas.push($(this).attr('id'));

        $(this).children('td').each(function () {
            datas.push($(this).attr('value'));
        });

        datas.pop();//doesn't need run_time

        //此時data長：
        //[id,number,name,run_group, tel, gender, run_time]

        //set information to form
        fields.forEach(function (field, i) {
            let inputID = "#r_" + field;
            $(inputID).val(datas[i]);
        });
        //handle gender
        let gender = (datas.pop() == "女") ? 0 : 1;
        $("#r_gender").val(gender);


        //show form
        $("#runner_form").fadeIn();

        // post
    });


    //update runner data
    $("#r_buttons>button").click(function (event) {
        event.preventDefault();

        // $("input").removeClass('error');
        $("#r_action").attr('value', $(this).attr('value'));
        
        //確認動作
        // console.log($("#runner_form").serialize());


        if (confirm("確認？")) {
            $.post("php/functions/runner.php", $("#runner_form").serialize(), function (result) {
                result = JSON.parse(result);
                console.log(result);
                let msg = result['msg'];
                switch (msg) {
                    case 'SUCCESS':
                        alert('成功！');
                        $("input").removeClass('error');
                        fetchRungroup();    //更新跑團名稱
                        fetchRunner(searchValue);
                        break;
                    case 'VAL_ERR':
                        let errorFields = result['field'];
                        for (let field in errorFields) {
                            $(`#r_${field}`).addClass('error');
                        }
                        break;
                    case 'RMV_ERR':
                        alert("刪除失敗！");
                        break;

                }
                console.log(result);
            });
        }
    })


    $("#r_insert").click(function () {
        buttonShow('r',1,0,0);
        $("input").removeClass('error');
        $("#runner_form input:not([type=submit])").val("");
        $("#runner_form input[name=action]").attr('value','insert');
        $("#runner_form").fadeIn();
    })


});



function fetchRungroup() {
    $.post("php/functions/get_rungroup.php", { 'action': 1 }, function (result) {
        run_groups = JSON.parse(result);
        // console.log(run_groups);
    });
}

function fetchRunner(txt) {
    $(".result_table tbody").empty();
    $("form").fadeOut();
    $.post("php/functions/search.php",
        {
            'action': 'runner',
            'text': txt
        },
        function (result) {
            if (result != "") {

                result = JSON.parse(result);

                result.forEach(runner => {
                    let id = runner['id'];
                    let number = runner['number'];
                    let run_type = run_types[runner['run_type']];
                    let name = runner['name'];
                    let run_group = run_groups[runner['run_group']];
                    let tel = runner['tel'];
                    let run_time = (runner['run_time'] === null) ? '' : runner['run_time'];
                    let gender = (runner['gender'] == 0) ? "女" : "男";


                    //最好根據th排序來變，但我來不及寫
                    let appendHTML = `
                <tr id="${id}">
                    <td value="${runner['number']}">${number}</td>
                    <td value="${runner['run_type']}">${run_type}</td>
                    <td value="${runner['name']}">${name}</td>
                    <td value="${run_group}">${run_group}</td>
                    <td value="${runner['tel']}">${tel}</td>
                    <td value="${runner['gender']}">${gender}</td>
                    <td value="${runner['run_time']}">${run_time}</td>
                </tr>
                `;

                    $("#runner_table tbody").append(appendHTML);
                });

            }

        })
}
function suggestRungroup(txt) {
    $("#run_group_sug").empty();
    $.post("php/functions/search.php",
        { 
            'action': 'run_group', 
            'text': txt 
        }, 
        function (result) {
            if(result!=""){
                result = JSON.parse(result);
                result.forEach(group=>{
                    let groupName = group['name'];
                    $("#run_group_sug").append(
                        `<div>${groupName}</div>`
                    )
                });
                $("#run_group_sug").fadeIn();
            }
        });

}

function buttonShow(f,a,b,c){
    let l = [
        ['add',a],
        ['update',b],
        ['delete',c]
    ]
    l.forEach(ele => {
        let id = `#${f}_${ele[0]}`  // #r_add, #s_delete
        ele[1]==1?($(id).show()):($(id).hide())

    });

    // a==1?($().show()):($().hide())

    // if(a){
    //     $(`#r_add`).show();
    // }else{
    //     $("#r_add").hide();

    // }

    // if(b){
    //     $("#r_update").show();
    // }else{
    //     $("#r_update").hide();
    // }

    // if(c){
    //     $("#r_delete").show();
    // }else{
    //     $("#r_delete").hide();

    // }
}
