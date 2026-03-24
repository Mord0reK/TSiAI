$(document).ready(function() {

    $(".obraz").on('click', function() {

        let src_obrazka = $(this).attr("src");

        $("#glowny img").fadeOut(500, function ()
            {
                $("#glowny img").attr("src", src_obrazka);
            }
        );

        $("#glowny img").fadeIn(500);
    })

});