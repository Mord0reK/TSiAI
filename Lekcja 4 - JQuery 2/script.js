$(document).ready(function() {

    const obrazy = [
        "../cdn/obrazy/volvo.jpg",
        "../cdn/obrazy/scania1.jpg",
        "../cdn/obrazy/man.jpg"
    ];

    let autoCzas = null;
    const dilej = 3000;

    // Funkcje auto zeby sie samo zmienialo
    function auto() {
        let indeks = (obrazy.indexOf($("#glowny img").attr("src")) + 1) % obrazy.length;
        let nastepny = obrazy[indeks];

        if ($("#typ").val() == "1") {
            $("#glowny img").animate({
                left: "-100%"
            }, 300, function() {
                $(this).attr("src", nastepny);
                $(this).css("left", "100%");
                $(this).animate({
                    left: "0"
                }, 300);
            });
        } else {
            $("#glowny img").fadeOut(500, function() {
                $("#glowny img").attr("src", nastepny);
            });
            $("#glowny img").fadeIn(500);
        }
    }

    function startAuto() {
        if (!autoCzas) {
            autoCzas = setInterval(auto, dilej);
        }
    }

    function stopAuto() {
        if (autoCzas) {
            clearInterval(autoCzas);
            autoCzas = null;
        }
    }

    startAuto();

    $("#glowny img").on('mouseover', function()
        {
            stopAuto();
        }
    );

    $("#glowny img").on('mouseout', function()
        {
            startAuto();
        }
    );

    // Same przyciski te na dole fikuśne

    $("#poprzedni").on('click', function() {
        let indeks = (obrazy.indexOf($("#glowny img").attr("src")) - 1 + obrazy.length) % obrazy.length;
        let poprzedni = obrazy[indeks];

        if ($("#typ").val() == "1") {
            $("#glowny img").animate({
                left: "100%"
            }, 300, function() {
                $(this).attr("src", poprzedni);
                $(this).css("left", "-100%");
                $(this).animate({
                    left: "0"
                }, 300);
            });
        } else {
            // Efekt zanikania
            $("#glowny img").fadeOut(500, function() {
                $("#glowny img").attr("src", poprzedni);
            });
            $("#glowny img").fadeIn(500);
        }
    });

    $("#nastepny").on('click', function() {
        let indeks = (obrazy.indexOf($("#glowny img").attr("src")) + 1) % obrazy.length;
        let nastepny = obrazy[indeks];

        if ($("#typ").val() == "1") {
            $("#glowny img").animate({
                left: "-100%"
            }, 300, function() {

                $(this).attr("src", nastepny);
                $(this).css("left", "100%");
                $(this).animate({
                    left: "0"
                }, 300);

            });
        } else {
            // Efekt zanikania
            $("#glowny img").fadeOut(500, function() {

                $("#glowny img").attr("src", nastepny);

            });

            $("#glowny img").fadeIn(500);
        }
    });
});