!function(a){"use strict";a.c5=a.c5||{},a.ConcreteEvent=function(a,b){function c(a){return a||(a=d),a instanceof b||(a=b(a)),a.length||(a=d),a}var d=b("<span />"),e={subscribe:function(a,b,d){return a instanceof Array?_(a).each(function(a){e.subscribe(a,b,d)}):(c(d).bind(a.toLowerCase(),b),e)},publish:function(a,b,d){return a instanceof Array?_(a).each(function(a){e.publish(a,b,d)}):(c(d).trigger(a.toLowerCase(),b),e)},unsubscribe:function(a,d,f){var g=[a.toLowerCase()];return void 0!==typeof d&&g.push(d),b.fn.unbind.apply(c(f),g),e}};return e.sub=e.bind=e.watch=e.on=e.subscribe,e.pub=e.fire=e.trigger=e.publish,e.unsub=e.unbind=e.unwatch=e.off=e.unsubscribe,a.event=e,e}(a.c5,jQuery)}(window,jQuery);