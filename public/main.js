$(document).ready(function() {
    $('.nav-btne').click(function() {
        $(".for-side").toggle("slide");
    });
    $(".nav-list li,#display_content").click(function() {

        if ($(window).width() < 768) {
            $(".for-side").hide("slide", 600);
        }
    }).not('.forseach');
    $(".searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".nav-list li a").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

})

function showLoader() {
    $("#loaderHtml").show()
}

function hideLoader() {
    $("#loaderHtml").hide()
}

function switchPage(type = 1) {
    if (type == 1) {
        $("#appIn").css('visibility', 'visible');
        $("#loader").css('display', 'none');
    } else {
        $("#loader").css('display', 'flex');
        $("#appIn").css('visibility', 'hidden');
    }
}

function showAlert(msg, icon = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)

            //toast.addexpenseListener('mouseenter', Swal.stopTimer)
            //toast.addexpenseListener('mouseleave', Swal.resumeTimer)
        }
    })

    Toast.fire({
        icon: icon,
        title: msg
    })
}
var loaderHtml = ''

async function postData(route, data, type = false) {
    $("#loaderHtml").show()
    return await axios.post(route, data).then(function(response) {
        //switchPage(1)
        $("#loaderHtml").hide()
        if (!type) {
            showAlert(response.data);
        } else {
            return response.data;
        }
    }).catch(function(error) {
        //switchPage(1)                   
        $("#loaderHtml").hide()
        Swal.fire({
            text: error.response.data,
            icon: 'error',
        })
    })
}

function formatToCurrency(amount) {
    return (amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function PrintElem(id, title) {

    var mywindow = window.open('', 'PRINT');

    mywindow.document.write('<html><head><title>' + title + '</title>');
    mywindow.document.write(`<link rel="stylesheet" href="{{asset('/lib/css/styles.css')}}" >`);
    mywindow.document.write(`<link rel="stylesheet" href="{{asset('/lib/css/boostrap4.css')}}" >`);
    mywindow.document.write('</head><body >');
    mywindow.document.write('<h1>' + title + '</h1>');
    let html = $('#' + id).html();
    html = html.replace('height', '');
    html = html.replace('overflow', '');
    mywindow.document.write(html);
    mywindow.document.write('</body></html>');
    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    setTimeout(() => {
        mywindow.print();
        mywindow.close();
    }, 2000);
    return true;
}
