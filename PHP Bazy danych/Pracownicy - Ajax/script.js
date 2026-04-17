$(document).ready(function(){

    getPracownicy();
    getPracownicyFiltr();
})

function getPracownicy(){

    $.ajax({
        url: "get/getPracownicy.php",
        method: 'POST'
    }).done(function( data )
    {
        $('#pracownicy').html(data);
    })
}

function getPracownicyFiltr(){
    $('#form').on('submit',function(e){
        $.ajax({
            url: "get/getPracownicyFiltr.php",
            method: 'POST',
            data: {
                search: $('#search').val(),
            }
        }).done(function( data ){
            $('#pracownicy').html(data);
        })
    })
}