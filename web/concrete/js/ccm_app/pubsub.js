var ccm_event = (function(window){
  "use strict";

  /**
   * concrete5 Event Managment
   * -------------------------
   *
   * The event management paradigm that is used here is a subscription to the
   * main object to attach to the correct dialog.
   *
   * Events that go along with created elements should define the element type
   * and pass the element dom object.
   */
  ccm_event = function(window) {
    var target = window.document.createElement('span');
    var self = this;

    // Handle subscribing
    self.sub = function (type, handler, elem) {
      var element = elem || target;
      if (element.addEventListener) {
        element.addEventListener(type.toLowerCase(), handler, false);
      } else {
        element.attachEvent('on' + type.toLowerCase(), handler);
      }
    };
    // Add aliases
    self.subscribe = self.bind = self.watch = self.sub;

    // Handle publishing
    self.pub = function (type, data, elem) {
      var event, eventName = 'CCMEvent', element = elem || target;
      if (document.createEvent) {
        event = document.createEvent("HTMLEvents");
        event.initEvent(type.toLowerCase(), true, true);
      } else {
        event = document.createEventObject();
        event.eventType = type.toLowerCase();
      }
      event.eventName = eventName;
      event.eventData = data || {};

      if (document.createEvent) {
        element.dispatchEvent(event);
      } else {
        element.fireEvent("on" + event.eventType, event);
      }
      if (typeof element['on' + type.toLowerCase()] === 'function') {
        element['on' + type.toLowerCase()](event);
      }
    };
    // Add aliases
    self.publish = self.fire = self.trigger = self.pub;

    return self;
  };
  return new ccm_event(window);
})(window);
window.ccm_subscribe = ccm_event.sub;
window.ccm_publish = ccm_event.pub;

/**
// Sample subscription.
ccm_subscribe('dialogOpened',function(event){
  data = event.eventData;

  if (data.type == "add_block_dialog") {
    ccm_subscribe('close',function(event){
      console.log('Closed Dialog.');
    }, data.element);
  }
});

// Sample publish
function launchAddDialog(){
  var element = $('<div><span>Add Dialog</span></div>');
  $.fn.dialog.open({
    element:element,
    onClose:function(){
      ccm_publish('close',{},data.element);
    }
  });
  var data = {};
  data.element = element.get(0);
  data.type = 'add_block_dialog';
  ccm_publish('dialogOpened',data);
}
*/