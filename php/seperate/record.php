

<script>
    $("#but").click(function () {
        var id = $("#num").val();
        // alert(id);
        $.post("finish.php?id=" + id, function (data, status) {
            alert(data);
        }
        )
    })

    $('#num').keypress(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            $("#but").trigger("click");
        }
    });
</script>