$(document).ready(function () {

    var count_banner_img = $(".bannerimg").length;
    var now_banner_img = 1;
    var prev_banner_img = 1;
    animbaner();
    function animbaner() {

        $(".ban" + now_banner_img).show();
        $(".ban" + now_banner_img).css('margin-top', '-75px');
        $(".ban" + now_banner_img).css('margin-left', '-160px');
        $(".ban" + now_banner_img).css('width', '650px');
        $(".ban" + now_banner_img).css('height', '160px');

        $(".ban" + now_banner_img).animate({
            opacity: 1,
            marginTop: '-35px',
            marginLeft: '-80px',
            width: '327px',
            height: '80px'
        }, 600, function () {
            setTimeout(function () {
                $(".ban" + prev_banner_img).fadeTo(1000, 0, function () {
                    $(this).hide();
                });
            }, 4000);

            setTimeout(animbaner, 4200);

        });

        prev_banner_img = now_banner_img;
        now_banner_img++;
        if (now_banner_img > count_banner_img) {
            now_banner_img = 1;
        }

    }

    $("#Search1_txtSearch").keyup(function () {
        var search = $("#Search1_txtSearch").val();
        if (search) {
            $.ajax({
                type: "POST",
                url: "/fastsearch?a=search&mode=lite",
                data: {"search": search},
                cache: false,
                success: function (response) {
                    $(".fastsearchhbox").html(response);
                    $(".fastsearchhbox").show();
                }
            });
        }
        else {
            $(".fastsearchhbox").hide();
        }
        return false;
    });


    $(".txtall").keyup(function () {
        var search = $(".txtall").val();
        if (search) {
            $.ajax({
                type: "POST",
                url: "/fastsearch?a=search&mode=lite",
                data: {"search": search},
                cache: false,
                success: function (response) {
                    $(".fastsearchhbox").html(response);
                    $(".fastsearchhbox").show();
                }
            });
        }
        else {
            $(".fastsearchhbox").hide();
        }
        return false;
    });


    $(".hrefrow").click(function () {
        window.location.href = $(this).attr('href');
    });

    $("body").click(function () {
        $(".fastsearchhbox").hide();
    });

    $(".enterlink").click(function () {
        $('.loginform').show();
        $('.enterbox').hide();
        $('.socialbutt').show();
        return false;
    });

    //$(".showblock").click(function() {
    //$('.blockcats').hide();
    //$("#block"+$(this).attr('tag')).show();
    //return false;
    //});

    $(".impscrollup").click(function () {
        $("body").scrollTop(0);
        $('.impscrollup').hide();
    });


    $(window).scroll(function () {
        var scro = $(this).scrollTop();
        if (scro >= 400) {
            $('.impscrollup').show();
        }
        else {
            $('.impscrollup').hide();
        }
        if (scro >= 150) {
            if ($('.maintop').css('margin-top') == '23px') {
                $('.maintop').css('margin-top', '-323px');
                $('.topmenuall').fadeTo(500, 1);
            }
        }
        else {
            $('.maintop').css('margin-top', '23px');
            $('.maintop').hide();
            $('.topmenuall').hide();
        }
    });


    $("a.asd").fancybox({
        'transitionIn': 'none',
        'transitionOut': 'none',
        'titlePosition': 'over'
    });

    $("#various5").fancybox({
        'width': 700,
        'height': 500,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'type': 'iframe',
        'scrolling': 'auto'
    });

    $("#voite5").fancybox({
        'width': 600,
        'height': 520,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'type': 'iframe',
        'scrolling': 'auto'
    });

    $("#voite6,.voite6").fancybox({
        'width': 600,
        'height': 520,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'type': 'iframe',
        'scrolling': 'auto'
    });

    $("a.addcart").fancybox({
        'width': 800,
        'height': 400,
        'autoScale': false,
        'transitionIn': 'none',
        'transitionOut': 'none',
        'type': 'iframe',
        'scrolling': 'auto'
    });


    $('table#naprs').mouseover(function () {
        $('.blockcats').hide();
        document.getElementById("block" + $(this).attr('src')).style.display = "block";
        //document.getElementById("block"+$(this).attr('src')).style.backgroundColor="#eee";
        document.getElementById("block" + $(this).attr('src')).style.fontStyle = "italic";
        document.getElementById("block" + $(this).attr('src')).style.marginLeft = "-10px";

    });
    $('table#naprs').mouseout(function () {
        document.getElementById("block" + $(this).attr('src')).style.background = "none";
        document.getElementById("block" + $(this).attr('src')).style.fontStyle = "normal";
        document.getElementById("block" + $(this).attr('src')).style.marginLeft = "0px";
    });

    var url = location.href;
    var url_index;
    url_index = url.substring(url.indexOf('#'), 0);
    if (!url_index) {
        url_index = url;
    }

    if (url.indexOf("#") > 0) {
        var id_layer = url.substring(url.indexOf('#') + 1, url.length);
        if (id_layer == 'tech') {
            id_layer = 'layer_tech'
        }
        else if (id_layer == 'recommend') {
            id_layer = 'layer_recommend'
        }
        else if (id_layer == 'files') {
            id_layer = 'layer_files'
        }
        else if (id_layer == 'othergoods') {
            id_layer = 'layer_othergoods'
        }
        else {
            id_layer = ''
        }
        if (id_layer) {
            $('.ttabslay').addClass('hidden');
            $(".ttabs li").removeClass('active');
            $('.' + id_layer).removeClass('hidden');
            $('#' + id_layer).parent().addClass('active');
        }
    }

    $(".ttabs li a").click(function () {
        //alert($(this).attr('id'));

        $('.ttabslay').addClass('hidden');
        $('.' + $(this).attr('id')).removeClass('hidden');

        $(".ttabs li").removeClass('active');
        $('#' + $(this).attr('id')).parent().addClass('active');

        if ($(this).attr('id') == 'layer_description') {
            history.pushState({}, "", url_index);
            return false;
        }
    });


});


function getElementsByClassName(cl) {
    var retnode = [];
    var myclass = new RegExp('\\b' + cl + '\\b');
    var elem = document.getElementsByTagName('*');
    for (var i = 0; i < elem.length; i++) {
        var classes = elem[i].className;
        if (myclass.test(classes)) retnode.push(elem[i]);
    }
    return retnode;
}

function mainOver(obj, id) {
    obj.className = "mpoint " + id + " selected" + id;
    var sub = document.getElementById(id);
    $(sub).show();
    //sub.style.display = 'block';
}

function mainOut(obj, id) {
    obj.className = "mpoint " + id;
    var sub = document.getElementById(id);
    $(sub).hide();
}

function overPunkt(obj) {
    if (obj.childNodes.length >= 1) {
        obj.childNodes[0].className = "ulhover";

        if (obj.childNodes.length >= 2) {
            $(obj.childNodes[1]).show();
            var pos = 115 - Math.round(($(obj).width()) / 2);
        }
    }
}
function outPunkt(obj) {
    if (obj.childNodes.length >= 1) {
        obj.childNodes[0].className = "ulout";
        if (obj.childNodes.length >= 2) {
            $(obj.childNodes[1]).hide();
        }
    }
}

function makeGray(obj, state, text) {
    if (state && (obj.value == '')) {
        obj.value = text;
    } else if (!state && (obj.value == text)) {
        obj.value = '';
    }
}