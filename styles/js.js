
var placementUser = 0;
    
function getDataListSelectedOption(txt_input, data_list_options) // (id de input, id du datalist)
{
    var shownVal = document.getElementById(txt_input).value;
    var value2send = document.querySelector("#" + data_list_options + " option[value='" + shownVal + "']").dataset.value;
    
    return value2send;
}

function populateInputHidden(valeurId, txt_input, data_list_options) 
{
    var inputHidden = document.getElementById(valeurId);

    inputHidden.value = getDataListSelectedOption(txt_input, data_list_options);
}





