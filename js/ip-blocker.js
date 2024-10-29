function openTab(evt, idName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(idName).style.display = "block";
    evt.currentTarget.className += " active";
}
/* Get the element with id="defaultOpen" and click on it */
document.getElementById("defaultOpen").click();
jQuery(document).ready(function() {
    /*--Start bootstrap datatable initialization--*/
    jQuery('#banned-ip-list').DataTable({
        responsive: true
    });
    /*--EOF bootstrap datatable initialization--*/
    setTimeout(() => {
        jQuery('.notif-msg').fadeOut();
    }, 5000);
});
var checkboxes = document.getElementsByTagName('input');
for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i].type == 'checkbox') {
        checkboxes[i].checked = false;
    }
}

function bannedCheckbox(ele) {
    if (ele.checked) {
        let UserIp = ele.getAttribute('data-ip');
        var askToDelete = confirm("Banned User with IP Address '" + UserIp + "' will be deleted.");
        if (askToDelete === true) {
            console.log('Inside True', ele.value);
            document.getElementById("form1").submit();
        } else {
            ele.checked = false;
        }
    }
}