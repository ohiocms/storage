import shared from 'belt/clip/js/attachments/ctlr/shared';

// components
import tags from 'belt/glue/js/taggables/ctlr-edit';

export default {
    mixins: [shared],
    components: {
        tab: tags,
    },
}