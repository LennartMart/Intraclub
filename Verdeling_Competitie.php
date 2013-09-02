<div class="span8 offset2" id="spelerslijst">
<!--    <div id="label-switch" class="make-switch" data-on-label="SI" data-off-label="NO" data-on="success" data-off="danger">-->
        <?php
            require_once("Models/Ranking.php");

            $ranking = new Ranking();
            $spelerslijst = $ranking->getAlgemeneRanking();
            foreach($spelerslijst as $speler)
            {

                $naam = $speler["voornaam"]." ".$speler["naam"];
                $ID = $speler["speler_id"];
                echo "<label><input type='checkbox' name='$naam' value='$ID' /> $naam</label>";
            }
        ?>

<!--   </div>-->
</div>
<div id="matchen"></div>
<input id='knopBerekenMatchen' type='button' onclick='BerekenMatchen()' value='Bereken wedstrijden'>
<input style="display:none" onclick = 'toonSpelers()' value='Toon de spelers'>

<script language='javascript'>
    var matchenPerPoule = 3;

    var aanwezigeSpelers = new Array();		// array met de ID's en namen van de aanwezige spelers
    var wedstrijdArray = new Array();			// array met alle wedstrijden: wedstrijdArray[match nummer][teamX_spelerX] = array {ID, naam}
    var geselecteerdeSpelers = new Array();		// array met de spelers die geselecteerd zijn voor een 2de match te spelen.
    var spelersLijstVoorDropdown = new Array();              //Dropdown, indien er géén speler toegekend is op die positie


    function BerekenMatchen() {

        var result = confirm("Wil je de wedstrijden verdelen?");
        if (result==false) {
            return;
        }
        //verberg de spelerslijst
        //blijven bij basic JS
        document.getElementById('spelerslijst').style.display = 'none';
        document.getElementById("knopBerekenMatchen").value = "Herloot de matchen";

        // array aanwezigeSpelers invullen met het ID en de naam van de aanwezige spelers
        var j = 0;
        for(var i=0;i<document.getElementById("spelerslijst").getElementsByTagName("INPUT").length;i++) {
            if(document.getElementById("spelerslijst").getElementsByTagName("INPUT")[i].type == 'checkbox') {
                if(document.getElementById("spelerslijst").getElementsByTagName("INPUT")[i].checked) {
                    aanwezigeSpelers[j] = new Array();
                    aanwezigeSpelers[j]['ID'] = document.getElementById("spelerslijst").getElementsByTagName("INPUT")[i].value;
                    aanwezigeSpelers[j]['naam'] = document.getElementById("spelerslijst").getElementsByTagName("INPUT")[i].name;
                    j++;
                }
            }
        }
        // Alle aanwezige spelers zitten in aanwezigeSpelers

        //Eerst: dropdownlist om eventueel te kunnen aanvullen
        spelersLijstVoorDropdown = aanwezigeSpelers;

        var matchNummer = 0;	// volgnummer van de match (1ste = 0)

        // matchen verdelen
        for(var i=0;i<Math.floor(aanwezigeSpelers.length/(matchenPerPoule*4));i++) { // i = poule

            //Neem het aantal spelers voor in de poule
            var pouleSpelers =  aanwezigeSpelers.splice(0, matchenPerPoule * 4);
            //Handicap toevoegen
            pouleSpelers = BerekenHandicap(pouleSpelers);

            //Nu de poule compleet door elkaar halen.
            pouleSpelers = MaakSpelersArrayRandom(pouleSpelers);

            for(var j=0;j<matchenPerPoule;j++) { // j = match in een poule
                wedstrijdArray[matchNummer] = new Array();
                wedstrijdArray[matchNummer]['team1_speler1'] = pouleSpelers[4*j];
                wedstrijdArray[matchNummer]['team1_speler2'] = pouleSpelers[4*j + 1];
                wedstrijdArray[matchNummer]['team2_speler1'] = pouleSpelers[4*j + 2];
                wedstrijdArray[matchNummer]['team2_speler2'] = pouleSpelers[4*j + 3];
                wedstrijdArray[matchNummer]['handicap_team1'] = (pouleSpelers[4*j]["handicap"] + pouleSpelers[4*j +1 ]["handicap"]) - (pouleSpelers[4*j + 2]["handicap"] + pouleSpelers[4*j + 3]["handicap"]);

                matchNummer++;
            }
        }

        // Vanaf hier zijn alle "volledige" poules berekend.
        // nu zit er in aanwezigeSpelers diegene die nog NIET in een match zitten.

        // resterende poule verdelen
        // We hebben nu max poule -1 spelers over
        if(aanwezigeSpelers.length%(matchenPerPoule*4) != 0) {

            aanwezigeSpelers = BerekenHandicap(aanwezigeSpelers);
            pouleSpelers = MaakSpelersArrayRandom(aanwezigeSpelers);

            for(var i=0;i<Math.floor((aanwezigeSpelers.length%(4*matchenPerPoule))/4);i++) {
                // matchen van 4 spelers die nog niet gespeeld hebben.
                wedstrijdArray[matchNummer] = new Array();
                wedstrijdArray[matchNummer]['team1_speler1'] = pouleSpelers[4*i];
                wedstrijdArray[matchNummer]['team1_speler2'] = pouleSpelers[4*i + 1];
                wedstrijdArray[matchNummer]['team2_speler1'] = pouleSpelers[4*i + 2];
                wedstrijdArray[matchNummer]['team2_speler2'] = pouleSpelers[4*i + 3];
                wedstrijdArray[matchNummer]['handicap_team1'] = (pouleSpelers[4*i]["handicap"] + pouleSpelers[4*i +1 ]["handicap"]) - (pouleSpelers[4*i + 2]["handicap"] + pouleSpelers[4*i + 3]["handicap"]);

                matchNummer++;
            }



            if(pouleSpelers.length%4 != 0) {
                // Nu hebben we alle mogelijke matchen er nog uitgehaald
                if(pouleSpelers.length -  (pouleSpelers.length%4) >= 4 )
                {
                    pouleSpelers.splice(0,pouleSpelers.length -  (pouleSpelers.length%4));
                }
                // Nu hebben we minder dan vier spelers over
                // Laatste verdelen
                // We hebben minimaal één speler over!
                wedstrijdArray[matchNummer] = new Array();
                wedstrijdArray[matchNummer]['team1_speler1'] = pouleSpelers[0];

                if(2 <= pouleSpelers.length%4) {
                    wedstrijdArray[matchNummer]['team2_speler1'] = pouleSpelers[1];
                    if(3 <= pouleSpelers.length%4){
                        wedstrijdArray[matchNummer]['team1_speler2'] = pouleSpelers[2];
                    }
                }
                matchNummer++;
            }
        }

        // We hebben alle matchen, nu verwerken in output

        var output = "<table class='table table-striped table-bordered table-condensed'>";
        for(var i = 0; i < matchNummer; i++)
        {
            output += '<tr>';
            output += '<td>' + outputSpeler(wedstrijdArray[i]['team1_speler1']);
            if(wedstrijdArray[i]['team1_speler2'] != null)
            {
                output += '<br/>' + outputSpeler(wedstrijdArray[i]['team1_speler2']) + '</td>';
            }
            else
            {
                output += '<br/>' + dropdownSpelerslijst(spelersLijstVoorDropdown, 1_2);
                output += "<input type='button' value='OK' onClick=\"selectSpeler(document.getElementById('1_2'), document.getElementById('select" + nummer + "'), " + (4-nummer) + ")\">"
            }

            //Hiertussen de inputs voor sets!
            output += inputSets();

            if(wedstrijdArray[i]['team2_speler1'] != null)
            {
                output += '<td>' + outputSpeler(wedstrijdArray[i]['team2_speler1']);
            }
            else
            {
                output += '<br/>' + dropdownSpelerslijst(spelersLijstVoorDropdown, 2_1);
            }
            if(wedstrijdArray[i]['team2_speler2'] != null)
            {
                output += '<br/>' + outputSpeler(wedstrijdArray[i]['team2_speler2']) + '</td>';
            }
            else
            {
                output += '<br/>' + dropdownSpelerslijst(spelersLijstVoorDropdown, 2_2);
            }


            if(wedstrijdArray[i]['handicap_team1'] != null)
            {
                if(wedstrijdArray[i]['handicap_team1'] > 0)
                {
                    output += '<td> (0 - ' + wedstrijdArray[i]['handicap_team1'] * (-1) + ')</td>';

                }
                else
                {
                    output += '<td> ('+ wedstrijdArray[i]['handicap_team1']   + ' - 0)</td>';
                }
            }

            output += '</tr>';

        }

        document.getElementById("matchen").innerHTML = output;





    }


    function dropdownSpelerslijst(array, nummer)
    {
        var output = "<select value='lijst' id='"+ nummer + "'>";
        for(var i = 0; i < array.length; i++)
        {
            output += "<option id='" + array[i]["ID"] + "'>" + array[i]['naam'] + "</option>";
        }
        output += "</select>";

        return output;
    }
    function outputSpeler(array)
    {
        return array['naam'] + "<input type='hidden' name='' id='' value='" + array['ID'] + "'>";
    }

    function inputSets()
    {
        var string = '';
        string += "<td align='center'><input type='input' size='2'> - <input type='input' size='2' ></td>";
        string += "<td align='center'><input type='input' size='2'> - <input type='input' size='2' ></td>";
        string += "<td align='center'><input type='input' size='2'> - <input type='input' size='2' ></td>";
        return string;
    }
    function BerekenHandicap(array){
        handicap = 0;
        for(j=0;j<array.length;j = j +2)
        {
            array[j]["handicap"] = handicap;
            if(j+1 < array.length){
                array[j+1]["handicap"] = handicap;
            }
            handicap++;
        }
        return array;
    }

    function MaakSpelersArrayRandom(array) {
        //Fisher-Yates algorithm
        //Kindly lend from http://stackoverflow.com/questions/2450954/how-to-randomize-a-javascript-array

            var currentIndex = array.length
                , temporaryValue
                , randomIndex
                ;

            // While there remain elements to shuffle...
            while (0 !== currentIndex) {

                // Pick a remaining element...
                randomIndex = Math.floor(Math.random() * currentIndex);
                currentIndex -= 1;

                // And swap it with the current element.
                temporaryValue = array[currentIndex];
                array[currentIndex] = array[randomIndex];
                array[randomIndex] = temporaryValue;
            }

            return array;
    }
    </script>