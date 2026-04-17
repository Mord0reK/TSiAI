$(document).ready(function(){

    getWorkers();
    getWorkersFilter();

});

function getWorkers(){
    $.ajax({
        url: "getWorkers.php",
        method: 'POST'
    }).done(function( data ) {
        $('#workersData').html(data);
    })
}

function getWorkersFilter(){
    $('#form').on('submit',function(e){
        e.preventDefault();
        $('#workersData').html('<tr>\n' +
            '                        <td colspan="9"><div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></td>\n' +
            '                    </tr>');
        $.ajax({
                url: "getWorkersFilter.php",
                method: 'POST',
                data: {
                    search: $('#search').val(),
                }
            }).done(function( data ) {
                $('#workersData').html(data);
            });
    });

}