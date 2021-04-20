var placementCR = 0;

var numRapport = document.getElementById("NumRapport");
var practicien = document.getElementById("Practiciens");
var dateRapport = document.getElementById("dateRapport");
var motifVisite = document.getElementById("motifVisite");
var BilanTx = document.getElementById("BilanTx");

tryPopulateDoc(0);

function tryPopulateDoc(intPosi) {
    var err1 = false;
    if (diffValDoc() == true) {
        try {
            populateInputHidden('valIdPracti','Practiciens','Practi');
        } catch (error) {
            alert('Aucun Praticien n\'existe à ce nom!');
            err1 = true;
        }
        try {
            populateInputHidden('valMotifVisi','motifVisite','MotifVisi');
        } catch (error2) {
            alert('Motif Visite Inconu');
            err1 = true;
        }
        if(!err1) { document.getElementById("formCR").submit(); }
    }

    if(!err1) { 
        placementCR = placementCR + intPosi;

        if (jsCR[placementCR] == null) {
            placementCR = placementCR - intPosi;
        }

        try { 
            populateDoc(placementCR); 
            populateInputHidden('valIdPracti','Practiciens','Practi');
            populateInputHidden('valMotifVisi','motifVisite','MotifVisi');
        }
        catch (error) {}
    }
}

function diffValDoc() {
    try {
        var placeDiffTest = numRapport.value -1;
        // alert(placeDiffTest+", "+ numRapport.value);
        // alert(dateRapport.value+", "+ jsCR[placeDiffTest].dateRapport);
        // alert(BilanTx.innerHTML+", "+ jsCR[placeDiffTest].bilan);
        // alert(motifVisite.value+", "+ jsCR[placeDiffTest].libMotif);
        
        if ((practicien.value != jsCR[placeDiffTest].nom +" "+ jsCR[placeDiffTest].prenom) 
            || (dateRapport.value != jsCR[placeDiffTest].dateRapport) 
            || (motifVisite.value != jsCR[placeDiffTest].libMotif) 
            || (BilanTx.value != jsCR[placeDiffTest].bilan))
        {
            //alert("diff");
            return true;
        }
        else {
            return false;
        }
    } catch (error) {
        
    }
}

function populateDoc(placeCR) {
    numRapport.value 	= jsCR[placeCR].idCompRendu;
    practicien.value 	= jsCR[placeCR].nom +" "+ jsCR[placeCR].prenom;
    dateRapport.value 	= jsCR[placeCR].dateRapport;
    motifVisite.value 	= jsCR[placeCR].libMotif;
    BilanTx.innerHTML 	= jsCR[placeCR].bilan;
}

var butB = document.getElementById("FgoBack");

function generateNewDoc() {
    numRapport.value 	= jsCR.length + 1;
    practicien.value 	= "";
    dateRapport.value 	= "";
    motifVisite.value 	= "";
    BilanTx.innerHTML 	= "";

    document.getElementById("NPbut").disabled = true;
    document.getElementById("NPbut2").disabled = true;
    $(butB).animate({opacity: 1}, 400);
    document.getElementById("saveForm1").style.display = "none";
    document.getElementById("saveForm2").style.display = "block";
}

function deleteNewDoc() {
    tryPopulateDoc(0);

    document.getElementById("NPbut").disabled = false;
    document.getElementById("NPbut2").disabled = false;
    var butB = document.getElementById("FgoBack");
    $(butB).animate({opacity: 0}, 400);
    document.getElementById("saveForm1").style.display = "block";
    document.getElementById("saveForm2").style.display = "none";
}

function saveNewDoc() {
    var err1 = false;
    if (diffValDoc() == true) {
        try {
            populateInputHidden('valIdPracti','Practiciens','Practi');
        } catch (error) {
            alert('Aucun Praticien n\'existe à ce nom!');
            err1 = true;
        }
        try {
            populateInputHidden('valMotifVisi','motifVisite','MotifVisi');
        } catch (error2) {
            alert('Motif Visite Inconu');
            err1 = true;
        }
        if(!err1) { document.getElementById("formCR").submit(); }
    }
}