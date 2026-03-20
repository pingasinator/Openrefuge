let espece_element = null;
let race_element = null;


//initialisation du document
$(function(){
    espece_element = document.getElementById("animal_espece");
    race_element = document.getElementById("animal_race");
    
    if(race_element != null && espece_element != null)
    {
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
    if(race_element != null && espece_element != null)
    {
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
                obj.map((e) => {race_element.innerHTML += `<option value=${e.animal_race}>${e.nom}</option>`});
                console.log(data);
            }
        });
    }
}
