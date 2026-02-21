import LogoutButton from './Components/LogoutButton.vue';
import ExtensionRegistry from '@modules/Module/ExtensionRegistry';
import AssignRole from './Components/AssignRole.vue';

ExtensionRegistry.register('navigation.sidebar.footer', LogoutButton);

ExtensionRegistry.register('role.update.form.end', AssignRole);
ExtensionRegistry.register('role.create.form.end', AssignRole);
