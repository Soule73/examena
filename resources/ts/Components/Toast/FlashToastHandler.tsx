import { useEffect } from 'react';
import { useToast } from '@/Components/Toast';
import { FlashMessageObject, FlashMessages } from '@/types';

interface FlashToastHandlerProps {
    flash: FlashMessages;
}

const displayedIds = new Set<string>();

const FlashToastHandler: React.FC<FlashToastHandlerProps> = ({ flash }) => {
    const { success, error, warning, info } = useToast();

    useEffect(() => {
        const showToast = (type: 'success' | 'error' | 'warning' | 'info' | 'message', data?: FlashMessageObject) => {
            if (!data || !data.id || !data.message) return;

            if (displayedIds.has(data.id)) return;

            displayedIds.add(data.id);

            switch (type) {
                case 'success':
                    success(data.message, { title: 'Succès', duration: 4000 });
                    break;
                case 'error':
                    error(data.message, { title: 'Erreur', autoClose: false });
                    break;
                case 'warning':
                    warning(data.message, { title: 'Attention', duration: 6000 });
                    break;
                case 'info':
                    info(data.message, { title: 'Information', duration: 5000 });
                    break;
                case 'message':
                    info(data.message, { duration: 5000 });
                    break;
            }
        };

        showToast('success', flash.success);
        showToast('error', flash.error);
        showToast('warning', flash.warning);
        showToast('info', flash.info);
    }, [flash, success, error, warning, info]);

    return null;
};

export default FlashToastHandler;