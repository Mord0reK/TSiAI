function wyslij()
{
    var tresc_wiadomosci = document.getElementById("wiadomosc").value;

    var nowa = document.createElement("div");
    nowa.className = "jolanta";
    nowa.innerHTML = '<img src="Jolka.jpg" alt="Jolanta"><p>'  +tresc_wiadomosci+'</p>';

    document.querySelector("#chat").appendChild(nowa);
}


function generujWiadomosc()
{
    var odpowiedzi = [
        "Nie będzie tak!",
        "Dajcie mi spokój ja już chce wolne.",
        "Ile jeszcze do tych swiąt.",
        "Dziapko.",
        "OPA",
        "asdasd",
        "To napasd",
        "Dzięasd"
    ];

    let randomowa = odpowiedzi[Math.floor(Math.random() * 8)];

    var tresc_wiadomosci = randomowa;

    var nowa = document.createElement("div");
    nowa.className = "krzysiek";
    nowa.innerHTML = '<img src="Krzysiek.jpg" alt="Krzysiek"><p>'  +tresc_wiadomosci+'</p>';

    document.querySelector("#chat").appendChild(nowa);

    nowa.scrollIntoView()
}