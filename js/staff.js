let staff_types={};
let positions ={};
let s_searchValue;

$(document).ready(function(){
    // get staff type data
    fetchStaffInfo();
    // setInterval(function(){
    //     fetchStaffInfo();
    // }, 1000*60*5)

    $("#staff_form").hide();

    $("input").removeClass('error');

    //search runner then show detail
    $("#staff_searchbox").keyup(function () {

        //clear everything in the table first
        s_searchValue = this.value;
        fetchStaff(s_searchValue);
    });

     /*************************
     * 
     * Click row to edit
     * 
     ************************/
    $("#staff_table>tbody").on('click', 'tr', function(){

        buttonShow('s',0,1,1);
        $("input").removeClass('error');

        let fields=['id'];
        //add other fields in
        $("#staff_table>thead th").each(function () {
            fields.push($(this).attr("field"));
        })

        console.log(fields);

        //put into form, first get info
        let datas=[];
        datas.push($(this).attr('id'));
        $(this).children('td').each(function(){
            datas.push($(this).attr('value'));
        })
        console.log(datas);

        //clear password field
        $("#s_new_pwd").val("");


        //set information to form        
        fields.forEach(function(field, i){
            let inputID = `#s_${field}`;
            $(inputID).val(datas[i]);
        })

        $("#staff_form").fadeIn();
        

    })

    /*************************
     * 
     * INSERT NEW
     * 
     ************************/
    $("#s_insert").click(function () {
        buttonShow('s',1,0,0);
        $("input").removeClass('error');
        $("#staff_form input:not([type=submit])").val("");
        $("#staff_form input[name=action]").attr('value','insert');
        $("#staff_form").fadeIn();
    })

     /*************************
     * 
     * Update Staff Data
     * 
     ************************/
    $("#s_buttons>button").click(function(event){
        event.preventDefault();

        /**
         * Set the hidden field "action" to the action value
         * registered in the button element
         * So that backend reads the action param
         * and decide what to do.
         */
        $("#s_action").attr('value', $(this).attr('value'))
        console.log($("#staff_form").serialize());

        if(confirm("確認？")){
            $.post("php/functions/staff.php", $("#staff_form").serialize(), function(result){
                result = JSON.parse(result);
                console.log(result);
                let msg = result["msg"];
                switch(msg){
                    case 'SUCCESS':
                        alert('成功！');
                        $("input").removeClass('error');
                        fetchStaff(s_searchValue);
                        break;
                    case 'VAL_ERR':
                        let errorFields = result['field'];
                        for(let field in errorFields){
                            $(`#s_${field}`).addClass('error');
                        }
                        break;
                    case 'RMV_ERR':
                        alert('刪除失敗！')
                        break;
                }
            });
        }

    });





})

function fetchStaff(txt){
    $(".result_table tbody").empty();
    $("form").fadeOut();
    $.post("php/functions/search.php",{
        'action':'staff',
        'text': txt
    },function(result){
        if(result!=""){
            result = JSON.parse(result)

            result.forEach(staff => {
                let id = staff["id"];
                let uid = staff["uid"];
                let staff_type = staff_types[staff["staff_type"]];
                let position = positions[staff["position"]];
                let name = staff["name"];
                let tel = staff["tel"];
                // console.log(staff_type);
                
                let appendHTML =`
                <tr id="${id}">
                <td value="${name}">${name}</td> 
                <td value="${uid}">${uid}</td> 
                <td value="${staff["position"]}">${position}</td> 
                <td value="${staff["staff_type"]}">${staff_type}</td> 
                <td value="${tel}">${tel}</td> 
                </tr>
                `
                $("#staff_table tbody").append(appendHTML);
            });
            
        }
    })
    
}


function fetchStaffInfo(){
    //get staff type & staff group
    $.post("php/functions/get_staff_info.php",
    {'action':'panel'},
    function(result){
        let jsonResult = JSON.parse(result);
        /**
         * result looks like {
         *  staff_type:{
         *      id=>name
         *  },
         *  position:
         *      id=>name
         * }
         * */
        staff_types=jsonResult[0];
        positions = jsonResult[1]  ;
    }).done(function(){
        let staffGroupSelectHTML = "";
        let staffTypeSelectHTML = "";


        for(let id in staff_types){
            staffTypeSelectHTML += 
            `<option value="${id}">${staff_types[id]}</option>`
        }
        for(let id in positions){
            staffGroupSelectHTML += 
            `<option value="${id}">${positions[id]}</option>`
        }

        $("#s_position").append(staffGroupSelectHTML);
        $("#s_staff_type").append(staffTypeSelectHTML);

    })
}

