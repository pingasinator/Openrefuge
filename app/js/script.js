let espece_element = null;
let race_element = null;
let defaultValue = 0;


//initialisation du document
$(function(){
    espece_element = document.getElementById("animal_espece");
    race_element = document.querySelector("#animal_race");
    
    if(race_element != null && espece_element != null)
    {
        defaultValue = race_element.value;
        update_race();
        //met à jours le selecteur race à chaque changements de valeurs dans le selecteur espèce
        espece_element.onchange = (() => {update_race();});
    }else{
        console.log('E');
    }
});

//changement du selecteur race en fonction de l'espèce
function update_race()
{
    if(race_element != null && espece_element != null && race_element.tagName.toLowerCase() == "select")
    {

        console.log(defaultValue);
        race_element.innerHTML = "";
        $.ajax({
            type: "post",
            url: "animal_race.php",
            data: {espece:espece_element.value},
            success: function(data)
            {
                if(data==null){
                    race_element.innerHTML = "<option value=null>Choisir espece</option>";
                }
                let obj = JSON.parse(data);
                obj.map((e) => {race_element.innerHTML += (`<option value=${e.animal_race}` + (e.animal_race === defaultValue ? ` selected` : "") + `>${e.nom}</option>`)});
            }
        });
    }
}
