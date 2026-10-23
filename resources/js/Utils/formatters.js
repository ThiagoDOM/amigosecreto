/**
 * Formats date from YYYY-MM-DD or ISO string to Brazilian DD/MM/YYYY
 *
 * @param {string|null} dateStr
 * @returns {string}
 */
export function formatDate(dateStr) {
    if (!dateStr) return '';
    
    // Check if already in DD/MM/YYYY format
    if (/^\d{2}\/\d{2}\/\d{4}/.test(dateStr)) {
        return dateStr.substring(0, 10);
    }

    // Split YYYY-MM-DD
    const parts = dateStr.split('T')[0].split('-');
    if (parts.length === 3) {
        const [year, month, day] = parts;
        return `${day.padStart(2, '0')}/${month.padStart(2, '0')}/${year}`;
    }

    return dateStr;
}

/**
 * Format phone string to (XX) XXXXX-XXXX or (XX) XXXX-XXXX
 *
 * @param {string|null} phone
 * @returns {string}
 */
export function formatPhone(phone) {
    if (!phone) return '';
    const clean = phone.replace(/\D/g, '');

    // 11 digits: (XX) 9XXXX-XXXX
    if (clean.length === 11) {
        return `(${clean.slice(0, 2)}) ${clean.slice(2, 7)}-${clean.slice(7)}`;
    }
    // 10 digits: (XX) XXXX-XXXX
    if (clean.length === 10) {
        return `(${clean.slice(0, 2)}) ${clean.slice(2, 6)}-${clean.slice(6)}`;
    }

    // International with 55
    if (clean.length === 13 && clean.startsWith('55')) {
        return `+55 (${clean.slice(2, 4)}) ${clean.slice(4, 9)}-${clean.slice(9)}`;
    }

    return phone;
}

/**
 * Phone mask on input typing
 *
 * @param {string} value
 * @returns {string}
 */
export function maskPhone(value) {
    if (!value) return '';
    let digits = value.replace(/\D/g, '');

    if (digits.length > 11) {
        digits = digits.slice(0, 11);
    }

    if (digits.length === 0) {
        return '';
    }
    if (digits.length <= 2) {
        return `(${digits}`;
    }
    if (digits.length <= 6) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
    }
    if (digits.length <= 10) {
        return `(${digits.slice(0, 2)}) ${digits.slice(2, 6)}-${digits.slice(6)}`;
    }
    return `(${digits.slice(0, 2)}) ${digits.slice(2, 7)}-${digits.slice(7, 11)}`;
}

/**
 * Currency formatter R$ 50,00
 *
 * @param {number|string|null} amount
 * @returns {string}
 */
export function formatCurrency(amount) {
    if (amount === null || amount === undefined || amount === '') return '';
    const num = Number(amount);
    if (isNaN(num)) return '';
    return num.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

/**
 * Parse wishlist which can be a JSON array of items or legacy raw string
 *
 * @param {string|Array|null} wishlist
 * @returns {Array<{title: string, url?: string, notes?: string}>}
 */
export function parseWishlist(wishlist) {
    if (!wishlist) return [];
    if (Array.isArray(wishlist)) return wishlist;

    if (typeof wishlist === 'string') {
        const trimmed = wishlist.trim();
        if (trimmed.startsWith('[') && trimmed.endsWith(']')) {
            try {
                const parsed = JSON.parse(trimmed);
                if (Array.isArray(parsed)) {
                    return parsed;
                }
            } catch (e) {
                // fall through to text split
            }
        }

        // If string has lines, split lines or return as single item
        const lines = trimmed.split('\n').filter(l => l.trim().length > 0);
        return lines.map(line => {
            // Check if line contains a URL
            const urlMatch = line.match(/(https?:\/\/[^\s]+)/g);
            let url = '';
            let title = line;

            if (urlMatch && urlMatch.length > 0) {
                url = urlMatch[0];
                title = line.replace(url, '').trim() || 'Link do presente';
            }

            return {
                title: title.replace(/^[-*•\d.)\s]+/, '').trim() || line,
                url: url,
                notes: '',
            };
        });
    }

    return [];
}
