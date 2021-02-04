$(document).ready(function(){
    mapDefaultMap();
});

function mapDefaultMap() {
    var middle = SMap.Coords.fromWGS84(14.41, 50.08);
    var map = new SMap(JAK.gel("map"), middle, 10);
    map.addDefaultLayer(SMap.DEF_BASE).enable();
    map.addDefaultControls();

    map.addControl(new SMap.Control.Sync());
    map.addDefaultLayer(SMap.DEF_BASE).enable();
    var mouse = new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM);
    map.addControl(mouse);

    var items = null;
    var link = $('#get-data-link').data('link');

    naja.makeRequest('get', link);
    naja.addEventListener('complete', ({detail}) => {
        addPoints(map, detail.payload);
    });
}

function addPoints(map, data) {
    //var data = getData();
    var tags = [];
    var coordinates = [];

    for (var address in data.addresses) {
        var coordinate = SMap.Coords.fromWGS84(data.addresses[address].gps);

        var options = {
            //url:obrazek,
            title: address,
            anchor: {left:10 , bottom: 1}
        }

        var tag = new SMap.Marker(coordinate, null, options);

        var card = new SMap.Card();
        card.setSize(400, 400);

        var addressPersons = "";

        for (var person in data.addresses[address].persons) {
            var personEntity = data.addresses[address].persons[person];

            addressPersons += personEntity + "<br>";
        }

        var addressBirthPersons = "";

        for (var person in data.addresses[address].birthPersons) {
            var personEntity = data.addresses[address].birthPersons[person];

            addressBirthPersons += personEntity + "<br>";
        }

        var addressDeadPersons = "";

        for (var person in data.addresses[address].deadPersons) {
            var personEntity = data.addresses[address].deadPersons[person];

            addressDeadPersons += personEntity + "<br>";
        }

        var addressGravedPersons = "";

        for (var person in data.addresses[address].gravedPersons) {
            var personEntity = data.addresses[address].gravedPersons[person];

            addressGravedPersons += personEntity + "<br>";
        }

        var addressJobs = "";

        for (var job in data.addresses[address].jobs) {
            var jobEntity = data.addresses[address].jobs[job];

            addressJobs += jobEntity + "<br>";
        }

        var addressWeddings = "";

        for (var wedding in data.addresses[address].weddings) {
            var weddingsEntity = data.addresses[address].weddings[wedding];

            addressWeddings += weddingsEntity + "<br>";
        }

        var addressTitle = $('.title_translate').data('address_address');
        var addressPersonsTitle = $('.title_translate').data('address_persons');
        var addressBirthPersonsTitle = $('.title_translate').data('address_birth_persons');
        var addressDeathPersonsTitle = $('.title_translate').data('address_death_persons');
        var addressGravedPersonsTitle = $('.title_translate').data('address_graved_persons');
        var addressJobsTitle = $('.title_translate').data('address_jobs');
        var addressWeddingsTitle = $('.title_translate').data('address_weddings');

        var cardText = "";

        if (addressPersons !== "") {
            var cardText = "<strong>" + addressPersonsTitle + "</strong><br>" + addressPersons;
        }

        if (addressBirthPersons !== "") {
            cardText += "<strong>" + addressBirthPersonsTitle + "</strong><br>" + addressBirthPersons;
        }

        if (addressDeadPersons !== "") {
            cardText += "<strong>" + addressDeathPersonsTitle + "</strong><br>" + addressDeadPersons;
        }

        if (addressGravedPersons !== "") {
            cardText += "<strong>" + addressGravedPersonsTitle + "</strong><br>" + addressGravedPersons;
        }

        if (addressJobs !== "") {
            cardText += "<strong>" + addressJobsTitle + "</strong><br>" + addressJobs;
        }

        if (addressWeddings !== "") {
            cardText += "<strong>" + addressWeddingsTitle + "</strong><br>" + addressWeddings;
        }

        card.getHeader().innerHTML = "<strong>" + addressTitle + " " + data.addresses[address].address + "</strong>";
        card.getBody().innerHTML = cardText;

        tag.decorate(SMap.Marker.Feature.Card, card);

        coordinates.push(coordinate);
        tags.push(tag);
    }

    for (var town in data.towns) {
        var coordinate = SMap.Coords.fromWGS84(data.towns[town].gps);

        var options = {
            //url:image,
            title: town,
            anchor: {left:10 , bottom: 1}
        }

        var tag = new SMap.Marker(coordinate, null, options);

        var card = new SMap.Card();
        card.setSize(400, 400);

        var townBirthPersons = "";

        for (var person in data.towns[town].birthPersons) {
            var personEntity = data.towns[town].birthPersons[person];

            townBirthPersons += personEntity + "<br>";
        }

        var townDeadPersons = "";

        for (var person in data.towns[town].deadPersons) {
            var personEntity = data.towns[town].deadPersons[person];

            townDeadPersons += personEntity + "<br>";
        }

        var townGravedPersons = "";

        for (var person in data.towns[town].gravedPersons) {
            var personEntity = data.towns[town].gravedPersons[person];

            townGravedPersons += personEntity + "<br>";
        }

        var townJobs = "";

        for (var job in data.towns[town].jobs) {
            var jobEntity = data.towns[town].jobs[job];

            townJobs += jobEntity + "<br>";
        }

        var townWeddings = "";

        for (var wedding in data.towns[town].weddings) {
            var weddingsEntity = data.towns[town].weddings[wedding];

            townWeddings += weddingsEntity + "<br>";
        }

        var townTitle = $('.title_translate').data('town_town');
        var townBirthPersonsTitle = $('.title_translate').data('town_birth_persons');
        var townDeathPersonsTitle = $('.title_translate').data('town_death_persons');
        var townGravedPersonsTitle = $('.title_translate').data('town_graved_persons');
        var townJobsTitle = $('.title_translate').data('town_jobs');
        var townWeddingsTitle = $('.title_translate').data('town_weddings');

        var cardText = "";

        if (townBirthPersons !== "") {
            cardText += "<strong>" + townBirthPersonsTitle + "</strong><br>" + townBirthPersons;
        }

        if (townDeadPersons !== "") {
            cardText += "<strong>" + townDeathPersonsTitle + "</strong><br>" + townDeadPersons;
        }

        if (townGravedPersons !== "") {
            cardText += "<strong>" + townGravedPersonsTitle + "</strong><br>" + townGravedPersons;
        }

        if (townJobs !== "") {
            cardText += "<strong>" + townJobsTitle + "</strong><br>" + townJobs;
        }

        if (townWeddings !== "") {
            cardText += "<strong>" + townWeddingsTitle + "</strong><br>" + townWeddings;
        }

        card.getHeader().innerHTML = "<strong>" + townTitle + " " + data.towns[town].town + "</strong>";
        card.getBody().innerHTML = cardText;

        tag.decorate(SMap.Marker.Feature.Card, card);

        coordinates.push(coordinate);
        tags.push(tag);
    }

    var layer = new SMap.Layer.Marker();

    map.addLayer(layer);

    layer.enable();

    for (var i = 0; i < tags.length; i++) {
        layer.addMarker(tags[i]);
    }

    var cz = map.computeCenterZoom(coordinates);

    map.setCenterZoom(cz[0], cz[1]);
}