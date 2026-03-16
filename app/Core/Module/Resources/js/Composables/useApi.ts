function getCookie(name: string): string | undefined {
    const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[2]) : undefined;
}

export function useApi() {
    async function get<T = unknown>(path: string): Promise<T> {
        const xsrfToken = getCookie('XSRF-TOKEN');
        const response = await fetch(path, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
            },
        });

        if (!response.ok) {
            throw new Error(`Api error: ${response.status}`);
        }

        return response.json();
    }

    return { get };
}
