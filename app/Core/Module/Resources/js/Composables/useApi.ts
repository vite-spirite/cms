export function useApi() {
    async function get<T = unknown>(path: string): Promise<T> {
        const response = await fetch(path, {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error(`Api error: ${response.status}`);
        }

        return response.json();
    }

    return { get };
}
