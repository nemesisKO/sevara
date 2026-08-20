import { router } from '@inertiajs/vue3';
import { markRaw } from 'vue';
import { toast } from 'vue-sonner';
import NewsletterSuccessToast from '@/components/public/NewsletterSuccessToast.vue';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
    router.on('flash', (event) => {
        const flash = (event as CustomEvent).detail?.flash;
        const data = flash?.toast as FlashToast | undefined;

        if (!data) {
            return;
        }

        if (data.variant === 'newsletter') {
            toast.custom(markRaw(NewsletterSuccessToast), {
                class: 'newsletter-toast',
                componentProps: {
                    message: data.message,
                },
                duration: 6500,
            });

            return;
        }

        toast[data.type](data.message);
    });
}
