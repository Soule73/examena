interface TextEntry {
    label: string;
    value: string;
    className?: string;
    valueClass?: string;
    labelClass?: string;
}


const TextEntry: React.FC<TextEntry> = ({ label, value, valueClass, labelClass, className }) => {
    return (
        <div className={`flex flex-col space-y-1 ${className || ''}`}>
            <span className={`text-sm font-bold text-gray-900 ${labelClass || ''}`}>{label}</span>
            <span className={`text-sm text-gray-600 ${valueClass || ''}`}>{value}</span>
        </div>
    );
};

export default TextEntry;