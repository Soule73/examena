import { SelectHTMLAttributes, forwardRef } from 'react';

interface Option {
    value: string | number;
    label: string;
    disabled?: boolean;
}

interface SelectProps extends SelectHTMLAttributes<HTMLSelectElement> {
    label?: string;
    error?: string;
    helperText?: string;
    options: Option[];
    placeholder?: string;
}

const Select = forwardRef<HTMLSelectElement, SelectProps>(
    ({ label, error, helperText, options, placeholder, className = '', ...props }, ref) => {
        const baseClasses = 'w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200 bg-white';
        const errorClasses = error
            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
            : 'border-gray-300 focus:border-blue-500';

        const finalClassName = `${baseClasses} ${errorClasses} ${className}`;

        return (
            <div className="w-full">
                {label && (
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                        {label}
                    </label>
                )}
                <select
                    ref={ref}
                    className={finalClassName}
                    {...props}
                >
                    {placeholder && (
                        <option value="">{placeholder}</option>
                    )}
                    {options.map((option) => (
                        <option
                            key={option.value}
                            value={option.value}
                            disabled={option.disabled}
                        >
                            {option.label}
                        </option>
                    ))}
                </select>
                {error && (
                    <p className="mt-1 text-sm text-red-600">{error}</p>
                )}
                {helperText && !error && (
                    <p className="mt-1 text-sm text-gray-500">{helperText}</p>
                )}
            </div>
        );
    }
);

Select.displayName = 'Select';

export default Select;