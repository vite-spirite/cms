import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import AssignPermission from './Components/AssignPermission.vue';
import AssignRole from './Components/AssignRole.vue';

ExtensionRegistry.register('users.create.end', AssignRole);
ExtensionRegistry.register('users.create.end', AssignPermission);

ExtensionRegistry.register('users.edit.end', AssignRole);
ExtensionRegistry.register('users.edit.end', AssignPermission);
