$(document).ready(function($) {

    $('.menu').click(function() {

    //$('.nav-menu').fadeIn(300);
	$('.nav-menu').css('visibility', 'visible');
	$('.nav-menu').css('opacity', '1');
    if(window.innerWidth < 800){
        document.body.style.overflow = 'hidden';
    }


    });

	$('.nav-menu .aux-panel-close').click(function() {

    //$('.nav-menu').fadeIn(300);
	$('.nav-menu').css('visibility', 'hidden');
	$('.nav-menu').css('opacity', '0');
    return false;
    });

    $('svg #text19393').click(function() {
        // Fancybox.show([{ src: "/include/ask_project.php", type: "inline" }]);
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-ozerny",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-ozerny').modal('show');
    });

    $('svg #text19495').click(function() {
        //Fancybox.show([{ src: "/include/ask_project.php", type: "inline" }]);
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-nazarovskoe",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-nazar').modal('show');
    });

    $('svg #text20619').click(function() {
        //Fancybox.show([{ src: "/include/ask_project.php", type: "inline" }]);
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-orp",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-orp').modal('show');
    });

    // $('svg #text19495').click(function() {
    //     Fancybox.show([{ src: "#nazarovskoe", type: "inline" }]);
    // });
    //
    // $('svg #text20619').click(function() {
    //     Fancybox.show([{ src: "#orp", type: "inline" }]);
    // });

    $('svg #text20884').click(function() {
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-kekura",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-kekura').modal('show');
    });

    $('.slider-block .slide1').click(function() {
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-kekura",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-servicedoc').modal('show');
    });

    $('.slider-block .slide2').click(function() {

        $('#modal-servicecity').modal('show');
    });

    $('.slider-block .slide3').click(function() {

        $('#modal-complexsmp').modal('show');
    });

    $('.slider-block .slide4').click(function() {

        $('#modal-pnr').modal('show');
    });

    $('.slider-block .slide5').click(function() {
        // Fancybox.show(
        //     // Array containing gallery items
        //     [
        //         // Gallery item
        //         {
        //             src: "#modal-kekura",
        //             type: "inline",
        //         },
        //     ],
        // );
        $('#modal-surveys').modal('show');
    });

    let textfile;
    let requiredtext = 'Это поле обязательно для заполнения';
    let maxlenghttext = "Поле должно быть максимум 500 символов";
    let extensiontext = "Поддерживаются форматы doc, docx, pdf";
    let structura = [];
    structura = ["Директор проектного офиса"
    ];

    let position = "Главный менеджер";
    let position2 = "Администратор проектов";

    if(getCookie("USER_LANG") == "en"){
        $('svg #tspan19391').text('Ozernoe');
        $('svg #tspan19501').text('Nazarovskoe');
        $('svg #tspan20739').text('Ozerninskaya mining area');
        $('svg #tspan21295').text('Kekura');
        textfile = "Choose File";
        structura = ["Project Office Director", "Direction of engineering", "Direction by configuration", "Direction in Economics and Finance",
            "Direction for calendar-network planning and control", "Direction for project administration"
        ]
        requiredtext = "This field is required";
        maxlenghttext = "The field must be a maximum of 500 characters";
        extensiontext = "doc, docx, pdf formats supported";
        position = "General manager";
        position2 = "Project Administrator";
    }
    if(getCookie("USER_LANG") == "ru"){
        textfile = "Выберите файл";

    }

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

        function replaceQueryParam(param, newval, search)
        {
            var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
            var query = search.replace(regex, "$1").replace(/&$/, '');
            return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval
            : '');
        }
        function action_lang(value)
        {
            window.location = replaceQueryParam('user_lang',
                value, window.location.search);
        }
    $('.lang #lang').click(function() {
        action_lang(this.innerHTML.toLowerCase())
        //document.cookie = 'USER_LANG='+this.innerHTML
        //location.reload();

    });

    // function openpopupsuccess(){
         //$('.bd-example-modal-sm.success').modal('show');
    // $(window).load(function() {
    //     $('.bd-example-modal-sm.success').modal('show');
    // });
    // }
    //
    // function openpopupfalse(){
         //$('.bd-example-modal-sm.false').modal('show');
    // }

    if ($('#idHf').val() !='') {
        idHf = $('#idHf').val();
    };


    $("form[name='form_resume']").validate({
        rules:{
            form_text_53:{
                required: true,
            },
            form_checkbox_AGREEMENT:{
                required: true,
             },
            form_textarea_64:{
                required: true,
                maxlength: 500,
            },
            form_file_57:{
                required: true,
                extension: "doc|docx|pdf",
            }
        },
        messages:{
            form_text_53:{
                required: requiredtext,
            },
            form_checkbox_AGREEMENT:{
                required: requiredtext,
            },
            form_textarea_64:{
                required: requiredtext,
                maxlength: maxlenghttext,
            },
            form_file_57:{
                required: requiredtext,
                extension: extensiontext
            }
        },
        //errorElement: "div",
        submitHandler: function( form ){
            if($("form[name='form_resume']").valid()){
                if (idHf != '') {
                    var $that = $("form[name='form_resume']"),
                        formData = new FormData($that.get(0));

                    $.ajax({
                        type: "POST",
                        url: "/ajax/translate.php",
                        data : formData,
                        processData: false,
                        contentType: false,
                        success: function(dataSuccess){
                            var jsonData = JSON.parse(dataSuccess);
                            if(jsonData.success){
                                //console.log(dataSuccess)
                                //Fancybox.show([{ src: "#modal-success", type: "inline" }]);
                                // var myModal = new bootstrap.Modal(document.getElementById('modal-success'), {
                                //     keyboard: false
                                // })
                                $('#modal-success').modal('show');
                            }else{
                                //console.log(dataSuccess)
                                //Fancybox.show([{ src: "#modal-fail", type: "inline" }]);
                                $('#modal-fail').modal('show');
                            }
                        },
                        error: function(error){
                            //console.log(error)
                            //Fancybox.show([{ src: "#modal-fail", type: "inline" }]);
                            $('#modal-fail').modal('show');
                        }
                    });
                }
            }
        },
    });

    $.validator.addMethod("extension", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
        return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
    }, "Please enter a value with a valid extension.");

    $(":file").filestyle({buttonBefore: true, btnClass: "btn-primary", text: textfile});

    window.onscroll = function() {scrollFunction()};

    //console.log(requiredtext)
    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("btntop").style.display = "block";

            document.getElementById("btntop").style.transform = "translateY(0px)";
        } else {

            document.getElementById("btntop").style.display = "none";
            document.getElementById("btntop").style.transform = "translateY(150px)";
        }
    }

// When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $('#btntop').click(function() {
        //topFunction();
        $('html, body').animate({scrollTop: 0},700);
        return false;
    });

    //if(window.innerWidth < 800){
            if(window.innerWidth < 800){
                var chart = new OrgChart(document.getElementById("tree"), {
                    template: "olivia",
                    enableSearch: false,
                    enableTouch: false,
                    enableDragDrop: false,
                    interactive: false,
                    //autoresize: true,
                    nodeMouseClick: OrgChart.action.none, //disable
                    mouseScrool: OrgChart.action.none,
                    nodeBinding: {
                        field_1: "title",
                        field_2: "position",
                        field_3: "line",
                        field_4: "titlemain",
                        img_0: "img",
                        img_1: "img1",
                    },
                    scaleInitial: OrgChart.match.boundary,
                    orientation: OrgChart.orientation.left,
                    //zoom: {speed: 130, smooth: 10},
                    nodes: [
                        { id: 1, titlemain: structura[0],line:"         ", img1: "/images/icons8-administrator-male-100.png" },
                        { id: 2, pid: 1, title: structura[1],line:"        ", position: position, img: "/images/icons8-engineer-90.png" },
                        { id: 3, pid: 1, title: structura[2],line:"        ", position: position,img: "/images/icons8-warehouse-100.png" },
                        { id: 4, pid: 1, title: structura[3],line:"        ", position: position,img: "/images/icons8-economics-98.png" },
                        { id: 5, pid: 1, title: structura[4],line:"        ", position: position,img: "/images/icons8-calendar-100.png" },
                        { id: 6, pid: 1, title: structura[5],line:"        ", position: position2,img: "/images/icons8-administrative-tools-100.png" }
                    ],
                });
            }else{
                var chart = new OrgChart(document.getElementById("tree"), {
                    template: "olivia",
                    enableSearch: false,
                    enableTouch: false,
                    enableDragDrop: false,
                    interactive: false,
                    //autoresize: true,
                    nodeMouseClick: OrgChart.action.none, //disable
                    mouseScrool: OrgChart.action.none,
                    nodeBinding: {
                        field_1: "title",
                        field_2: "position",
                        field_3: "line",
                        field_4: "titlemain",
                        img_0: "img",
                        img_1: "img1"
                    },
                    scaleInitial: OrgChart.match.boundary,
                    //zoom: {speed: 130, smooth: 10},
                    nodes: [
                        { id: 1, titlemain: structura[0], position: "",line:"        ", img1: "/images/icons8-administrator-male-100.png" },
                        { id: 2, pid: 1, title: structura[1], position: position,line:"        ", img: "/images/icons8-engineer-90.png" },
                        { id: 3, pid: 1, title: structura[2], position: position,line:"        ", img: "/images/icons8-warehouse-100.png" },
                        { id: 4, pid: 1, title: structura[3], position: position,line:"        ", img: "/images/icons8-economics-98.png" },
                        { id: 5, pid: 1, title: structura[4], position: position,line:"        ", img: "/images/icons8-calendar-100.png" },
                        { id: 6, pid: 1, title: structura[5], position: position2,line:"        ", img: "/images/icons8-administrative-tools-100.png" }
                    ],
                });
            }

    //console.log(structura)


    OrgChart.templates.olivia.field_1 =
        '<text data-width="200" data-text-overflow="multiline" x="249" y="40" text-anchor="end" class="field_1">{val}</text>';

    OrgChart.templates.olivia.field_2 =
        '<text data-width="200" data-text-overflow="multiline" x="248" y="140" text-anchor="end" class="field_2">{val}</text>';

    OrgChart.templates.olivia.field_3 =
        '<line x1="15" y1="120" x2="248" y2="120" style="stroke:rgb(254, 167, 94); stroke-width:2" />';

    OrgChart.templates.olivia.field_4 =
        '<text data-width="250" data-text-overflow="multiline" x="132" y="145" text-anchor="middle" class="field_4">{val}</text>';

    OrgChart.templates.olivia.img_1 =
        '<image preserveAspectRatio="xMidYMid slice" xlink:href="{val}" x="111" y="37" width="80" height="80"></image>';

    OrgChart.templates.olivia.img_0 =
        '<image preserveAspectRatio="xMidYMid slice" xlink:href="{val}" x="10" y="24" width="80" height="80"></image>';



    OrgChart.templates.olivia.size = [268, 160]

    //text = document.querySelectorAll("#tree [data-n-id='1'] .field_1 tspan")




    //parent = document.getElementById("tree");
    //svg = parent.children;
    //console.log(parent)
    //const {x, y, width, height} = svg.getBBox();
    //console.log(width);
    //console.log(svg)
    //var box = svg.getAttribute('viewBox');
    //box.split(/\s+|,/);
    //console.log(box);
    //svg.setAttribute("viewBox", `${x} ${y} ${width} ${height}`);

});