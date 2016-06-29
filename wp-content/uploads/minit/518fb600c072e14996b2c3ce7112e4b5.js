!function(a,b){"use strict";function c(){if(!e){e=!0;var a,c,d,f,g=-1!==navigator.appVersion.indexOf("MSIE 10"),h=!!navigator.userAgent.match(/Trident.*rv:11\./),i=b.querySelectorAll("iframe.wp-embedded-content");for(c=0;c<i.length;c++)if(d=i[c],!d.getAttribute("data-secret")){if(f=Math.random().toString(36).substr(2,10),d.src+="#?secret="+f,d.setAttribute("data-secret",f),g||h)a=d.cloneNode(!0),a.removeAttribute("security"),d.parentNode.replaceChild(a,d)}else;}}var d=!1,e=!1;if(b.querySelector)if(a.addEventListener)d=!0;if(a.wp=a.wp||{},!a.wp.receiveEmbedMessage)if(a.wp.receiveEmbedMessage=function(c){var d=c.data;if(d.secret||d.message||d.value)if(!/[^a-zA-Z0-9]/.test(d.secret)){var e,f,g,h,i,j=b.querySelectorAll('iframe[data-secret="'+d.secret+'"]'),k=b.querySelectorAll('blockquote[data-secret="'+d.secret+'"]');for(e=0;e<k.length;e++)k[e].style.display="none";for(e=0;e<j.length;e++)if(f=j[e],c.source===f.contentWindow){if(f.removeAttribute("style"),"height"===d.message){if(g=parseInt(d.value,10),g>1e3)g=1e3;else if(200>~~g)g=200;f.height=g}if("link"===d.message)if(h=b.createElement("a"),i=b.createElement("a"),h.href=f.getAttribute("src"),i.href=d.value,i.host===h.host)if(b.activeElement===f)a.top.location.href=d.value}else;}},d)a.addEventListener("message",a.wp.receiveEmbedMessage,!1),b.addEventListener("DOMContentLoaded",c,!1),a.addEventListener("load",c,!1)}(window,document);

var disqus_url = embedVars.disqusUrl;
var disqus_identifier = embedVars.disqusIdentifier;
var disqus_container_id = 'disqus_thread';
var disqus_shortname = embedVars.disqusShortname;
var disqus_title = embedVars.disqusTitle;
var disqus_config_custom = window.disqus_config;
var disqus_config = function () {
    /*
    All currently supported events:
    onReady: fires when everything is ready,
    onNewComment: fires when a new comment is posted,
    onIdentify: fires when user is authenticated
    */
    if (typeof embedVars.disqusConfig.remote_auth_s3 !== 'undefined') {
        this.page.remote_auth_s3 = embedVars.disqusConfig.remote_auth_s3;
    }

    if (typeof embedVars.disqusConfig.api_key !== 'undefined') {
        this.page.api_key = embedVars.disqusConfig.api_key;
    }

    if (typeof embedVars.disqusConfig.sso !== 'undefined') {
        this.sso = {
            name: embedVars.disqusConfig.sso.name,
            button: embedVars.disqusConfig.sso.button,
            url: embedVars.disqusConfig.sso.url,
            logout: embedVars.disqusConfig.sso.logout,
            width: embedVars.disqusConfig.sso.width,
            height: embedVars.disqusConfig.sso.height
        };
    }

    this.language = embedVars.disqusConfig.language;
    this.callbacks.onReady.push(function () {
        if (!embedVars.options.manualSync) {
            // sync comments in the background so we don't block the page
            var script = document.createElement('script');
            script.async = true;
            script.src = '?cf_action=sync_comments&post_id=' + embedVars.postId;

            var firstScript = document.getElementsByTagName('script')[0];
            firstScript.parentNode.insertBefore(script, firstScript);
        }
    });

    if (disqus_config_custom) {
        disqus_config_custom.call(this);
    }
};

(function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript';
    dsq.async = true;
    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
})();


function PASfunctions(){jQuery(function(o){if(o("body").on("click","a.paspopup",function(a){var t=o(this).attr("adzone_id"),n=o(this).attr("popup_type");return setTimeout(function(){loadPASPopup(t,n,ajaxurl)},0),!1}),o("body").on("click","div.close_paspopup",function(o){disablePASPopup()}),o(this).keyup(function(o){27==o.which&&disablePASPopup()}),o("body").on("click","div#backgroundPasPopup",function(o){disablePASPopup()}),o("body").on("click","div.close_pasflyin",function(o){disablePASFlyIn()}),o("body").on("click",function(a){var t=o(a.target),n=a.target.id,s={link_full:"",link_full_target:"_blank",pas_container:"body",link_left:"",link_right:""},i=o.extend({},s,clickable_paszone);if(t.is(i.pas_container)||n==i.pas_container){if(""==i.link_full||null==i.link_full){var p=o(document).width();if(a.pageX<=p/2){if(""!=i.link_left)var e=window.open(i.link_left,i.link_left_target)}else if(""!=i.link_right)var e=window.open(i.link_right,i.link_right_target)}else if(""!=i.link_full)var e=window.open(i.link_full,i.link_full_target);e.focus()}}),o.fn.followTo=function(a){var t=this,n=o(window),s=t.offset().top;$bumper=o(a),bumperPos=t.offset().bottom,$bumper.length&&(bumperPos=$bumper.offset().top);var i=t.outerHeight(),p=function(){n.scrollTop()>bumperPos-(i+50)?t.toggleClass("pas_sticky",n.scrollTop()<s):t.toggleClass("pas_sticky",n.scrollTop()>s)};n.scroll(p),p()},o("#pas-sticky-div").length>0){var a=o("#pas-sticky-div").attr("stick_till");o("#pas-sticky-div").followTo(a)}setTimeout(function(){checkAdStatus()||(console.log("You are using AD Blocker!"),o.ajax({type:"POST",url:ajaxurl,data:"action=adblocker_detected"}).done(function(o){msg=JSON.parse(o),msg.alert&&alert(msg.alert)}))},500)})}function loadPASPopup(o,a,t,n,s){jQuery(function(a){n?Cookies.get("wpproads-popup-"+o)?disablePASPopup():(Cookies.set("wpproads-popup-"+o,new Date(a.now())),delayPASPopup(s)):delayPASPopup(s)})}function delayPASPopup(o){if(o){var a=1e3*o;setTimeout(function(){showPASPopup()},a)}else showPASPopup()}function showPASPopup(){if(0==paspopupStatus&&(jQuery("html").addClass("wppas-model-open"),jQuery(".PasPopupCont").fadeIn(320),jQuery(".PasPopupCont").css({visibility:"visible",opacity:1}),jQuery("#backgroundPasPopup").fadeIn(1),paspopupStatus=1,jQuery("#backgroundPasPopup").hasClass("autoclose"))){var o=jQuery("#backgroundPasPopup").attr("closesec");setTimeout(function(){closePASPopup()},o)}}function disablePASPopup(){jQuery(function(o){1==paspopupStatus&&(o("#backgroundPasPopup").hasClass("autoclose")||closePASPopup())})}function closePASPopup(){jQuery(function(o){1==paspopupStatus&&(o(".PasPopupCont").fadeOut("normal"),o("#backgroundPasPopup").fadeOut("normal"),o("html").removeClass("wppas-model-open"),paspopupStatus=0)})}function loadPASFlyIn(o,a,t,n){jQuery(function(t){var s=1e3*a;setTimeout(function(){n?Cookies.get("wpproads-flyin-"+o)||(Cookies.set("wpproads-flyin-"+o,new Date(t.now())),t(".pas_fly_in").css({visibility:"visible"}).effect("shake"),t(".pas_fly_in").addClass("showing")):(t(".pas_fly_in").css({visibility:"visible"}).effect("shake"),t(".pas_fly_in").addClass("showing"))},s)})}function disablePASFlyIn(){jQuery(function(o){o(".pas_fly_in").fadeOut("normal")})}function checkAdStatus(){var o=!0;return window.wpproads_no_adblock!==!0&&(o=!1),o}var paspopupStatus=0,clickable_paszone,ajaxurl=wppas_ajax_script.ajaxurl;jQuery(document).ready(function(o){new PASfunctions});

var disqus_shortname = countVars.disqusShortname;
(function () {
    var nodes = document.getElementsByTagName('span');
    for (var i = 0, url; i < nodes.length; i++) {
        if (nodes[i].className.indexOf('dsq-postid') != -1) {
            nodes[i].parentNode.setAttribute('data-disqus-identifier', nodes[i].getAttribute('data-dsqidentifier'));
            url = nodes[i].parentNode.href.split('#', 1);
            if (url.length == 1) { url = url[0]; }
            else { url = url[1]; }
            nodes[i].parentNode.href = url + '#disqus_thread';
        }
    }
    var s = document.createElement('script'); s.async = true;
    s.type = 'text/javascript';
    s.src = '//' + disqus_shortname + '.disqus.com/count.js';
    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
}());
