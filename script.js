
function aff(option) {
	if(document.getElementById(option).checked) {
	        var classe = document.getElementsByClassName(option), i;
        	for (i = 0; i < classe.length; i++) {
            		classe[i].style.display = 'none';
        	}
    }
	else if(document.getElementById(option).checked == false) {
	        var classe = document.getElementsByClassName(option), i;
        	for (i = 0; i < classe.length; i++) {
            		classe[i].style.display = '';
        	}
    }
}
