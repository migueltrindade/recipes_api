(function($) {
  var RiotNotify = {

    init: function() {
      var o;
      if(this.opts['RiotNotifyOptions'] !== 'undefined') {
        for(o in this.opts['RiotNotifyOptions']) {
          this.RiotNotifyOptions[o] = this.opts['RiotNotifyOptions'][o];
        }
      }
    },

    RiotNotifyOptions : {
      clickToHide: true,
      autoHide: true,
      autoHideDelay: 5000,
      arrowShow: true,
      arrowSize: 5,
      position: 'top center',
      elementPosition: 'top center',
      globalPosition: 'top center',
      style: 'bootstrap',
      className: 'success',
      showAnimation: 'slideDown',
      showDuration: 400,
      hideAnimation: 'slideUp',
      hideDuration: 200,
      gap: 2
    },

    RiotNotifySend : function(notification, options, element) {
      if(!notification) {
        return;
      }
      options = Object.assign({}, this.RiotNotifyOptions, options);
      if(typeof element != 'undefined') {
        $.notify(element, notification, options)
      } else {
        $.notify(notification, options)
      }
    }
  };

  riot.mixin('RiotNotify', RiotNotify);
})(jQuery);
