jQuery(document).ready(function ($) {
    var origin_url = window.location.origin;
    var cur_url = window.location.href;
    var thisPage = window.location.protocol + "//" + window.location.hostname + window.location.pathname;
    if (cur_url.startsWith(origin_url) && cur_url.includes('?')) {
        let getTheReferer = getRefererUrl();
        let findingOReferer = cur_url.indexOf("origin_referer");
        let newReferer = "referer=" + getTheReferer;
        let newOriginReferer = "origin_referer=" + getTheReferer;
        let SubstrArray = cur_url.split("?");
        if (findingOReferer !== -1) {

            let paramArray = SubstrArray[1].split("&");

            for (i = 0; i < paramArray.length; i++) {
                let matchParam1 = paramArray[i].match(/referer=/g);
                if (matchParam1) {
                    let indexedat = paramArray.indexOf(paramArray[i]);
                    let r_pos = paramArray[i].indexOf("r");
                    if (r_pos !== -1 && r_pos == 0) {
                        paramArray.splice(indexedat, 1);
                        paramArray.splice(indexedat, 0, newReferer);
                    }
                }
            }
            let newParametersStr = paramArray.toString();
            newParametersStr = newParametersStr.replace(/,/g, "&");
            newParametersStr = "?" + newParametersStr;
            let encodedUrl = newParametersStr;
            changeParametersForDirect(encodedUrl);
        }
        else {
            newReferer = "&" + newReferer;
            newOriginReferer = "&" + newOriginReferer;
            let newParameters = newReferer.concat(newOriginReferer);
            let newParametersStr = "?" + SubstrArray[1].concat(newParameters);
            let encodedUrl = newParametersStr;
            changeParametersForDirect(encodedUrl);
        }
    }
    else {
        let getTheReferer = getRefererUrl();
        if (getTheReferer == "Direct") {
            u_source = "utm_source=Direct";
            u_medium = "&utm_medium=" + thisPage;
            u_referer = "&referer=" + getTheReferer;
            origin_referer = "&origin_referer=" + thisPage;
            let query_string = "?" + u_source + u_medium + u_referer + origin_referer;
            let encodedUrl = query_string;
            changeParametersForDirect(encodedUrl);
        }
        else {
            let refererType = getTheReferer.match(/kxstage/g);
            if (refererType) { }
            else {
                u_source = "utm_source=website_organic";
                u_medium = "&utm_medium=" + getTheReferer;
                u_referer = "&referer=" + getTheReferer;
                origin_referer = "&origin_referer=" + getTheReferer;
                let query_string = "?" + u_source + u_medium + u_referer + origin_referer;
                let encodedUrl = query_string;
                changeParametersForDirect(encodedUrl);
            }
        }
    }

    function getRefererUrl() {
        var refererUrl = document.referrer;
        let checkRefererUrl = refererUrl.match(/\?/g);
        if (checkRefererUrl) {
            let newrefererUrl = refererUrl.split("?");
            refererUrl = newrefererUrl[0];
        }

        var isreferer = '';
        if (refererUrl) {
            let match_url = refererUrl
            let resultOfMatch = match_url.match(/healthray.com/g);
            if (resultOfMatch) {
                isreferer = refererUrl;
                return (isreferer);
            }
            else {
                isreferer = refererUrl;
                return (isreferer);
            }
        }
        else {
            isreferer = "Direct";
            return (isreferer);
        }
    }
    function isUrlValid(url) {
        return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
    }

    function changeParametersForDirect(encodedUrl) {
        let urlArray = Array();
        jQuery('.elementor-567').find('a').each(function () {
            var getUrls = jQuery(this).attr('href');
            if (isUrlValid(getUrls)) {
                if (!getUrls.match(/\?/g)) {
                    let new_url = getUrls.concat(encodedUrl);
                    jQuery(this).attr('href', new_url);
                }
            }
        });
        jQuery('.wrap').find('a').each(function () {
            var getUrls = jQuery(this).attr('href');
            if (isUrlValid(getUrls)) {
                if (!getUrls.match(/\?/g)) {
                    let new_url = getUrls.concat(encodedUrl);
                    jQuery(this).attr('href', new_url);
                }
            }
        });
        jQuery('footer').find('a').each(function () {
            var getUrlsF = jQuery(this).attr('href');
            if (isUrlValid(getUrlsF)) {
                if (!getUrlsF.match(/\?/g)) {
                    let new_url = getUrlsF.concat(encodedUrl);
                    jQuery(this).attr('href', new_url);
                }
            }
        });
    }
});