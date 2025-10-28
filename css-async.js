/**
 * ContentFreaks CSS Async Loader
 * 非クリティカルCSSの非同期読み込みスクリプト
 */
(function() {
    'use strict';
    
    /**
     * CSSを非同期で読み込む
     */
    function loadCSS(href, before, media, attributes) {
        var doc = window.document;
        var ss = doc.createElement('link');
        var ref;
        
        if (before) {
            ref = before;
        } else {
            var refs = (doc.body || doc.getElementsByTagName('head')[0]).childNodes;
            ref = refs[refs.length - 1];
        }
        
        var sheets = doc.styleSheets;
        
        // 属性設定
        if (attributes) {
            for (var attributeName in attributes) {
                if (attributes.hasOwnProperty(attributeName)) {
                    ss.setAttribute(attributeName, attributes[attributeName]);
                }
            }
        }
        
        ss.rel = 'stylesheet';
        ss.href = href;
        ss.media = 'only x'; // 一時的に無効なメディアクエリ
        
        // 読み込み完了を待つ
        function ready(cb) {
            if (doc.body) {
                return cb();
            }
            setTimeout(function() {
                ready(cb);
            });
        }
        
        // CSSが読み込まれたかチェック
        function onloadcssdefined(cb) {
            var resolvedHref = ss.href;
            var i = sheets.length;
            
            while (i--) {
                if (sheets[i].href === resolvedHref) {
                    return cb();
                }
            }
            
            setTimeout(function() {
                onloadcssdefined(cb);
            });
        }
        
        // ロード完了時の処理
        function loadCB() {
            if (ss.addEventListener) {
                ss.removeEventListener('load', loadCB);
            }
            ss.media = media || 'all';
        }
        
        // イベントリスナー
        if (ss.addEventListener) {
            ss.addEventListener('load', loadCB);
        }
        
        ss.onloadcssdefined = onloadcssdefined;
        
        onloadcssdefined(loadCB);
        
        ready(function() {
            ref.parentNode.insertBefore(ss, before ? ref : ref.nextSibling);
        });
        
        return ss;
    }
    
    // グローバルに公開
    if (typeof module !== 'undefined') {
        module.exports = loadCSS;
    } else {
        window.loadCSS = loadCSS;
    }
}());

/**
 * preload polyfill
 */
(function(w) {
    'use strict';
    
    if (!w.loadCSS) {
        return;
    }
    
    var rp = loadCSS.relpreload = {};
    
    rp.support = function() {
        try {
            return w.document.createElement('link').relList.supports('preload');
        } catch (e) {
            return false;
        }
    };
    
    rp.poly = function() {
        var links = w.document.getElementsByTagName('link');
        
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            
            if (link.rel === 'preload' && link.getAttribute('as') === 'style') {
                w.loadCSS(link.href, link, link.getAttribute('media'));
                link.rel = null;
            }
        }
    };
    
    if (!rp.support()) {
        rp.poly();
        
        var run = w.setInterval(rp.poly, 300);
        
        if (w.addEventListener) {
            w.addEventListener('load', function() {
                rp.poly();
                w.clearInterval(run);
            });
        }
        
        if (w.attachEvent) {
            w.attachEvent('onload', function() {
                w.clearInterval(run);
            });
        }
    }
}(this));
