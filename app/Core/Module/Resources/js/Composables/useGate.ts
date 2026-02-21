import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface PermissionProps {
    capabilities: string[];
    owner: boolean;
}

export function useGate() {
    const page = usePage();
    const userPermissions = computed<PermissionProps | undefined>(() => (page.props.permissions as PermissionProps) ?? undefined);

    function can(permission: string): boolean {
        if (!userPermissions.value) {
            return true;
        }

        return userPermissions.value.owner || userPermissions.value.capabilities.includes(permission);
    }

    function canAny(permissions: string[]): boolean {
        if (!userPermissions.value) {
            return true;
        }

        if (userPermissions.value.owner) {
            return true;
        }

        return permissions.some(can);
    }

    return { can, canAny };
}
