function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
function budbee_check_key(e) {
    if (e.Key === "Enter" || e.keyCode === 13) {
        budbee_check_alternatives();
    }
}
function budbee_check_alternatives() {
    let xhr = new XMLHttpRequest();
    let nonce = document.getElementById("budbee-postal-nonce").value
    let postal = document.getElementById("budbee-postal-value").value
    document.getElementById("budbee-fallback-text").style.display = "block";
    document.cookie = "budbee-postal=" + postal + ";path=/";
    xhr.open("GET", "/wp-json/budbee/v1/postalcode/" + postal + "?_wpnonce=" + nonce, true);
    xhr.onload = function () {
        if (this.status === 200) {
            
            document.getElementById("budbee-box-response").style.display = "block";

        }
        else {
            document.getElementById("budbee-box-response").style.display = "none";
        }
    }
    xhr.send()


    let xhr2 = new XMLHttpRequest();
    xhr2.open("GET", "/wp-json/budbee/v1/homedelivery/" + postal + "?_wpnonce=" + nonce, true);
    xhr2.onload = function () {
        if (this.status === 200) {
            
            document.getElementById("budbee-home-response").style.display = "block";
        }
        else {
            document.getElementById("budbee-home-response").style.display = "none";
        }
        console.log(document.getElementById("budbee-home-response").style.display);
    }
    xhr2.send()

}
if (getCookie("budbee-postal")) {
    document.getElementById("budbee-postal-value").value = getCookie("budbee-postal");
    budbee_check_alternatives();
}