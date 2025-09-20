import { useEffect, useRef } from 'react';
import { useToast } from '@/Components/Toast';
import { FlashMessages } from '@/types';

interface FlashToastHandlerProps {
    flash: FlashMessages;
}

const FlashToastHandler: React.FC<FlashToastHandlerProps> = ({ flash }) => {
    const { success, error, warning, info } = useToast();
    const processedMessages = useRef<Set<string>>(new Set());

    useEffect(() => {
        const messageKey = JSON.stringify(flash);

        if (processedMessages.current.has(messageKey)) {
            return;
        }

        let hasMessages = false;

        if (flash.success) {
            success(flash.success, {
                title: 'Succès',
                duration: 4000
            });
            hasMessages = true;
        }

        if (flash.error) {
            error(flash.error, {
                title: 'Erreur',
                autoClose: false
            });
            hasMessages = true;
        }

        if (flash.warning) {
            warning(flash.warning, {
                title: 'Attention',
                duration: 6000
            });
            hasMessages = true;
        }

        if (flash.info) {
            info(flash.info, {
                title: 'Information',
                duration: 5000
            });
            hasMessages = true;
        }

        if (flash.message) {
            info(flash.message, {
                duration: 5000
            });
            hasMessages = true;
        }

        if (hasMessages) {
            processedMessages.current.add(messageKey);
        }
    }, [flash, success, error, warning, info]);

    return null;
};

export default FlashToastHandler;