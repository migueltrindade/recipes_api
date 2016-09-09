<riot-tags-class-toggle>
    <ul class="riot-tags-class-toggle">
        <li each="{ si, i in opts.class_config }" class={'toggle-' + si.class + (parent.active_classes[si.class] ? ' active' : '') }>
            <a onclick="{ toggleClicked }"
               data-class="{ si.class }"><i></i>{ parent.active_classes[si.class] ? si.label_active : si.label_inactive }</a>
        </li>
    </ul>
    <script>
        (function(tag, $) {
            tag.active_count = 0
            tag.active_classes = {}
            for(i in opts.class_config) {
                if(opts.class_config[i].default == 'true') {
                    tag.active_classes[opts.class_config[i].class] = true
                    tag.active_count++
                }
            }
            tag.one('mount', function() {
                tag.update()
            })
            tag.on('update',function() {
                for(i in opts.class_config) {
                    $(tag.root).parent().toggleClass(opts.class_config[i].class, typeof tag.active_classes[opts.class_config[i].class] == 'undefined' ? false : tag.active_classes[opts.class_config[i].class])
                }
            })
            tag.toggleClicked = function(e) {
                if(tag.active_classes[e.item.si.class]) {
                    if(tag.active_count > 1 || opts.emtpy) {
                        tag.active_classes[e.item.si.class] = false
                        tag.active_count--
                    }
                } else {
                    if(parseInt(opts.multi) == 0) {
                        tag.active_classes = {}
                        tag.active_count = 0
                    }
                    tag.active_classes[e.item.si.class] = true
                    tag.active_count++
                }
                tag.update()
            }
        })(this, jQuery);
    </script>
    <style scoped>
        @import "riot-tags-class-toggle";
    </style>
</riot-tags-class-toggle>
