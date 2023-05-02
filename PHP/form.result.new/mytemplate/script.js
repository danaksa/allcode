function ajaxForm(obForm, link) {

    BX.bind(obForm, 'submit', BX.proxy(function(e) {
        BX.PreventDefault(e);
        //obForm.getElementsByClassName('error-msg')[0].innerHTML = '';

        let xhr = new XMLHttpRequest();
        xhr.open('POST', link);

        xhr.onload = function() {
            if (xhr.status != 200) {
                //alert(`Ошибка ${xhr.status}: ${xhr.statusText}`);
                //openpopupfalse()
                Fancybox.show([{ src: "#modal-fail", type: "inline" }]);

            } else {
                var json = JSON.parse(xhr.responseText)

                if(json.success){
                    Fancybox.show([{ src: "#modal-success", type: "inline" }]);
                }
                else if(json.formerror){
                    console.log(json.errorsform)
                    let errorStr = '';
                    for (let fieldKey in json.errorsform) {
                        errorStr += json.errorsform[fieldKey] + '<br>';
                    }

                    // Ошибки вывести в элемент с классом error-msg
                    //obForm.getElementsByClassName('error-msg')[0].innerHTML = errorStr;
                }
                 else if(json.error){
                    Fancybox.show([{ src: "#modal-fail", type: "inline" }]);
                }
            }
        };

        xhr.onerror = function() {
            Fancybox.show([{ src: "#modal-fail", type: "inline" }]);//запрос не удался
        };

        // Передаем все данные из формы
        xhr.send(new FormData(obForm));
    }, obForm, link));
}