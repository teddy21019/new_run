$(document).ready(function(){
    //set tab active for the first
    $(".sidebar li:first").addClass("active");
    $(".main>div").hide();
    $(".main div:first").show();
    
    //if clicked
    $(".sidebar li").click(function(){
        let toShow = $(this).attr('id').split("_")[1];
        if(!$(this).hasClass("active")){
            $(".sidebar li").removeClass("active");
            $(this).addClass("active");
            
            //hide all in class
            $(".main>div").hide();
            $("#"+toShow).fadeIn('slow');
        }
    })
});