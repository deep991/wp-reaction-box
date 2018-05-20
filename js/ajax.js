
jQuery(document).ready(function ($) {

    if (hkpFeelboxAjax.onloadAjax == 1) {
        $(window).load(function () {
            jQuery.post(
                    hkpFeelboxAjax.ajaxurl, {
                        action: hkpFeelboxAjax.onloadaction,
                        postID: hkpFeelboxAjax.id,
                        token: hkpFeelboxAjax.token,
                    },
                    function (response) {

                        var percentage;
                        $.each(response, function (key, value) {
                            $.each(value, function (mood, votes) {
                                $('.feelbox-emotion-pic.' + mood).next().html( returnVoteCount( votes ) );
                                percentage = ((votes * 100) / response.totalVotes);
                                $('#sparkbardiv .spark.' + mood).css('width', percentage + '%');
                                $('#sparkbardiv .spark.' + mood).attr('title', ucwords(mood) + ' ' + percentage.toFixed(0) + '%');
                            });
                        });

                    });
        });
    }





    function returnVoteCount( votes ) {

        if (votes > 999) {
            vote = votes / 1000;
            return Math.floor ( vote ) +"k+";
        } else {
            return votes;
        }

    }


    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ')
                c = c.substring(1);
            if (c.indexOf(name) == 0)
                return c.substring(name.length, c.length);
        }
        return "";
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }





    jQuery("#hkp-feelbox-widget .feelbox-emotion-pic").click(function () {

        if (!getCookie("hkp_reaction_" + hkpFeelboxAjax.id)) {

            /* var reaction = $(this).children('.feelbox-emotion-pic').attr('data-id');
             var votes = $(this).children().attr('class'); */
            $('#hkp-feelbox-widget #bd #loading').show();
            var reaction = $(this).attr('data-id');
            var votes = $(this).attr('class');
            votes = '.' + votes.replace(/\s+/g, '.');
            if (typeof (hkpFeelboxAjax) != 'undefined' && (typeof (hkpFeelboxAjax.ajaxurl) != undefined)) {
                jQuery.post(
                        hkpFeelboxAjax.ajaxurl, {
                            action: hkpFeelboxAjax.action,
                            postID: hkpFeelboxAjax.id,
                            token: hkpFeelboxAjax.token,
                            reaction: reaction
                        },
                function (response) {

                    $(votes).next().html(response.formatedVote);
                    setCookie("hkp_reaction_" + hkpFeelboxAjax.id, reaction, 1);
                    $('#hkp-feelbox-widget #bd #loading').hide();

                    $('#wp-reactions .wp-emotion-icon').each(function () {
                        var moods = $(this).children('.feelbox-emotion-pic').attr('data-id');
                        var mvotes = $(this).children('.feelbox-emotion-pic').next().html();
                        percentage = ((mvotes * 100) / response.totalvotes);

                        $('#sparkbardiv .spark.' + moods).css('width', percentage + '%');
                        $('#sparkbardiv .spark.' + moods).attr('title', ucwords(moods) + ' ' + percentage.toFixed(0) + '%');
                    });

                    if (hkpFeelboxAjax.social == 1) {
                        $('#hkp-feelbox-social').show();
                    }

                });
            }
        }
    });

    jQuery('#hkp-feelbox-social #clr').click(function () {
        $('#hkp-feelbox-social').hide();
    });

});