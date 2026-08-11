<script lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed, defineComponent, h  } from 'vue';
import type {PropType} from 'vue';

/**
 * Injects a schema.org block into the document head.
 *
 * Written as a render function because Vue templates cannot contain a script
 * tag, and Inertia's head manager discards components placed inside <Head> —
 * it only serialises plain elements.
 */
export default defineComponent({
    name: 'JsonLd',

    props: {
        data: {
            type: Object as PropType<Record<string, unknown>>,
            required: true,
        },

        /** Lets a page replace an inherited block instead of stacking a second one. */
        headKey: {
            type: String,
            default: 'json-ld',
        },
    },

    setup(props) {
        /**
         * Inertia writes head children out verbatim, so every "<" is escaped to
         * stop a value containing a closing script tag from ending it early.
         */
        const json = computed(() =>
            JSON.stringify(props.data).replace(/</g, '\\u003c'),
        );

        return () =>
            h(Head, null, {
                default: () => [
                    h(
                        'script',
                        {
                            type: 'application/ld+json',
                            'head-key': props.headKey,
                        },
                        json.value,
                    ),
                ],
            });
    },
});
</script>
