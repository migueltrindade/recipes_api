riot.tag('subtag', '', function(opts) {
  var htmlTag = opts.tag;
  if(typeof htmlTag != 'undefined') {
    (function($, tag) {
      var options = {}
      if(typeof opts.options !== 'undefined') {
        Object.assign(options, opts.options)
      }
      riot.mount(tag.root, htmlTag, options);
    })(jQuery, this);
  }
});
